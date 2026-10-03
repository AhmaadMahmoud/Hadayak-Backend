@props(['title' => 'هداياك', 'description' => 'هداياك — متجر الهدايا الأول في مصر: اختار الهدية، غلفها ببطاقة معايدة بكلماتك، ووصلها لحد باب اللي بتحبهم في كل المحافظات.'])

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — هداياك</title>
    <meta name="description" content="{{ $description }}">
    <link rel="icon" href="{{ asset('images/logo-red.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">

    {{-- Top bar --}}
    <header class="sticky top-0 z-40 border-b border-sand bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            {{-- Logo --}}
            <a href="{{ route('web.home') }}" wire:navigate class="flex shrink-0 items-center gap-2">
                <img src="{{ asset('images/logo-red.png') }}" alt="هداياك" width="39" height="36" class="h-9 w-auto">
                <span class="hidden text-xl font-extrabold text-brand sm:block">هداياك</span>
            </a>

            {{-- Nav (desktop) --}}
            <nav class="hidden items-center gap-6 text-sm font-bold text-mocha md:flex">
                <a href="{{ route('web.home') }}" wire:navigate class="transition hover:text-brand {{ request()->routeIs('web.home') ? 'text-brand' : '' }}">الرئيسية</a>
                @php
                    $navCategories = \Illuminate\Support\Facades\Cache::remember(
                        'nav_categories_v3', 300,
                        fn () => \App\Models\Category::where('is_active', true)
                            ->whereHas('products', fn ($q) => $q->where('is_active', true))
                            ->orderBy('sort_order')
                            ->get(['id', 'name', 'image'])
                            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'image' => $c->image])
                            ->all()
                    );
                @endphp
                <div class="relative" x-data="{ open: false }" x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                    <a
                        href="{{ route('web.products') }}"
                        wire:navigate
                        class="flex items-center gap-1.5 transition hover:text-brand {{ request()->routeIs('web.products') ? 'text-brand' : '' }}"
                    >
                        المنتجات
                        <svg class="size-3.5 transition duration-200" :class="open && 'rotate-180 text-brand'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                    </a>

                    @if (count($navCategories))
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute right-0 top-full z-50 w-[580px] pt-4"
                        >
                            <div class="overflow-hidden rounded-3xl border border-sand bg-white shadow-2xl">
                                <p class="px-6 pb-1 pt-5 text-xs font-extrabold text-mocha/60">تسوق حسب القسم 🎁</p>

                                <div class="grid grid-cols-3 gap-1 p-4">
                                    @foreach ($navCategories as $navCat)
                                        @php
                                            $navImg = $navCat['image'] ? \Illuminate\Support\Facades\Storage::disk('public')->url($navCat['image']) : null;
                                        @endphp
                                        <a
                                            href="{{ route('web.products', ['category' => $navCat['id']]) }}"
                                            wire:navigate
                                            x-on:click="open = false"
                                            class="group/cat flex items-center gap-3 rounded-2xl p-2.5 transition hover:bg-blush"
                                        >
                                            <span class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-cream ring-2 ring-sand transition group-hover/cat:ring-brand/40">
                                                @if ($navImg)
                                                    <img src="{{ $navImg }}" alt="" loading="lazy" class="size-full object-cover">
                                                @else
                                                    <span class="text-lg">🎁</span>
                                                @endif
                                            </span>
                                            <span class="min-w-0 truncate text-sm font-bold text-ink transition group-hover/cat:text-brand">{{ $navCat['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    @endif
                </div>
                <a href="{{ route('web.about') }}" wire:navigate class="transition hover:text-brand {{ request()->routeIs('web.about') ? 'text-brand' : '' }}">عن هداياك</a>
                <a href="{{ route('web.contact') }}" wire:navigate class="transition hover:text-brand {{ request()->routeIs('web.contact') ? 'text-brand' : '' }}">تواصل معنا</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('web.orders') }}" wire:navigate class="hidden rounded-full px-4 py-2 text-sm font-bold text-mocha transition hover:bg-blush hover:text-brand sm:block">
                        طلباتي
                    </a>
                    <form method="POST" action="{{ route('web.logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="rounded-full px-3 py-2 text-xs font-bold text-mocha/60 transition hover:text-brand">خروج</button>
                    </form>
                @else
                    <a href="{{ route('web.login') }}" wire:navigate class="hidden rounded-full border-2 border-brand px-4 py-1.5 text-sm font-bold text-brand transition hover:bg-brand hover:text-white sm:block">
                        تسجيل الدخول
                    </a>
                @endauth

                {{-- Cart --}}
                <livewire:web.cart-badge />

                {{-- Mobile menu button --}}
                <button
                    type="button"
                    class="flex size-10 items-center justify-center rounded-full text-mocha transition hover:bg-blush md:hidden"
                    x-data
                    x-on:click="document.getElementById('mobile-menu').classList.toggle('hidden')"
                    aria-label="القائمة"
                >
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden border-t border-sand bg-white px-4 py-3 md:hidden">
            <nav class="flex flex-col gap-1 text-sm font-bold text-mocha">
                <a href="{{ route('web.home') }}" wire:navigate class="rounded-lg px-3 py-2.5 hover:bg-blush hover:text-brand">الرئيسية</a>
                <a href="{{ route('web.products') }}" wire:navigate class="rounded-lg px-3 py-2.5 hover:bg-blush hover:text-brand">المنتجات</a>
                <a href="{{ route('web.about') }}" wire:navigate class="rounded-lg px-3 py-2.5 hover:bg-blush hover:text-brand">عن هداياك</a>
                <a href="{{ route('web.contact') }}" wire:navigate class="rounded-lg px-3 py-2.5 hover:bg-blush hover:text-brand">تواصل معنا</a>
                @auth
                    <a href="{{ route('web.orders') }}" wire:navigate class="rounded-lg px-3 py-2.5 hover:bg-blush hover:text-brand">طلباتي</a>
                    <form method="POST" action="{{ route('web.logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-right text-mocha/60 hover:bg-blush">تسجيل الخروج</button>
                    </form>
                @else
                    <a href="{{ route('web.login') }}" wire:navigate class="rounded-lg px-3 py-2.5 text-brand hover:bg-blush">تسجيل الدخول</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Page content --}}
    <main class="mx-auto min-h-[60vh] w-full max-w-6xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-12 bg-gradient-to-bl from-brand to-brand-deep text-white">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 px-4 py-12 sm:px-6 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-white.png') }}" alt="هداياك" width="43" height="40" class="h-10 w-auto">
                    <span class="text-2xl font-extrabold">هداياك</span>
                </div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-white/80">
                    متجرك المتكامل للهدايا في مصر — نختار، نغلّف، ونوصّل الفرحة لحد الباب. 🎁
                </p>

                @php
                    $socials = array_filter([
                        'facebook' => \App\Models\Setting::get('social_facebook', ''),
                        'instagram' => \App\Models\Setting::get('social_instagram', ''),
                        'tiktok' => \App\Models\Setting::get('social_tiktok', ''),
                    ]);
                @endphp
                @if (count($socials))
                    <div class="mt-4 flex gap-3">
                        @foreach ($socials as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $network }}"
                               class="flex size-9 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/25">
                                @if ($network === 'facebook')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14C17.17 2.1 15.95 2 14.66 2 11.97 2 10 3.66 10 6.7v2.8H7v4h3V22h4v-8.5Z"/></svg>
                                @elseif ($network === 'instagram')
                                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.8" cy="6.2" r="1.3" fill="currentColor" stroke="none"/></svg>
                                @else
                                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.6 6.9a4.8 4.8 0 0 1-3.8-4.4V2h-3.3v13.7a2.9 2.9 0 1 1-2.9-2.9c.3 0 .6 0 .9.1V9.5a6.2 6.2 0 1 0 5.3 6.2V8.6a8 8 0 0 0 4.7 1.5V6.9h-.9Z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <p class="text-sm font-extrabold text-accent">روابط سريعة</p>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-white/85">
                    <a href="{{ route('web.products') }}" wire:navigate class="transition hover:text-white">المنتجات</a>
                    <a href="{{ route('web.about') }}" wire:navigate class="transition hover:text-white">عن هداياك</a>
                    <a href="{{ route('web.contact') }}" wire:navigate class="transition hover:text-white">تواصل معنا</a>
                    <a href="{{ route('web.returns') }}" wire:navigate class="transition hover:text-white">الاستبدال والإرجاع</a>
                    <a href="{{ route('web.privacy') }}" wire:navigate class="transition hover:text-white">سياسة الخصوصية</a>
                </nav>
            </div>
            <div>
                <p class="text-sm font-extrabold text-accent">حسابك</p>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-white/85">
                    @auth
                        <a href="{{ route('web.orders') }}" wire:navigate class="transition hover:text-white">طلباتي</a>
                    @else
                        <a href="{{ route('web.login') }}" wire:navigate class="transition hover:text-white">تسجيل الدخول</a>
                        <a href="{{ route('web.register') }}" wire:navigate class="transition hover:text-white">حساب جديد</a>
                    @endauth
                    <a href="{{ route('web.cart') }}" wire:navigate class="transition hover:text-white">سلة الهدايا</a>
                </nav>
            </div>
            <div>
                <p class="text-sm font-extrabold text-accent">تابعنا</p>
                @php
                    $socials = [
                        ['label' => 'Instagram', 'url' => \App\Models\Setting::get('instagram_url', '#'), 'icon' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.6" fill="currentColor"/>'],
                        ['label' => 'Facebook', 'url' => \App\Models\Setting::get('facebook_url', '#'), 'icon' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'],
                        ['label' => 'LinkedIn', 'url' => \App\Models\Setting::get('linkedin_url', '#'), 'icon' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>'],
                    ];
                @endphp
                <div class="mt-3 flex gap-2">
                    @foreach ($socials as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}" class="flex size-8 items-center justify-center rounded-full bg-white/15 text-white transition hover:bg-white hover:text-brand">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $social['icon'] !!}</svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-t border-white/15 py-4 text-center text-xs text-white/60">
            © {{ now()->year }} هداياك
        </div>
    </footer>
    <x-web.cart-toast />
</body>
</html>
