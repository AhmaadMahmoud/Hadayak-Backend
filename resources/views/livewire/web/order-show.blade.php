<div class="mx-auto max-w-3xl">
    <nav class="flex items-center gap-2 text-xs font-bold text-mocha/70">
        <a href="{{ route('web.orders') }}" wire:navigate class="hover:text-brand">طلباتي</a>
        <span>/</span>
        <span class="text-ink">{{ $order->number }}</span>
    </nav>

    {{-- Header --}}
    <div class="mt-5 flex items-center justify-between rounded-2xl border border-sand bg-white p-5">
        <div>
            <p class="text-lg font-extrabold text-ink">{{ $order->number }}</p>
            <p class="mt-1 text-xs text-mocha">{{ $order->created_at->translatedFormat('d M Y — h:i A') }}</p>
        </div>
        <span
            @class([
                'rounded-full px-4 py-1.5 text-sm font-bold',
                'bg-blush text-brand-dark' => in_array($order->status, ['pending', 'confirmed']),
                'bg-[#FFF8E1] text-[#B26A00]' => in_array($order->status, ['preparing', 'delivering']),
                'bg-[#E8F5E9] text-[#2E7D32]' => $order->status === 'delivered',
                'bg-sand text-mocha/60' => $order->status === 'cancelled',
            ])
        >{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span>
    </div>

    @if ($error)
        <p class="mt-4 rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $error }}</p>
    @endif

    {{-- Items --}}
    <div class="mt-5 overflow-hidden rounded-2xl border border-sand bg-white">
        @foreach ($order->items as $item)
            <div @class(['flex items-center justify-between gap-4 p-4', 'border-t border-sand' => ! $loop->first])>
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-blush">🎁</span>
                    <div>
                        <p class="text-sm font-extrabold text-ink">{{ $item->product_name }}</p>
                        <p class="text-xs text-mocha">الكمية: {{ $item->qty }}</p>
                    </div>
                </div>
                <span class="text-sm font-extrabold text-brand">{{ number_format($item->price * $item->qty) }} ج.م</span>
            </div>
        @endforeach
    </div>

    {{-- Delivery --}}
    <div class="mt-4 rounded-2xl border border-sand bg-white p-5">
        @if ($order->delivery_type === 'gift')
            <p class="mb-3 rounded-xl bg-blush px-4 py-2.5 text-sm font-bold text-brand">
                🎀 هدية لـ {{ $order->recipient_name }} — <span dir="ltr">{{ $order->recipient_phone }}</span>
            </p>
        @endif
        @if ($order->address)
            <p class="flex items-center gap-2 text-sm text-mocha">
                <span class="text-base">📍</span>
                {{ $order->address->area }} — {{ $order->address->street }}{{ $order->address->building ? ' — عمارة '.$order->address->building : '' }}
            </p>
        @endif
        @if ($order->card_message)
            <p class="mt-3 rounded-xl bg-cream p-4 text-sm leading-relaxed text-mocha">💌 {{ $order->card_message }}</p>
        @endif
    </div>

    {{-- Totals --}}
    <div class="mt-4 space-y-2.5 rounded-2xl border border-sand bg-white p-5 text-sm">
        <div class="flex justify-between text-mocha"><span>المنتجات</span><span>{{ number_format($order->items_total) }} ج.م</span></div>
        @if ($order->wrap_price > 0)
            <div class="flex justify-between text-mocha"><span>التغليف</span><span>{{ number_format($order->wrap_price) }} ج.م</span></div>
        @endif
        @if ($order->card_price > 0)
            <div class="flex justify-between text-mocha"><span>بطاقة المعايدة</span><span>{{ number_format($order->card_price) }} ج.م</span></div>
        @endif
        <div class="flex justify-between text-mocha"><span>التوصيل</span><span>{{ number_format($order->delivery_fee) }} ج.م</span></div>
        <div class="flex justify-between border-t border-sand pt-3 text-base font-extrabold text-ink">
            <span>الإجمالي</span><span class="text-brand">{{ number_format($order->total) }} ج.م</span>
        </div>
        <p class="pt-1 text-xs text-mocha/70">وسيلة الدفع: {{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p>
    </div>

    {{-- Cancel --}}
    @if ($order->status === 'pending')
        <button
            type="button"
            wire:click="confirmCancel"
            class="mt-5 w-full rounded-2xl border border-brand/20 bg-blush p-4 text-sm font-extrabold text-brand transition hover:bg-brand hover:text-white"
        >
            إلغاء الطلب
        </button>
    @endif

    {{-- Cancel confirmation --}}
    @if ($confirmingCancel)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="dismissCancel">
            <div class="w-full max-w-sm rounded-3xl bg-white p-7 text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-blush text-3xl">😢</span>
                <h3 class="mt-4 text-lg font-extrabold text-ink">متأكد إنك عايز تلغي الطلب؟</h3>
                <p class="mt-2 text-sm text-mocha">الطلب {{ $order->number }} هيتلغي وأي كميات محجوزة هترجع للمتجر.</p>
                <div class="mt-6 flex flex-col gap-3">
                    <button type="button" wire:click="cancelOrder" wire:loading.attr="disabled"
                        class="w-full rounded-full bg-brand py-3.5 text-sm font-extrabold text-white transition hover:bg-brand-dark disabled:opacity-60">
                        <span wire:loading.remove wire:target="cancelOrder">أيوه، الغي الطلب</span>
                        <span wire:loading wire:target="cancelOrder">جاري الإلغاء…</span>
                    </button>
                    <button type="button" wire:click="dismissCancel"
                        class="w-full rounded-full border border-sand py-3.5 text-sm font-extrabold text-ink transition hover:bg-cream">
                        لأ، كمّل الطلب
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
