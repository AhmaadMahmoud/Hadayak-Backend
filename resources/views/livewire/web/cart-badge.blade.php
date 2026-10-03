<a
    href="{{ route('web.cart') }}"
    wire:navigate
    x-data="{ pop: false }"
    x-on:cart-updated.window="pop = false; $nextTick(() => { pop = true; setTimeout(() => pop = false, 600) })"
    :class="pop && 'cart-pop'"
    class="relative flex h-10 items-center justify-center gap-1.5 rounded-full px-2.5 text-brand transition hover:bg-blush"
    aria-label="سلة الهدايا"
>
    <span class="hidden text-sm font-extrabold lg:block">السلة</span>
    <svg @class(['size-6', 'cart-nudge' => $count > 0]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="8" width="18" height="4" rx="1"/>
        <path d="M12 8v13M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7M7.5 8a2.5 2.5 0 0 1 0-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 0 1 0 5"/>
    </svg>
    @if ($count > 0)
        <span class="absolute -top-0.5 -left-0.5 flex size-5 items-center justify-center rounded-full bg-accent text-[11px] font-extrabold text-brand-dark">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</a>
