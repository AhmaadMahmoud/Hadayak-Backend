<div>
    <h1 class="sr-only">هداياك — متجر هدايا وتغليف وتوصيل لكل مصر</h1>

    @if ($banners->isNotEmpty())
        {{-- Banners (بيتحكم فيها من الداشبورد) --}}
        <section
            x-data="{ active: 0, count: {{ $banners->count() }} }"
            x-init="count > 1 && setInterval(() => active = (active + 1) % count, 5000)"
            class="relative overflow-hidden rounded-3xl shadow-lg"
        >
            <div class="relative aspect-[16/9] sm:aspect-[5/2]">
                @foreach ($banners as $banner)
                    @php
                        $bImg = \Illuminate\Support\Facades\Storage::disk('public')->url($banner->image);
                        $bMob = $banner->mobile_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($banner->mobile_image) : null;
                    @endphp
                    <a
                        @if ($banner->link) href="{{ $banner->link }}" @else href="{{ route('web.products') }}" @endif
                        x-show="active === {{ $loop->index }}"
                        x-cloak
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 block"
                        wire:key="banner-{{ $banner->id }}"
                    >
                        <img src="{{ $bImg }}" alt="{{ $banner->name }}" @if(! $loop->first) loading="lazy" @endif class="{{ $bMob ? 'hidden sm:block' : '' }} size-full object-cover">
                        @if ($bMob)
                            <img src="{{ $bMob }}" alt="{{ $banner->name }}" @if(! $loop->first) loading="lazy" @endif class="size-full object-cover sm:hidden">
                        @endif
                    </a>
                @endforeach
            </div>

            @if ($banners->count() > 1)
                <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2" dir="ltr">
                    @foreach ($banners as $banner)
                        <button
                            type="button"
                            x-on:click="active = {{ $loop->index }}"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="active === {{ $loop->index }} ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
                            aria-label="بانر {{ $loop->iteration }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-bl from-brand to-brand-deep text-white">
            <img src="{{ asset('images/web/gifts-bg.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover opacity-15">
            <div class="relative grid grid-cols-1 items-center gap-8 px-6 py-14 sm:px-12 md:grid-cols-2 md:py-20">
                <div>
                    <p class="text-3xl font-extrabold leading-tight sm:text-5xl">فرحتك… هديتنا 🎁</p>
                    <p class="mt-4 max-w-md text-base leading-relaxed text-white/85 sm:text-lg">
                        اختار الهدية، اكتب كلماتك على بطاقة المعايدة، واحنا نغلفها ونوصلها لحد باب اللي بتحبهم — في أي مكان في مصر.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('web.products') }}" wire:navigate class="rounded-full bg-accent px-8 py-3.5 text-base font-extrabold text-brand-dark shadow-lg transition hover:brightness-105 active:scale-[0.98]">
                            تسوق دلوقتي
                        </a>
                        <a href="{{ route('web.about') }}" wire:navigate class="rounded-full border-2 border-white/60 px-8 py-3.5 text-base font-bold text-white transition hover:bg-white/10">
                            اعرف عننا
                        </a>
                    </div>
                </div>
                <div class="hidden justify-center md:flex">
                    <img src="{{ asset('images/web/hero-banner.jpg') }}" alt="هدايا هداياك" class="max-h-72 w-full rounded-2xl object-cover shadow-2xl">
                </div>
            </div>
        </section>
    @endif

    {{-- Highlights strip --}}
    <section class="mt-8 grid grid-cols-3 gap-2 sm:gap-4">
        <div class="flex flex-col items-center gap-1.5 rounded-2xl border border-sand bg-white p-2.5 text-center sm:flex-row sm:gap-3 sm:p-4 sm:text-right">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-blush text-xl sm:size-11 sm:text-2xl">🎀</span>
            <div><p class="text-xs font-extrabold text-ink sm:text-sm">تغليف مميز</p><p class="hidden text-xs text-mocha sm:block">هديتك تتقدم بشكل يليق بيها</p></div>
        </div>
        <div class="flex flex-col items-center gap-1.5 rounded-2xl border border-sand bg-white p-2.5 text-center sm:flex-row sm:gap-3 sm:p-4 sm:text-right">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-blush text-xl sm:size-11 sm:text-2xl">🚚</span>
            <div><p class="text-xs font-extrabold text-ink sm:text-sm">توصيل سريع</p><p class="hidden text-xs text-mocha sm:block">لحد باب البيت في كل مصر</p></div>
        </div>
        <div class="flex flex-col items-center gap-1.5 rounded-2xl border border-sand bg-white p-2.5 text-center sm:flex-row sm:gap-3 sm:p-4 sm:text-right">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-blush text-xl sm:size-11 sm:text-2xl">💌</span>
            <div><p class="text-xs font-extrabold text-ink sm:text-sm">بطاقة بكلماتك</p><p class="hidden text-xs text-mocha sm:block">رسالتك تتكتب وتوصل مع الهدية</p></div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold text-ink">الأقسام</h2>
                <a href="{{ route('web.products') }}" wire:navigate class="text-sm font-bold text-brand hover:underline">كل المنتجات ←</a>
            </div>
            <div
                x-data="{
                    atStart: true,
                    atEnd: false,
                    update() {
                        const el = this.$refs.track;
                        {{-- في RTL الـ scrollLeft بيبقى سالب --}}
                        const x = Math.abs(el.scrollLeft);
                        this.atStart = x <= 2;
                        this.atEnd = x + el.clientWidth >= el.scrollWidth - 2;
                    },
                    scroll(dir) {
                        const el = this.$refs.track;
                        el.scrollBy({ left: -dir * el.clientWidth * 0.8, behavior: 'smooth' });
                    },
                }"
                x-init="$nextTick(() => update())"
                x-on:resize.window.debounce.150ms="update()"
                class="relative mt-3"
            >
                <div
                    x-ref="track"
                    x-on:scroll.debounce.50ms="update()"
                    class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth py-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                @foreach ($categories as $category)
                    @php
                        $catImg = $category->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($category->image) : null;
                    @endphp
                    <a
                        href="{{ route('web.products', ['category' => $category->id]) }}"
                        wire:navigate
                        class="group flex w-28 shrink-0 snap-start flex-col items-center gap-2 rounded-2xl border border-sand bg-white p-4 text-center transition hover:-translate-y-1 hover:border-brand/30 hover:shadow-md sm:w-36 lg:w-44"
                    >
                        <span class="flex size-16 items-center justify-center overflow-hidden rounded-full bg-blush">
                            @if ($catImg)
                                <img src="{{ $catImg }}" alt="" loading="lazy" class="size-full object-cover">
                            @else
                                <span class="text-2xl">🎁</span>
                            @endif
                        </span>
                        <span class="text-xs font-bold text-ink transition group-hover:text-brand">{{ $category->name }}</span>
                    </a>
                @endforeach
                </div>

                {{-- Arrows --}}
                <button type="button" x-show="! atStart" x-transition.opacity x-on:click="scroll(-1)" class="absolute -right-3 top-1/2 z-10 hidden size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-brand shadow-md transition hover:bg-blush active:scale-95 sm:flex" aria-label="السابق">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <button type="button" x-show="! atEnd" x-transition.opacity x-on:click="scroll(1)" class="absolute -left-3 top-1/2 z-10 hidden size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-brand shadow-md transition hover:bg-blush active:scale-95 sm:flex" aria-label="التالي">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            </div>
        </section>
    @endif

    {{-- Featured products --}}
    @if ($featured->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold text-ink">هدايا مختارة ليك</h2>
                <a href="{{ route('web.products') }}" wire:navigate class="text-sm font-bold text-brand hover:underline">شوف الكل ←</a>
            </div>
            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <x-web.product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Services --}}
    @if ($services->isNotEmpty())
        <section class="mt-12 rounded-3xl bg-blush p-6 sm:p-10">
            <h2 class="text-2xl font-extrabold text-ink">خدمات خاصة على مزاجك</h2>
            <p class="mt-1 text-sm text-mocha">تيشرت باسمه؟ مج بصورتكم؟ قولنا وإحنا ننفذ</p>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                @foreach ($services as $service)
                    @php
                        $srvImg = $service->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($service->image) : null;
                    @endphp
                    <a
                        href="{{ route('web.service', $service) }}"
                        wire:navigate
                        class="group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-ink"
                    >
                        @if ($srvImg)
                            <img src="{{ $srvImg }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover opacity-80 transition duration-300 group-hover:scale-105 group-hover:opacity-60">
                        @endif
                        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent px-4 pb-4 pt-10 text-sm font-extrabold text-white">
                            {{ $service->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
