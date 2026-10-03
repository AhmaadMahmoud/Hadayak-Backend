<div>
    @if ($success)
        {{-- نجاح --}}
        <div class="mx-auto max-w-md rounded-3xl border border-sand bg-white p-10 text-center shadow-sm">
            <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-[#E8F5E9] text-4xl">🎉</span>
            <h1 class="mt-5 text-2xl font-extrabold text-ink">طلبك وصلنا!</h1>
            <p class="mt-2 text-sm leading-relaxed text-mocha">
                استلمنا تصميمك لخدمة "{{ $service->name }}" — فريقنا هيراجعه ويتواصل معاك على
                <span dir="ltr" class="font-extrabold">{{ $phone }}</span> بالسعر والتفاصيل قريب جدًا.
            </p>
            <a href="{{ route('web.home') }}" wire:navigate class="mt-7 inline-block rounded-full bg-brand px-10 py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark">
                ارجع للرئيسية
            </a>
        </div>
    @else
        <nav class="flex items-center gap-2 text-xs font-bold text-mocha/70">
            <a href="{{ route('web.home') }}" wire:navigate class="hover:text-brand">الرئيسية</a>
            <span>/</span>
            <span class="text-ink">{{ $service->name }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-extrabold text-ink">{{ $service->name }} ✨</h1>
        <p class="mt-1 text-sm text-mocha">ارفع صورتك وشوفها على المنتج قبل ما تطلب — واكتبلنا أي تفاصيل في دماغك</p>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2" x-data="{ url: null }">
            {{-- ===== المعاينة الحية ===== --}}
            <div class="flex flex-col items-center justify-center rounded-3xl bg-blush p-6 sm:p-10">
                @if ($template === 'mug')
                    {{-- المج --}}
                    <svg viewBox="0 0 320 300" class="w-full max-w-sm drop-shadow-xl">
                        <ellipse cx="150" cy="272" rx="105" ry="14" fill="#00000014"/>
                        {{-- الجسم --}}
                        <path d="M60,70 L60,225 Q60,250 90,250 L200,250 Q230,250 230,225 L230,70 Z" fill="#ffffff" stroke="#E6E0DF" stroke-width="2"/>
                        {{-- الأذن --}}
                        <path d="M230,105 Q290,100 288,155 Q286,205 230,198" fill="none" stroke="#E6E0DF" stroke-width="26" stroke-linecap="round"/>
                        <path d="M230,105 Q290,100 288,155 Q286,205 230,198" fill="none" stroke="#ffffff" stroke-width="20" stroke-linecap="round"/>
                        {{-- فتحة المج --}}
                        <ellipse cx="145" cy="70" rx="85" ry="16" fill="#ffffff" stroke="#E6E0DF" stroke-width="2"/>
                        <ellipse cx="145" cy="70" rx="72" ry="11" fill="#F3EFE7"/>
                        {{-- منطقة الطباعة --}}
                        <clipPath id="mugPrint"><rect x="78" y="100" width="134" height="130" rx="8"/></clipPath>
                        <image x-show="url" :href="url" x="78" y="100" width="134" height="130" preserveAspectRatio="xMidYMid slice" clip-path="url(#mugPrint)"/>
                        <g x-show="! url">
                            <rect x="78" y="100" width="134" height="130" rx="8" fill="none" stroke="#D81D35" stroke-width="2" stroke-dasharray="7 6" opacity="0.45"/>
                            <text x="145" y="160" text-anchor="middle" font-size="34" opacity="0.6">🖼️</text>
                            <text x="145" y="192" text-anchor="middle" font-size="12" fill="#5C403C" opacity="0.7">صورتك هتظهر هنا</text>
                        </g>
                        {{-- ظل الانحناء --}}
                        <rect x="60" y="72" width="22" height="176" fill="url(#mugShadeR)" clip-path="none"/>
                        <rect x="208" y="72" width="22" height="176" fill="url(#mugShadeL)"/>
                        <defs>
                            <linearGradient id="mugShadeR" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0" stop-color="#000" stop-opacity="0.10"/><stop offset="1" stop-color="#000" stop-opacity="0"/>
                            </linearGradient>
                            <linearGradient id="mugShadeL" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity="0.10"/>
                            </linearGradient>
                        </defs>
                    </svg>
                @else
                    {{-- التيشرت --}}
                    <svg viewBox="0 0 340 360" class="w-full max-w-sm drop-shadow-xl">
                        <ellipse cx="170" cy="345" rx="120" ry="12" fill="#00000012"/>
                        {{-- جسم التيشرت --}}
                        <path d="M110,38 Q140,62 170,62 Q200,62 230,38 L292,72 L322,138 L268,164 L258,132 L258,318 Q170,338 82,318 L82,132 L72,164 L18,138 L48,72 Z"
                              fill="#ffffff" stroke="#E6E0DF" stroke-width="2.5" stroke-linejoin="round"/>
                        {{-- الرقبة --}}
                        <path d="M110,38 Q140,80 170,80 Q200,80 230,38 Q200,62 170,62 Q140,62 110,38 Z" fill="#F3EFE7" stroke="#E6E0DF" stroke-width="2"/>
                        {{-- خط الأكمام --}}
                        <path d="M82,132 L82,150" stroke="#EFEAE2" stroke-width="2"/>
                        <path d="M258,132 L258,150" stroke="#EFEAE2" stroke-width="2"/>
                        {{-- منطقة الطباعة --}}
                        <clipPath id="teePrint"><rect x="105" y="120" width="130" height="150" rx="6"/></clipPath>
                        <image x-show="url" :href="url" x="105" y="120" width="130" height="150" preserveAspectRatio="xMidYMid meet" clip-path="url(#teePrint)" style="mix-blend-mode: multiply;"/>
                        <g x-show="! url">
                            <rect x="105" y="120" width="130" height="150" rx="6" fill="none" stroke="#D81D35" stroke-width="2" stroke-dasharray="7 6" opacity="0.45"/>
                            <text x="170" y="185" text-anchor="middle" font-size="36" opacity="0.6">🖼️</text>
                            <text x="170" y="220" text-anchor="middle" font-size="12" fill="#5C403C" opacity="0.7">صورتك هتظهر هنا</text>
                        </g>
                    </svg>
                @endif

                <p class="mt-5 text-center text-xs font-bold text-mocha/70">
                    دي معاينة تقريبية — فريقنا بيظبط التصميم النهائي معاك قبل التنفيذ 🎨
                </p>
            </div>

            {{-- ===== الفورم ===== --}}
            <div class="space-y-4">
                {{-- رفع الصورة --}}
                <label
                    class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-3xl border-2 border-dashed border-brand/40 bg-white p-8 text-center transition hover:border-brand hover:bg-blush/40"
                >
                    <span class="text-3xl">📤</span>
                    <span class="text-sm font-extrabold text-ink" x-text="url ? 'تمام! تحب تغير الصورة؟ دوس تاني' : 'ارفع الصورة اللي عايزها على المنتج'"></span>
                    <span class="text-xs text-mocha/70">JPG أو PNG — لحد 4 ميجا</span>
                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        wire:model="photo"
                        x-on:change="$event.target.files[0] && (url = URL.createObjectURL($event.target.files[0]))"
                    >
                </label>
                <div wire:loading wire:target="photo" class="text-center text-xs font-bold text-brand">جاري رفع الصورة…</div>

                {{-- ملاحظات --}}
                <textarea
                    wire:model="notes"
                    rows="3"
                    maxlength="1000"
                    placeholder="ملاحظاتك… (المقاس، اللون، مكان الطباعة، أي تفاصيل تانية) ✍️"
                    class="w-full rounded-2xl border border-sand bg-white p-4 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none"
                ></textarea>

                {{-- بيانات التواصل --}}
                <div class="grid gap-3 sm:grid-cols-2">
                    <input type="text" wire:model="name" placeholder="الاسم"
                        class="w-full rounded-2xl border border-sand bg-white px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                    <input type="tel" wire:model="phone" placeholder="رقم الموبايل"
                        class="w-full rounded-2xl border border-sand bg-white px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                    <input type="email" wire:model="email" placeholder="البريد الإلكتروني (اختياري)" dir="ltr" style="text-align: right;"
                        class="w-full rounded-2xl border border-sand bg-white px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none sm:col-span-2">
                </div>

                @if ($errors->any())
                    <p class="rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $errors->first() }}</p>
                @endif

                <button
                    type="button"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    class="w-full rounded-full bg-brand py-4 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98] disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="submit">ابعت طلبك واحنا نكلمك 🎁</span>
                    <span wire:loading wire:target="submit">ثواني…</span>
                </button>

                <p class="text-center text-xs text-mocha/60">من غير أي التزام — بنراجع التصميم ونبعتلك السعر الأول</p>
            </div>
        </div>
    @endif
</div>
