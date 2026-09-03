<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardDesign;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WrapOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /** إنشاء طلب جديد من شاشة الدفع */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'address_id' => ['required', 'exists:addresses,id'],
            'delivery_type' => ['required', 'in:me,gift'],
            'recipient_name' => ['required_if:delivery_type,gift', 'nullable', 'string', 'max:255'],
            'recipient_phone' => ['required_if:delivery_type,gift', 'nullable', 'string', 'max:20'],
            'wrap_option_id' => ['nullable', 'exists:wrap_options,id'],
            'card_design_id' => ['nullable', 'exists:card_designs,id'],
            'card_message' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:card,vodafone_cash,instapay,cod'],
        ]);

        $user = $request->user();

        // العنوان لازم يكون بتاع نفس المستخدم
        abort_unless(
            $user->addresses()->whereKey($data['address_id'])->exists(),
            403, 'العنوان غير صحيح',
        );

        // الدفع عند الاستلام حسب الإعدادات
        if ($data['payment_method'] === 'cod' && ! (bool) (int) Setting::get('cod_enabled', 0)) {
            throw ValidationException::withMessages(['payment_method' => 'الدفع عند الاستلام غير متاح لهذا الطلب']);
        }

        $order = DB::transaction(function () use ($data, $user) {
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
                'delivery_fee' => (float) Setting::get('delivery_fee', 50),
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price, // snapshot وقت الطلب
                    'qty' => $item['qty'],
                ]);
            }

            $order->load('items');
            $order->recalculateTotals();

            return $order;
        });

        return response()->json(['order' => $this->payload($order)], 201);
    }

    /** طلبات المستخدم */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'orders' => $request->user()->orders()
                ->with('items')
                ->latest()
                ->get()
                ->map(fn ($o) => $this->payload($o)),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return response()->json(['order' => $this->payload($order->load('items'))]);
    }

    private function payload(Order $o): array
    {
        return [
            'id' => $o->id,
            'number' => $o->number,
            'status' => $o->status,
            'status_label' => Order::STATUSES[$o->status] ?? $o->status,
            'delivery_type' => $o->delivery_type,
            'payment_method' => $o->payment_method,
            'payment_status' => $o->payment_status,
            'items' => $o->items->map(fn ($i) => [
                'product_id' => $i->product_id,
                'name' => $i->product_name,
                'price' => (float) $i->price,
                'qty' => $i->qty,
            ]),
            'wrap_price' => (float) $o->wrap_price,
            'card_price' => (float) $o->card_price,
            'card_message' => $o->card_message,
            'items_total' => (float) $o->items_total,
            'delivery_fee' => (float) $o->delivery_fee,
            'total' => (float) $o->total,
            'created_at' => $o->created_at->toIso8601String(),
        ];
    }
}
