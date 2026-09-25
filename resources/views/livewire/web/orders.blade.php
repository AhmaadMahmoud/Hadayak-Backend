<div class="mx-auto max-w-3xl">
    <h1 class="text-3xl font-extrabold text-ink">طلباتي</h1>

    @if (session('order_placed'))
        <div class="mt-5 flex items-center gap-3 rounded-2xl border border-[#2E7D32]/20 bg-[#E8F5E9] p-4">
            <span class="text-2xl">🎉</span>
            <div>
                <p class="text-sm font-extrabold text-[#2E7D32]">طلبك وصلنا!</p>
                <p class="text-xs text-[#2E7D32]/80">رقم الطلب: {{ session('order_placed') }}</p>
            </div>
        </div>
    @endif

    @if ($orders->isEmpty())
        <div class="mt-16 flex flex-col items-center text-center">
            <span class="flex size-24 items-center justify-center rounded-full bg-blush text-5xl">📦</span>
            <p class="mt-5 text-xl font-extrabold text-ink">لسه معملتش أي طلب</p>
            <a href="{{ route('web.products') }}" wire:navigate class="mt-6 rounded-full bg-brand px-10 py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark">
                ابدأ التسوق
            </a>
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($orders as $order)
                <a
                    href="{{ route('web.order', $order) }}"
                    wire:navigate
                    wire:key="order-{{ $order->id }}"
                    class="block rounded-2xl border border-sand bg-white p-5 transition hover:-translate-y-0.5 hover:border-brand/30 hover:shadow-md"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-extrabold text-ink">{{ $order->number }}</span>
                        <span
                            @class([
                                'rounded-full px-3 py-1 text-xs font-bold',
                                'bg-blush text-brand-dark' => in_array($order->status, ['pending', 'confirmed']),
                                'bg-[#FFF8E1] text-[#B26A00]' => in_array($order->status, ['preparing', 'delivering']),
                                'bg-[#E8F5E9] text-[#2E7D32]' => $order->status === 'delivered',
                                'bg-sand text-mocha/60' => $order->status === 'cancelled',
                            ])
                        >{{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-mocha">
                        <span>{{ $order->items->sum('qty') }} منتج · {{ $order->created_at->translatedFormat('d M Y') }}</span>
                        <span class="font-extrabold text-brand">{{ number_format($order->total) }} ج.م</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</div>
