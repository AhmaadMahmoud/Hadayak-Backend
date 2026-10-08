<?php

namespace App\Services;

use App\Mail\OrderConfirmed;
use App\Models\CardDesign;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\WrapOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * منطق إنشاء الطلب الموحد — بيستخدمه الـ API (التطبيق) وصفحة الدفع في الموقع.
 *
 * $data: items[], address_id, delivery_type, recipient_name?, recipient_phone?,
 *        wrap_option_id?, card_design_id?, card_message?, payment_method
 *
 * @throws ValidationException
 */
class PlaceOrder
{
    public static function handle(User $user, array $data): Order
    {
        // العنوان لازم يكون بتاع نفس المستخدم
        $address = $user->addresses()->whereKey($data['address_id'])->first();

        if (! $address) {
            throw ValidationException::withMessages(['address_id' => 'العنوان غير صحيح']);
        }

        // الدفع عند الاستلام حسب الإعدادات
        if ($data['payment_method'] === 'cod' && ! (bool) (int) Setting::get('cod_enabled', 0)) {
            throw ValidationException::withMessages(['payment_method' => 'الدفع عند الاستلام غير متاح لهذا الطلب']);
        }

        $order = DB::transaction(function () use ($data, $user, $address) {
            $wrap = isset($data['wrap_option_id']) ? WrapOption::find($data['wrap_option_id']) : null;
            $card = isset($data['card_design_id']) ? CardDesign::find($data['card_design_id']) : null;

            $order = $user->orders()->create([
                'address_id' => $data['address_id'],
                'delivery_type' => $data['delivery_type'],
                'recipient_name' => $data['recipient_name'] ?? null,
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'wrap_option_id' => $wrap?->id,
                'wrap_price' => $wrap?->price ?? 0,
                'card_design_id' => $card?->id,
                'card_price' => $card?->price ?? 0,
                'card_message' => $data['card_message'] ?? null,
                'payment_method' => $data['payment_method'],
                'delivery_fee' => $address->shippingFee(),
            ]);

            foreach ($data['items'] as $item) {
                // قفل الصف عشان اتنين ميطلبوش آخر قطعة في نفس اللحظة
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();

                // التحقق من المخزون (null = كمية غير محدودة)
                if ($product->stock !== null && $item['qty'] > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => $product->stock > 0
                            ? "المتاح من {$product->name} هو {$product->stock} فقط"
                            : "{$product->name} نفدت كميته للأسف",
                    ]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price, // snapshot وقت الطلب
                    'qty' => $item['qty'],
                ]);

                // خصم الكمية من المخزون
                if ($product->stock !== null) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            $order->load('items');

            // شحن مجاني لو إجمالي المنتجات وصل للحد المحدد في الإعدادات
            $freeAt = (float) Setting::get('free_shipping_threshold', 0);

            if ($freeAt > 0 && $order->items->sum(fn ($i) => $i->price * $i->qty) >= $freeAt) {
                $order->delivery_fee = 0;
            }

            $order->recalculateTotals();

            return $order;
        });

        // إيميل تأكيد الطلب — فشل الإرسال عمره ما يكسر الطلب نفسه
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new OrderConfirmed($order));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $order;
    }
}
