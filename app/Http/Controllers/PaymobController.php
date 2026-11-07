<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Services\Paymob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymobController extends Controller
{
    /**
     * Webhook نتيجة الدفع (transaction processed) — ده مصدر الحقيقة الوحيد.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (! Paymob::validWebhookHmac($payload, $request->query('hmac'))) {
            return response()->json(['message' => 'invalid hmac'], 403);
        }

        $obj = $payload['obj'] ?? [];

        $reference = data_get($obj, 'payment_key_claims.extra.special_reference')
            ?? data_get($obj, 'order.merchant_order_id');

        $order = Order::where('number', $reference)->first();

        if (! $order) {
            return response()->json(['message' => 'order not found'], 404);
        }

        // تسجيل المعاملة مرة واحدة بس (الـ webhook ممكن يتبعت أكتر من مرة)
        $order->transactions()->firstOrCreate(
            ['provider' => 'paymob', 'transaction_id' => (string) data_get($obj, 'id')],
            [
                'amount_cents' => (int) data_get($obj, 'amount_cents', 0),
                'success' => (bool) data_get($obj, 'success'),
                'is_refund' => (bool) data_get($obj, 'is_refunded'),
                'payload' => $obj,
            ],
        );

        if (data_get($obj, 'is_refunded')) {
            $order->update(['payment_status' => 'refunded']);
        } elseif (data_get($obj, 'success')) {
            if ($order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);

                if ($order->user?->email) {
                    try {
                        Mail::to($order->user->email)->send(new OrderConfirmed($order));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        } elseif ($order->payment_status !== 'paid') {
            $order->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * رجوع العميل من صفحة الدفع — عرض فقط، الاعتماد الفعلي على الـ webhook.
     */
    public function return(Request $request)
    {
        $success = filter_var($request->query('success'), FILTER_VALIDATE_BOOLEAN);
        $order = Order::where('number', $request->query('merchant_order_id'))->first();

        session()->flash('payment_result', $success ? 'success' : 'failed');

        return $order
            ? redirect()->route('web.order', $order)
            : redirect()->route('web.orders');
    }
}
