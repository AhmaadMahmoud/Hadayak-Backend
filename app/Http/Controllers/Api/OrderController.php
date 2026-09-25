<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmed;
use App\Models\CardDesign;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WrapOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        $order = \App\Services\PlaceOrder::handle($request->user(), $data);

        return response()->json(['order' => $this->payload($order)], 201);
    }

    /** طلبات المستخدم */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'orders' => $request->user()->orders()
                ->with('items', 'address')
                ->latest()
                ->get()
                ->map(fn ($o) => $this->payload($o)),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return response()->json(['order' => $this->payload($order->load('items', 'address'))]);
    }

    /** إلغاء طلب — بس طالما لسه جديد */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'الطلب دخل مرحلة التجهيز ومينفعش يتلغي — كلمنا وإحنا نظبطك',
            ]);
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // رجّع الكميات للمخزون
            foreach ($order->items as $item) {
                Product::whereKey($item->product_id)
                    ->whereNotNull('stock')
                    ->increment('stock', $item->qty);
            }
        });

        return response()->json(['order' => $this->payload($order->fresh()->load('items'))]);
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
            'recipient_name' => $o->recipient_name,
            'recipient_phone' => $o->recipient_phone,
            'address' => $o->address ? [
                'label' => $o->address->label,
                'area' => $o->address->area,
                'street' => $o->address->street,
                'building' => $o->address->building,
            ] : null,
            'can_cancel' => $o->status === 'pending',
            'created_at' => $o->created_at->toIso8601String(),
        ];
    }
}
