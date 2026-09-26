{{-- تنبيه "اتضاف للسلة" — بيسمع لحدث cart-added من أي كومبوننت Livewire --}}
<div
    x-data="{
        show: false,
        item: {},
        timer: null,
        key: 0,
        open(detail) {
            this.item = detail;
            this.key++;
            this.show = true;
            this.startTimer();
        },
        startTimer() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.show = false, 4500);
        },
        pause() {
            clearTimeout(this.timer);
        },
        price(v) {
            return Number(v).toLocaleString('en-US');
        },
    }"
    x-on:cart-added.window="open($event.detail)"
    x-on:keydown.escape.window="show = false"
    class="pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4 sm:inset-x-auto sm:left-6 sm:justify-end sm:px-0"
    aria-live="polite"
>
    <div
        x-show="show"
        x-cloak
        x-on:mouseenter="pause()"
        x-on:mouseleave="key++; startTimer()"
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="-translate-y-4 scale-95 opacity-0"
        x-transition:enter-end="translate-y-0 scale-100 opacity-100"
        x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="translate-y-0 scale-100 opacity-100"
        x-transition:leave-end="-translate-y-2 scale-95 opacity-0"
        class="pointer-events-auto relative w-full max-w-sm overflow-hidden rounded-2xl border border-sand bg-white shadow-2xl shadow-ink/15 ring-1 ring-black/5"
        role="status"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 bg-gradient-to-l from-brand to-brand-dark px-4 py-2.5 text-white">
            <div class="flex items-center gap-2">
                <span class="flex size-6 items-center justify-center rounded-full bg-white/20">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
                </span>
                <p class="text-sm font-extrabold">اتضافت لسلة الهدايا 🎁</p>
            </div>
            <button
                type="button"
                x-on:click="show = false"
                class="flex size-7 items-center justify-center rounded-full text-white/80 transition hover:bg-white/15 hover:text-white"
                aria-label="إغلاق"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        {{-- Product --}}
        <div class="flex items-center gap-3 p-4">
            <div class="relative shrink-0">
                <template x-if="item.image">
                    <img :src="item.image" :alt="item.name" class="size-16 rounded-xl border border-sand object-cover">
                </template>
                <template x-if="!item.image">
                    <div class="flex size-16 items-center justify-center rounded-xl bg-blush text-2xl">🎁</div>
                </template>
                <span
                    x-show="item.qty > 1"
                    x-text="'×' + item.qty"
                    class="absolute -top-2 -right-2 rounded-full bg-accent px-1.5 py-0.5 text-[11px] font-extrabold text-ink shadow"
                ></span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="line-clamp-2 text-sm font-bold leading-snug text-ink" x-text="item.name"></p>
                <p class="mt-1 text-base font-extrabold text-brand">
                    <span x-text="price(item.price)"></span>
                    <span class="text-xs font-bold text-mocha">ج.م</span>
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 px-4 pb-4">
            <a
                href="{{ route('web.cart') }}"
                wire:navigate
                x-on:click="show = false"
                class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand/25 transition hover:bg-brand-dark"
            >
                عرض السلة
                <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs" x-text="item.count"></span>
            </a>
            <button
                type="button"
                x-on:click="show = false"
                class="rounded-xl border border-sand px-4 py-2.5 text-sm font-bold text-mocha transition hover:bg-cream"
            >
                كمّل تسوق
            </button>
        </div>

        {{-- Auto-dismiss progress --}}
        <div class="h-1 bg-sand">
            <template x-for="k in [key]" :key="k">
                <div class="cart-toast-progress h-full bg-accent"></div>
            </template>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .cart-toast-progress { transform-origin: right; animation: cart-toast-shrink 4.5s linear forwards; }
        [role="status"]:hover .cart-toast-progress { animation-play-state: paused; }
        @keyframes cart-toast-shrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }
    </style>
</div>
