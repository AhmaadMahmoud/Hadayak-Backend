<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-bl from-brand to-brand-deep text-white">
        <img src="{{ asset('images/web/gifts-bg.jpg') }}" alt="" aria-hidden="true" class="absolute inset-0 size-full object-cover opacity-15">
        <div class="relative grid items-center gap-8 px-6 py-14 sm:px-12 md:grid-cols-2 md:py-20">
            <div>
                <h1 class="text-3xl font-extrabold leading-tight sm:text-5xl">فرحتك… هديتنا 🎁</h1>
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

    {{-- Highlights strip --}}
    <section class="mt-8 grid gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-3 rounded-2xl border border-sand bg-white p-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blush text-2xl">🎀</span>
            <div><p class="text-sm font-extrabold text-ink">تغليف مميز</p><p class="text-xs text-mocha">هديتك تتقدم بشكل يليق بيها</p></div>
        </div>
        <div class="flex items-center gap-3 rounded-2xl border border-sand bg-white p-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blush text-2xl">🚚</span>
            <div><p class="text-sm font-extrabold text-ink">توصيل سريع</p><p class="text-xs text-mocha">لحد باب البيت في كل مصر</p></div>
        </div>
        <div class="flex items-center gap-3 rounded-2xl border border-sand bg-white p-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blush text-2xl">💌</span>
            <div><p class="text-sm font-extrabold text-ink">بطاقة بكلماتك</p><p class="text-xs text-mocha">رسالتك تتكتب وتوصل مع الهدية</p></div>
        </div>
    </section>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-extrabold text-ink">الأقسام</h2>
                <a href="{{ route('web.products') }}" wire:navigate class="text-sm font-bold text-brand hover:underline">كل المنتجات ←</a>
            </div>
            <div class="mt-5 grid grid-cols-3 gap-4 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ($categories as $category)
                    @php
                        $catImg = $category->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($category->image) : null;
                    @endphp
                    <a
                        href="{{ route('web.products', ['category' => $category->id]) }}"
                        wire:navigate
                        class="group flex flex-col items-center gap-2 rounded-2xl border border-sand bg-white p-4 text-center transition hover:-translate-y-1 hover:border-brand/30 hover:shadow-md"
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
                        href="{{ route('web.contact', ['service' => $service->name]) }}"
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
