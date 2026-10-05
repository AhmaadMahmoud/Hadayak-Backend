@props(['product'])

@php
    $img = $product->images->first()?->path;
    $img = $img ? \Illuminate\Support\Facades\Storage::disk('public')->url($img) : null;
    $inStock = $product->stock === null || $product->stock > 0;
    $cartQty = (int) (\App\Services\WebCart::items()[$product->id]['qty'] ?? 0);
    $atMax = $cartQty >= 99 || ($product->stock !== null && $cartQty >= $product->stock);
@endphp

<div {{ $attributes->merge(['class' => 'group overflow-hidden rounded-2xl border border-sand bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg']) }}>
    <a href="{{ route('web.product', $product) }}" wire:navigate class="relative block aspect-square overflow-hidden bg-sand">
        @if ($img)
            <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <span class="flex size-full items-center justify-center text-5xl">🎁</span>
        @endif

        @unless ($inStock)
            <span class="absolute inset-x-0 top-0 bg-brand/90 py-1.5 text-center text-xs font-bold text-white">نفدت الكمية</span>
        @endunless
    </a>

    <div class="p-3 sm:p-4">
        <a href="{{ route('web.product', $product) }}" wire:navigate class="block truncate text-sm font-bold sm:text-base text-ink transition hover:text-brand">
            {{ $product->name }}
        </a>
        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
            <span class="text-base font-extrabold text-brand sm:text-lg">{{ number_format($product->price) }} <span class="text-xs font-bold text-mocha">ج.م</span></span>

            @if ($cartQty > 0)
                {{-- عداد الكمية بعد الإضافة --}}
                <div
                    wire:key="stepper-{{ $product->id }}"
                    class="stepper-in flex h-10 w-full items-center justify-between rounded-full lg:h-9 lg:w-auto lg:justify-start bg-brand p-0.5 text-white shadow-md shadow-brand/25"
                    role="group"
                    aria-label="كمية {{ $product->name }} في السلة"
                >
                    <button
                        type="button"
                        wire:click="cartIncrement({{ $product->id }})"
                        wire:loading.attr="disabled"
                        wire:target="cartIncrement({{ $product->id }}), cartDecrement({{ $product->id }})"
                        @disabled($atMax)
                        class="flex size-9 shrink-0 touch-manipulation items-center justify-center rounded-full transition hover:bg-white/20 lg:size-8 active:scale-90 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent"
                        aria-label="زيادة"
                        @if ($atMax) title="دي آخر كمية متاحة" @endif
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </button>

                    <span
                        wire:key="qty-{{ $product->id }}-{{ $cartQty }}"
                        wire:loading.class="opacity-50"
                        wire:target="cartIncrement({{ $product->id }}), cartDecrement({{ $product->id }})"
                        class="qty-bump min-w-7 flex-1 select-none text-center text-base lg:flex-none lg:text-sm font-extrabold tabular-nums transition-opacity"
                        aria-live="polite"
                    >{{ $cartQty }}</span>

                    <button
                        type="button"
                        wire:click="cartDecrement({{ $product->id }})"
                        wire:loading.attr="disabled"
                        wire:target="cartIncrement({{ $product->id }}), cartDecrement({{ $product->id }})"
                        class="flex size-9 shrink-0 touch-manipulation items-center justify-center rounded-full transition hover:bg-white/20 lg:size-8 active:scale-90"
                        aria-label="{{ $cartQty === 1 ? 'شيل من السلة' : 'تقليل' }}"
                    >
                        @if ($cartQty === 1)
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6M10 11v6M14 11v6"/></svg>
                        @else
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                        @endif
                    </button>
                </div>
            @else
                <button
                    type="button"
                    wire:key="add-{{ $product->id }}"
                    @if ($inStock) wire:click="addToCart({{ $product->id }})" @endif
                    wire:loading.attr="disabled"
                    wire:target="addToCart({{ $product->id }})"
                    @disabled(! $inStock)
                    class="flex size-9 shrink-0 touch-manipulation items-center justify-center rounded-full bg-blush text-brand transition hover:bg-brand hover:text-white active:scale-95 disabled:opacity-40 disabled:hover:bg-blush disabled:hover:text-brand"
                    aria-label="أضف {{ $product->name }} للسلة"
                >
                    <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    <svg wire:loading wire:target="addToCart({{ $product->id }})" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 3a9 9 0 1 0 9 9"/></svg>
                </button>
            @endif
        </div>
    </div>
</div>
