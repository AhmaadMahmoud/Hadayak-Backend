<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * التكامل مع Paymob (Intention API + Unified Checkout).
 * المفاتيح من config/services.php → .env، والتفعيل من إعدادات المتجر.
 */
class Paymob
{
    public static function enabled(): bool
    {
        return (bool) (int) Setting::get('card_payment_enabled', 0)
            && config('services.paymob.secret_key')
            && config('services.paymob.public_key')
            && config('services.paymob.integration_id');
    }

    /**
     * ينشئ Intention عند Paymob ويرجع لينك صفحة الدفع الموحدة.
     */
    public static function checkoutUrl(Order $order): string
    {
        $order->loadMissing(['items', 'address', 'user']);

        $items = $order->items->map(fn ($i) => [
            'name' => mb_substr($i->product_name, 0, 50),
            'amount' => (int) round($i->price * 100),
            'description' => mb_substr($i->product_name, 0, 100),
            'quantity' => (int) $i->qty,
        ])->values()->all();

        // فرق التغليف والكارت والشحن كبند واحد عشان مجموع البنود يطابق الإجمالي
        $extras = (int) round($order->total * 100) - collect($items)->sum(fn ($i) => $i['amount'] * $i['quantity']);

        if ($extras > 0) {
            $items[] = ['name' => 'التغليف والشحن', 'amount' => $extras, 'description' => 'خدمات إضافية', 'quantity' => 1];
        }

        $phone = $order->user->phone ?: ($order->address?->phone ?? '+20000000000');

        $response = Http::withHeaders(['Authorization' => 'Token ' . config('services.paymob.secret_key')])
            ->acceptJson()
            ->post('https://accept.paymob.com/v1/intention/', [
                'amount' => (int) round($order->total * 100),
                'currency' => 'EGP',
                'payment_methods' => [(int) config('services.paymob.integration_id')],
                'items' => $items,
                'billing_data' => [
                    'first_name' => mb_substr($order->user->name ?: 'عميل', 0, 30),
                    'last_name' => 'هداياك',
                    'phone_number' => $phone,
                    'email' => $order->user->email ?: 'guest@hdayak.com',
                    'country' => 'EG',
                    'city' => $order->address?->area ?: 'Cairo',
                    'street' => mb_substr($order->address?->street ?: 'N/A', 0, 50),
                    'building' => $order->address?->building ?: 'NA',
                    'floor' => $order->address?->floor ?: 'NA',
                    'apartment' => $order->address?->apartment ?: 'NA',
                    'state' => 'NA',
                ],
                'special_reference' => $order->number,
                'notification_url' => route('paymob.webhook'),
                'redirection_url' => route('paymob.return'),
            ])
            ->throw()
            ->json();

        $order->forceFill(['payment_reference' => $response['id'] ?? null])->save();

        return 'https://accept.paymob.com/unifiedcheckout/?publicKey='
            . config('services.paymob.public_key')
            . '&clientSecret=' . $response['client_secret'];
    }

    /**
     * التحقق من توقيع HMAC بتاع الـ webhook (transaction processed callback).
     */
    public static function validWebhookHmac(array $payload, ?string $hmac): bool
    {
        if (! $hmac || ! config('services.paymob.hmac_secret')) {
            return false;
        }

        $obj = $payload['obj'] ?? [];

        $keys = [
            'amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction',
            'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded',
            'is_standalone_payment', 'is_voided', 'order.id', 'owner', 'pending',
            'source_data.pan', 'source_data.sub_type', 'source_data.type', 'success',
        ];

        $data = '';

        foreach ($keys as $key) {
            $value = data_get($obj, $key);

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $data .= $value;
        }

        return hash_equals(hash_hmac('sha512', $data, config('services.paymob.hmac_secret')), $hmac);
    }
}
