@props(['title' => 'هداياك'])

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — هداياك</title>
    <link rel="icon" href="{{ asset('images/logo-red.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-ink antialiased">

    {{-- Top bar --}}
    <header class="sticky top-0 z-40 border-b border-sand bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            {{-- Logo --}}
            <a href="{{ route('web.home') }}" wire:navigate class="flex shrink-0 items-center gap-2">
                <img src="{{ asset('images/logo-red.png') }}" alt="هداياك" class="h-9 w-auto">
                <span class="hidden text-xl font-extrabold text-brand sm:block">هداياك</span>
            </a>

            {{-- Nav (desktop) --}}
            <nav class="hidden items-center gap-6 text-sm font-bold text-mocha md:flex">
                <a href="{{ route('web.home') }}" wire:navigate class="transition hover:text-brand {{ request()->routeIs('web.home') ? 'text-brand' : '' }}">الرئيسية</a>
                <a href="{{ route('web.products') }}" wire:navigate class="transition hover:text-brand {{ request()->routeIs('web.products') ? 'text-brand' : '' }}">المنتجات</a>
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
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-white.png') }}" alt="هداياك" class="h-10 w-auto">
                    <span class="text-2xl font-extrabold">هداياك</span>
                </div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-white/80">
                    متجرك المتكامل للهدايا في مصر — نختار، نغلّف، ونوصّل الفرحة لحد الباب. 🎁
                </p>
            </div>
            <div>
                <p class="text-sm font-extrabold text-accent">روابط سريعة</p>
                <nav class="mt-3 flex flex-col gap-2 text-sm text-white/85">
                    <a href="{{ route('web.products') }}" wire:navigate class="transition hover:text-white">المنتجات</a>
                    <a href="{{ route('web.about') }}" wire:navigate class="transition hover:text-white">عن هداياك</a>
                    <a href="{{ route('web.contact') }}" wire:navigate class="transition hover:text-white">تواصل معنا</a>
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
        </div>
        <div class="border-t border-white/15 py-4 text-center text-xs text-white/60">
            © {{ now()->year }} هداياك — صنع بحب في مصر ❤️
        </div>
    </footer>
</body>
</html>
