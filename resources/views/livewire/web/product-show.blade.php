<div>
    @php
        $images = $product->images->map(fn ($i) => \Illuminate\Support\Facades\Storage::disk('public')->url($i->path))->values();
        $inStock = $product->stock === null || $product->stock > 0;
    @endphp

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-bold text-mocha/70">
        <a href="{{ route('web.home') }}" wire:navigate class="hover:text-brand">الرئيسية</a>
        <span>/</span>
        <a href="{{ route('web.products', ['category' => $product->category_id]) }}" wire:navigate class="hover:text-brand">{{ $product->category?->name ?? 'المنتجات' }}</a>
        <span>/</span>
        <span class="text-ink">{{ $product->name }}</span>
    </nav>

    <div class="mt-6 grid gap-8 lg:grid-cols-2">
        {{-- Gallery --}}
        <div
            x-data="{ active: 0, images: {{ $images->toJson() }} }"
            class="space-y-3"
        >
            <div class="relative aspect-square overflow-hidden rounded-3xl border border-sand bg-white">
                <template x-if="images.length">
                    <img :src="images[active]" alt="{{ $product->name }}" class="size-full object-cover">
                </template>
                <template x-if="! images.length">
                    <span class="flex size-full items-center justify-center text-7xl">🎁</span>
                </template>

                @unless ($inStock)
                    <span class="absolute inset-x-0 top-0 bg-brand/90 py-2 text-center text-sm font-bold text-white">نفدت الكمية</span>
                @endunless
            </div>

            <template x-if="images.length > 1">
                <div class="flex gap-3 overflow-x-auto pb-1">
                    <template x-for="(img, i) in images" :key="i">
                        <button
                            type="button"
                            x-on:click="active = i"
                            class="size-20 shrink-0 overflow-hidden rounded-xl border-2 transition"
                            :class="active === i ? 'border-brand' : 'border-sand opacity-70 hover:opacity-100'"
                        >
                            <img :src="img" alt="" class="size-full object-cover">
                        </button>
                    </template>
                </div>
            </template>
        </div>

        {{-- Info --}}
        <div>
            <h1 class="text-3xl font-extrabold text-ink">{{ $product->name }}</h1>
            <p class="mt-3 text-3xl font-extrabold text-brand">
                {{ number_format($product->price) }} <span class="text-base font-bold text-mocha">ج.م</span>
            </p>

            @if ($product->description)
                <p class="mt-5 max-w-prose text-base leading-relaxed text-mocha">{{ $product->description }}</p>
            @endif

            @if ($inStock)
                <div class="mt-7 flex flex-wrap items-center gap-4">
                    {{-- Qty --}}
                    <div class="flex items-center gap-1 rounded-full border border-sand bg-white p-1">
                        <button type="button" wire:click="incrementQty" class="flex size-9 items-center justify-center rounded-full text-brand transition hover:bg-blush" aria-label="زيادة">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <span class="w-8 text-center text-base font-extrabold text-ink">{{ $qty }}</span>
                        <button type="button" wire:click="decrementQty" class="flex size-9 items-center justify-center rounded-full text-brand transition hover:bg-blush" aria-label="تقليل">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                        </button>
                    </div>

                    <button
                        type="button"
                        wire:click="addToCart"
                        wire:loading.attr="disabled"
                        class="flex items-center gap-2 rounded-full bg-brand px-8 py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98] disabled:opacity-60"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7M7.5 8a2.5 2.5 0 0 1 0-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 0 1 0 5"/></svg>
                        <span wire:loading.remove wire:target="addToCart">أضف لسلة الهدايا</span>
                        <span wire:loading wire:target="addToCart">ثواني…</span>
                    </button>
                </div>
            @else
                <div class="mt-7 rounded-2xl bg-sand px-6 py-4 text-center text-base font-bold text-mocha">
                    نفدت الكمية 😔 — رجعلنا قريب أو شوف هدايا شبهها تحت
                </div>
            @endif

            {{-- Trust points --}}
            <div class="mt-8 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-2xl bg-blush p-3"><span class="text-xl">🎀</span><p class="mt-1 text-[11px] font-bold text-ink">تغليف هدايا</p></div>
                <div class="rounded-2xl bg-blush p-3"><span class="text-xl">💌</span><p class="mt-1 text-[11px] font-bold text-ink">بطاقة معايدة</p></div>
                <div class="rounded-2xl bg-blush p-3"><span class="text-xl">🚚</span><p class="mt-1 text-[11px] font-bold text-ink">توصيل للباب</p></div>
            </div>
        </div>
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <section class="mt-14">
            <h2 class="text-2xl font-extrabold text-ink">هدايا شبهها</h2>
            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($related as $rel)
                    <x-web.product-card :product="$rel" wire:key="rel-{{ $rel->id }}" />
                @endforeach
            </div>
        </section>
    @endif
</div>
