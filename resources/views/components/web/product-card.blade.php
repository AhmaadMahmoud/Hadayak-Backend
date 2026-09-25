@props(['product'])

@php
    $img = $product->images->first()?->path;
    $img = $img ? \Illuminate\Support\Facades\Storage::disk('public')->url($img) : null;
    $inStock = $product->stock === null || $product->stock > 0;
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

    <div class="p-4">
        <a href="{{ route('web.product', $product) }}" wire:navigate class="block truncate text-base font-bold text-ink transition hover:text-brand">
            {{ $product->name }}
        </a>
        <div class="mt-2 flex items-center justify-between">
            <span class="text-lg font-extrabold text-brand">{{ number_format($product->price) }} <span class="text-xs font-bold text-mocha">ج.م</span></span>
            <button
                type="button"
                @if ($inStock) wire:click="addToCart({{ $product->id }})" @endif
                @disabled(! $inStock)
                class="flex size-9 items-center justify-center rounded-full bg-blush text-brand transition hover:bg-brand hover:text-white active:scale-95 disabled:opacity-40 disabled:hover:bg-blush disabled:hover:text-brand"
                aria-label="أضف {{ $product->name }} للسلة"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            </button>
        </div>
    </div>
</div>
