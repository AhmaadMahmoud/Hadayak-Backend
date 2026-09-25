<div>
    <h1 class="text-3xl font-extrabold text-ink">سلة الهدايا 🎁</h1>

    @if (empty($items))
        <div class="mt-16 flex flex-col items-center text-center">
            <span class="flex size-24 items-center justify-center rounded-full bg-blush text-5xl">🎁</span>
            <p class="mt-5 text-xl font-extrabold text-ink">سلتك فاضية لسه</p>
            <p class="mt-1 text-sm text-mocha">املاها هدايا تفرّح اللي بتحبهم</p>
            <a href="{{ route('web.products') }}" wire:navigate class="mt-6 rounded-full bg-brand px-10 py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark">
                تسوق دلوقتي
            </a>
        </div>
    @else
        <div class="mt-6 grid gap-8 lg:grid-cols-3">
            {{-- Items + extras --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Items --}}
                <div class="overflow-hidden rounded-2xl border border-sand bg-white">
                    @foreach ($items as $item)
                        <div @class(['flex items-center gap-4 p-4', 'border-t border-sand' => ! $loop->first]) wire:key="item-{{ $item['id'] }}">
                            <span class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-sand">
                                @if ($item['image'])
                                    <img src="{{ $item['image'] }}" alt="" class="size-full object-cover">
                                @else
                                    <span class="text-2xl">🎁</span>
                                @endif
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-extrabold text-ink">{{ $item['name'] }}</p>
                                <p class="mt-0.5 text-sm font-bold text-brand">{{ number_format($item['price']) }} ج.م</p>
                            </div>

                            <div class="flex items-center gap-1 rounded-full border border-sand p-0.5">
                                <button type="button" wire:click="increment({{ $item['id'] }})" class="flex size-8 items-center justify-center rounded-full text-brand transition hover:bg-blush" aria-label="زيادة">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                                <span class="w-7 text-center text-sm font-extrabold">{{ $item['qty'] }}</span>
                                <button type="button" wire:click="decrement({{ $item['id'] }})" class="flex size-8 items-center justify-center rounded-full text-brand transition hover:bg-blush" aria-label="تقليل">
                                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                                </button>
                            </div>

                            <button type="button" wire:click="remove({{ $item['id'] }})" class="flex size-9 items-center justify-center rounded-full text-mocha/40 transition hover:bg-blush hover:text-brand" aria-label="حذف">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Wrap options --}}
                @if ($wraps->isNotEmpty())
                    <div class="rounded-2xl border border-sand bg-white p-5">
                        <h2 class="text-lg font-extrabold text-ink">🎀 غلف هديتك <span class="text-xs font-bold text-mocha/60">(اختياري — دوس تاني للإلغاء)</span></h2>
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($wraps as $wrap)
                                @php $wImg = $wrap->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($wrap->image) : null; @endphp
                                <button
                                    type="button"
                                    wire:click="selectWrap({{ $wrap->id }})"
                                    wire:key="wrap-{{ $wrap->id }}"
                                    @class([
                                        'rounded-2xl border-2 p-3 text-center transition',
                                        'border-brand bg-blush' => $wrapId === $wrap->id,
                                        'border-sand bg-white hover:border-brand/40' => $wrapId !== $wrap->id,
                                    ])
                                >
                                    <span class="mx-auto flex size-14 items-center justify-center overflow-hidden rounded-xl bg-sand">
                                        @if ($wImg)<img src="{{ $wImg }}" alt="" class="size-full object-cover">@else 🎀 @endif
                                    </span>
                                    <p class="mt-2 truncate text-xs font-extrabold text-ink">{{ $wrap->name }}</p>
                                    <p class="text-xs font-bold text-brand">{{ number_format($wrap->price) }} ج.م</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Card options --}}
                @if ($cards->isNotEmpty())
                    <div class="rounded-2xl border border-sand bg-white p-5">
                        <h2 class="text-lg font-extrabold text-ink">💌 بطاقة معايدة <span class="text-xs font-bold text-mocha/60">(اختياري)</span></h2>
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach ($cards as $card)
                                @php $cImg = $card->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($card->image) : null; @endphp
                                <button
                                    type="button"
                                    wire:click="selectCard({{ $card->id }})"
                                    wire:key="card-{{ $card->id }}"
                                    @class([
                                        'rounded-2xl border-2 p-3 text-center transition',
                                        'border-brand bg-blush' => $cardId === $card->id,
                                        'border-sand bg-white hover:border-brand/40' => $cardId !== $card->id,
                                    ])
                                >
                                    <span class="mx-auto flex size-14 items-center justify-center overflow-hidden rounded-xl bg-sand">
                                        @if ($cImg)<img src="{{ $cImg }}" alt="" class="size-full object-cover">@else 💌 @endif
                                    </span>
                                    <p class="mt-2 truncate text-xs font-extrabold text-ink">{{ $card->name }}</p>
                                    <p class="text-xs font-bold text-brand">{{ number_format($card->price) }} ج.م</p>
                                </button>
                            @endforeach
                        </div>

                        @if ($cardId)
                            <textarea
                                wire:model.live.debounce.500ms="cardMessage"
                                rows="3"
                                maxlength="500"
                                placeholder="اكتب رسالتك اللي هتتكتب في البطاقة… ✍️"
                                class="mt-4 w-full rounded-2xl border border-sand bg-cream p-4 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none"
                            ></textarea>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Summary --}}
            <div class="h-fit rounded-2xl border border-sand bg-white p-5 lg:sticky lg:top-24">
                <h2 class="text-lg font-extrabold text-ink">ملخص الطلب</h2>
                <div class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between text-mocha"><span>المنتجات</span><span>{{ number_format($itemsTotal) }} ج.م</span></div>
                    @if ($wrapPrice > 0)
                        <div class="flex justify-between text-mocha"><span>التغليف</span><span>{{ number_format($wrapPrice) }} ج.م</span></div>
                    @endif
                    @if ($cardPrice > 0)
                        <div class="flex justify-between text-mocha"><span>بطاقة المعايدة</span><span>{{ number_format($cardPrice) }} ج.م</span></div>
                    @endif
                    <div class="flex justify-between text-mocha"><span>التوصيل</span><span>{{ number_format($deliveryFee) }} ج.م</span></div>
                    <div class="flex justify-between border-t border-sand pt-3 text-base font-extrabold text-ink">
                        <span>الإجمالي</span>
                        <span class="text-brand">{{ number_format($itemsTotal + $wrapPrice + $cardPrice + $deliveryFee) }} ج.م</span>
                    </div>
                </div>

                <a
                    href="{{ route('web.checkout') }}"
                    wire:navigate
                    class="mt-5 block w-full rounded-full bg-brand py-3.5 text-center text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98]"
                >
                    إتمام الطلب
                </a>
                <a href="{{ route('web.products') }}" wire:navigate class="mt-3 block text-center text-sm font-bold text-mocha hover:text-brand">
                    كمّل تسوق
                </a>
            </div>
        </div>
    @endif
</div>
