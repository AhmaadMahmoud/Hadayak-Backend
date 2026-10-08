<div>
    <h1 class="text-3xl font-extrabold text-ink">إتمام الطلب</h1>

    <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Guest contact info --}}
            @guest
                <div class="rounded-2xl border border-sand bg-white p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-extrabold text-ink">👋 بياناتك</h2>
                        <a href="{{ route('web.login') }}" wire:navigate class="text-sm font-bold text-brand hover:underline">
                            عندك حساب؟ سجل دخولك
                        </a>
                    </div>
                    <p class="mt-1 text-xs text-mocha">هنبعتلك تأكيد الطلب وتحديثاته على إيميلك</p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <input type="text" wire:model="guestName" placeholder="الاسم" autocomplete="name"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="tel" wire:model="guestPhone" placeholder="رقم الموبايل" autocomplete="tel"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="email" wire:model="guestEmail" placeholder="البريد الإلكتروني" autocomplete="email" dir="ltr" style="text-align: right;"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none sm:col-span-2">
                    </div>
                </div>
            @endguest

            {{-- Delivery type --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-lg font-extrabold text-ink">الطلب دا لمين؟</h2>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <button
                        type="button"
                        wire:click="setDeliveryType('me')"
                        @class([
                            'rounded-2xl border-2 p-4 text-center transition',
                            'border-brand bg-blush' => $deliveryType === 'me',
                            'border-sand hover:border-brand/40' => $deliveryType !== 'me',
                        ])
                    >
                        <span class="text-2xl">🛍️</span>
                        <p class="mt-1 text-sm font-extrabold text-ink">التوصيل ليّا</p>
                    </button>
                    <button
                        type="button"
                        wire:click="setDeliveryType('gift')"
                        @class([
                            'rounded-2xl border-2 p-4 text-center transition',
                            'border-brand bg-blush' => $deliveryType === 'gift',
                            'border-sand hover:border-brand/40' => $deliveryType !== 'gift',
                        ])
                    >
                        <span class="text-2xl">🎁</span>
                        <p class="mt-1 text-sm font-extrabold text-ink">هدية لحد غالي</p>
                    </button>
                </div>

                @if ($deliveryType === 'gift')
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <input type="text" wire:model="recipientName" placeholder="اسم المستلم"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="tel" wire:model="recipientPhone" placeholder="رقم موبايل المستلم"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                    </div>
                    <p class="mt-3 rounded-xl bg-blush px-4 py-2.5 text-xs font-bold text-brand">🤫 متقلقش — مش هنبعت فاتورة السعر مع الهدية</p>
                @endif
            </div>

            {{-- Address --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-lg font-extrabold text-ink">📍 عنوان التوصيل</h2>

                @if ($addresses->isNotEmpty())
                    <div class="mt-4 space-y-2">
                        @foreach ($addresses as $address)
                            <button
                                type="button"
                                wire:click="selectAddress({{ $address->id }})"
                                wire:key="addr-{{ $address->id }}"
                                @class([
                                    'flex w-full items-center gap-3 rounded-2xl border-2 p-4 text-right transition',
                                    'border-brand bg-blush' => $addressId === $address->id && ! $showNewAddress,
                                    'border-sand hover:border-brand/40' => $addressId !== $address->id || $showNewAddress,
                                ])
                            >
                                <span @class([
                                    'flex size-5 shrink-0 items-center justify-center rounded-full border-2',
                                    'border-brand' => $addressId === $address->id && ! $showNewAddress,
                                    'border-sand' => $addressId !== $address->id || $showNewAddress,
                                ])>
                                    @if ($addressId === $address->id && ! $showNewAddress)
                                        <span class="size-2.5 rounded-full bg-brand"></span>
                                    @endif
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-extrabold text-ink">
                                        {{ ['home' => 'المنزل', 'office' => 'المكتب'][$address->label] ?? 'عنوان' }}
                                        @if ($address->is_default) <span class="text-xs text-accent">⭐</span> @endif
                                    </span>
                                    <span class="block truncate text-xs text-mocha">{{ $address->governorate ? $address->governorate->name.' — ' : '' }}{{ $address->area }} — {{ $address->street }}</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <button type="button" wire:click="toggleNewAddress" class="mt-3 text-sm font-bold text-brand hover:underline">
                    {{ $showNewAddress ? '− إخفاء العنوان الجديد' : '+ إضافة عنوان جديد' }}
                </button>

                @if ($showNewAddress)
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <select
                            wire:model.live="governorateId"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink focus:border-brand focus:outline-none sm:col-span-2"
                        >
                            <option value="">اختار المحافظة…</option>
                            @foreach ($governorates as $gov)
                                <option value="{{ $gov->id }}">{{ $gov->name }} — شحن {{ number_format($gov->shipping_fee) }} ج.م ({{ $gov->delivery_days }})</option>
                            @endforeach
                        </select>
                        <input type="text" wire:model="area" placeholder="المنطقة / المدينة"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="text" wire:model="street" placeholder="الشارع"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="text" wire:model="building" placeholder="رقم العمارة (اختياري)"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="text" wire:model="floor" placeholder="الدور (اختياري)"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="text" wire:model="apartment" placeholder="الشقة (اختياري)"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="tel" wire:model="addressPhone" placeholder="رقم للتواصل (اختياري)"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
                        <input type="text" wire:model="landmark" placeholder="علامة مميزة (اختياري)"
                            class="w-full rounded-2xl border border-sand bg-cream px-5 py-3 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none sm:col-span-2">
                    </div>
                @endif
            </div>

            {{-- Payment --}}
            <div class="rounded-2xl border border-sand bg-white p-5">
                <h2 class="text-lg font-extrabold text-ink">💳 وسيلة الدفع</h2>
                <div class="mt-4 space-y-2">
                    @foreach ([
                        'card' => ['بطاقة ائتمان', true],
                        'vodafone_cash' => ['فودافون كاش', true],
                        'instapay' => ['انستا باي', true],
                        'cod' => ['الدفع عند الاستلام', $codEnabled],
                    ] as $method => [$label, $enabled])
                        <button
                            type="button"
                            @if ($enabled) wire:click="setPayment('{{ $method }}')" @endif
                            @disabled(! $enabled)
                            wire:key="pay-{{ $method }}"
                            @class([
                                'flex w-full items-center gap-3 rounded-2xl border-2 p-4 text-right transition',
                                'border-brand bg-blush' => $paymentMethod === $method,
                                'border-sand hover:border-brand/40' => $paymentMethod !== $method && $enabled,
                                'border-sand opacity-40' => ! $enabled,
                            ])
                        >
                            <span @class([
                                'flex size-5 shrink-0 items-center justify-center rounded-full border-2',
                                'border-brand' => $paymentMethod === $method,
                                'border-sand' => $paymentMethod !== $method,
                            ])>
                                @if ($paymentMethod === $method)
                                    <span class="size-2.5 rounded-full bg-brand"></span>
                                @endif
                            </span>
                            <span class="text-sm font-extrabold text-ink">{{ $label }}</span>
                            @unless ($enabled)
                                <span class="mr-auto text-xs font-bold text-mocha/60">غير متاح</span>
                            @endunless
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="h-fit rounded-2xl border border-sand bg-white p-5 lg:sticky lg:top-24">
            <h2 class="text-lg font-extrabold text-ink">ملخص الطلب</h2>
            <div class="mt-4 space-y-2 border-b border-sand pb-3 text-sm">
                @foreach ($items as $item)
                    <div class="flex justify-between text-mocha" wire:key="sum-{{ $item['id'] }}">
                        <span class="truncate">{{ $item['name'] }} <span class="text-xs text-mocha/60">× {{ $item['qty'] }}</span></span>
                        <span class="shrink-0">{{ number_format($item['price'] * $item['qty']) }} ج.م</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 space-y-2.5 text-sm">
                @if ($wrapPrice > 0)
                    <div class="flex justify-between text-mocha"><span>🎀 التغليف</span><span>{{ number_format($wrapPrice) }} ج.م</span></div>
                @endif
                @if ($cardPrice > 0)
                    <div class="flex justify-between text-mocha"><span>💌 البطاقة</span><span>{{ number_format($cardPrice) }} ج.م</span></div>
                @endif
                <div class="flex justify-between text-mocha">
                    <span>🚚 التوصيل</span>
                    <span>{{ $freeShipping ? 'مجاني 🎉' : ($deliveryFee !== null ? number_format($deliveryFee).' ج.م' : 'حسب المحافظة') }}</span>
                </div>
                @if ($deliveryDays)
                    <p class="rounded-xl bg-blush px-3 py-2 text-xs font-bold text-brand">📦 التوصيل المتوقع خلال {{ $deliveryDays }}</p>
                @endif
                <div class="flex justify-between border-t border-sand pt-3 text-base font-extrabold text-ink">
                    <span>الإجمالي</span>
                    <span class="text-brand">{{ number_format($itemsTotal + $wrapPrice + $cardPrice + ($deliveryFee ?? 0)) }} ج.م{{ $deliveryFee === null ? ' + الشحن' : '' }}</span>
                </div>
            </div>

            @if ($error || $errors->any())
                <p class="mt-4 rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $error ?? $errors->first() }}</p>
            @endif

            <button
                type="button"
                wire:click="placeOrder"
                wire:loading.attr="disabled"
                class="mt-5 w-full rounded-full bg-brand py-4 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98] disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="placeOrder">تأكيد الطلب 🎉</span>
                <span wire:loading wire:target="placeOrder">جاري تأكيد الطلب…</span>
            </button>

            <p class="mt-3 text-center text-[11px] leading-relaxed text-mocha/60">
                بتأكيد الطلب انت موافق على
                <a href="{{ route('web.returns') }}" wire:navigate class="font-bold text-brand hover:underline">سياسة الاستبدال والإرجاع</a>
                و<a href="{{ route('web.privacy') }}" wire:navigate class="font-bold text-brand hover:underline">سياسة الخصوصية</a>
            </p>
        </div>
    </div>
</div>
