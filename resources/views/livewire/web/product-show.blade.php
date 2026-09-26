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
            x-data="{
                active: 0,
                images: {{ $images->toJson() }},
                startX: null,
                go(i) {
                    const n = this.images.length;
                    if (! n) return;
                    this.active = (i + n) % n;
                    this.$nextTick(() => this.$root.querySelector(`[data-thumb='${this.active}']`)?.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' }));
                },
                next() { this.go(this.active + 1) },
                prev() { this.go(this.active - 1) },
                swipeEnd(x) {
                    if (this.startX === null) return;
                    const dx = x - this.startX;
                    this.startX = null;
                    if (Math.abs(dx) < 40) return;
                    {{-- الصفحة RTL: السحب لليمين يجيب الصورة اللي بعدها --}}
                    dx > 0 ? this.next() : this.prev();
                },
            }"
            class="space-y-3"
        >
            <div
                class="group relative aspect-square overflow-hidden rounded-3xl border border-sand bg-white"
                x-on:touchstart.passive="startX = $event.touches[0].clientX"
                x-on:touchend="swipeEnd($event.changedTouches[0].clientX)"
            >
                <template x-if="images.length">
                    <div
                        class="flex size-full transition-transform duration-500 ease-out"
                        :style="`transform: translateX(${active * 100}%)`"
                    >
                        <template x-for="(img, i) in images" :key="i">
                            <img :src="img" alt="{{ $product->name }}" :loading="i === 0 ? 'eager' : 'lazy'" class="size-full shrink-0 object-cover" draggable="false">
                        </template>
                    </div>
                </template>
                <template x-if="! images.length">
                    <span class="flex size-full items-center justify-center text-7xl">🎁</span>
                </template>

                <template x-if="images.length > 1">
                    <div>
                        {{-- Arrows --}}
                        <button type="button" x-on:click="prev()" class="absolute right-3 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-brand shadow-md transition hover:bg-white active:scale-95" aria-label="الصورة السابقة">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                        <button type="button" x-on:click="next()" class="absolute left-3 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-brand shadow-md transition hover:bg-white active:scale-95" aria-label="الصورة التالية">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        </button>

                        {{-- Dots --}}
                        <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                            <template x-for="(img, i) in images" :key="'dot' + i">
                                <button
                                    type="button"
                                    x-on:click="go(i)"
                                    class="h-2 rounded-full shadow transition-all duration-300"
                                    :class="active === i ? 'w-6 bg-white' : 'w-2 bg-white/60 hover:bg-white/90'"
                                    :aria-label="'صورة ' + (i + 1)"
                                ></button>
                            </template>
                        </div>
                    </div>
                </template>

                @unless ($inStock)
                    <span class="absolute inset-x-0 top-0 z-10 bg-brand/90 py-2 text-center text-sm font-bold text-white">نفدت الكمية</span>
                @endunless
            </div>

            <template x-if="images.length > 1">
                <div class="flex gap-3 overflow-x-auto pb-1">
                    <template x-for="(img, i) in images" :key="i">
                        <button
                            type="button"
                            :data-thumb="i"
                            x-on:click="go(i)"
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

            @if ($reviewsCount > 0)
                <a href="#reviews" class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-mocha hover:text-brand">
                    <span class="text-accent">{{ str_repeat('★', (int) round($avgRating)) }}{{ str_repeat('☆', 5 - (int) round($avgRating)) }}</span>
                    {{ $avgRating }} من 5 · {{ $reviewsCount }} تقييم
                </a>
            @endif

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

    {{-- Reviews --}}
    <section id="reviews" class="mt-14">
        <div class="flex flex-wrap items-center gap-4">
            <h2 class="text-2xl font-extrabold text-ink">التقييمات</h2>
            @if ($reviewsCount > 0)
                <span class="rounded-full bg-blush px-4 py-1.5 text-sm font-extrabold text-brand">
                    <span class="text-accent">★</span> {{ $avgRating }} من 5 · {{ $reviewsCount }} تقييم
                </span>
            @endif
        </div>

        {{-- فورم التقييم --}}
        @if ($canReview)
            <div class="mt-5 rounded-3xl border border-sand bg-white p-6">
                <p class="text-sm font-extrabold text-ink">قيّم المنتج دا ✍️</p>

                <div class="mt-4 flex items-center gap-1" x-data="{ r: $wire.entangle('myRating'), h: 0 }" dir="ltr">
                    <template x-for="n in 5" :key="n">
                        <button
                            type="button"
                            x-on:click="r = n"
                            x-on:mouseenter="h = n"
                            x-on:mouseleave="h = 0"
                            class="text-3xl transition"
                            :class="(h ? n <= h : n <= r) ? 'text-accent scale-110' : 'text-sand'"
                        >★</button>
                    </template>
                </div>

                <textarea
                    wire:model="reviewComment"
                    rows="3"
                    maxlength="1000"
                    placeholder="احكيلنا تجربتك مع المنتج… (اختياري)"
                    class="mt-4 w-full rounded-2xl border border-sand bg-cream p-4 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none"
                ></textarea>

                @error('myRating')
                    <p class="mt-2 text-sm font-bold text-brand">{{ $message }}</p>
                @enderror

                <button
                    type="button"
                    wire:click="submitReview"
                    wire:loading.attr="disabled"
                    class="mt-4 rounded-full bg-brand px-8 py-3 text-sm font-extrabold text-white shadow-lg transition hover:bg-brand-dark disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="submitReview">انشر تقييمك</span>
                    <span wire:loading wire:target="submitReview">ثواني…</span>
                </button>
            </div>
        @elseif ($alreadyReviewed)
            <p class="mt-5 rounded-2xl bg-[#E8F5E9] p-4 text-sm font-bold text-[#2E7D32]">شكرًا! تقييمك للمنتج دا متسجل عندنا ✓</p>
        @elseif (auth()->check())
            <p class="mt-5 rounded-2xl bg-cream p-4 text-sm font-bold text-mocha">التقييم متاح للعملاء اللي اشتروا المنتج دا — اطلبه وجرّبه وارجع قولنا رأيك 🎁</p>
        @else
            <p class="mt-5 rounded-2xl bg-cream p-4 text-sm font-bold text-mocha">
                <a href="{{ route('web.login') }}" wire:navigate class="text-brand hover:underline">سجل دخولك</a>
                — التقييم متاح للعملاء اللي اشتروا المنتج فقط، عشان كل التقييمات هنا حقيقية 100%
            </p>
        @endif

        {{-- قائمة التقييمات --}}
        @if ($reviews->isNotEmpty())
            <div class="mt-5 space-y-3">
                @foreach ($reviews as $review)
                    <div class="rounded-2xl border border-sand bg-white p-5" wire:key="review-{{ $review->id }}">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="flex size-9 items-center justify-center rounded-full bg-blush text-sm font-extrabold text-brand">
                                {{ mb_substr($review->user->name ?? 'ع', 0, 1) }}
                            </span>
                            <span class="text-sm font-extrabold text-ink">{{ $review->user->name ?? 'عميل هداياك' }}</span>
                            <span class="mr-auto text-xs text-mocha/60">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-2 text-accent" dir="ltr" style="text-align: right;">{{ str_repeat('★', $review->rating) }}<span class="text-sand">{{ str_repeat('★', 5 - $review->rating) }}</span></p>
                        @if ($review->comment)
                            <p class="mt-2 text-sm leading-relaxed text-mocha">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif ($reviewsCount === 0)
            <p class="mt-5 text-sm text-mocha/60">لسه مفيش تقييمات — كن أول من يقيّم المنتج دا</p>
        @endif
    </section>
</div>