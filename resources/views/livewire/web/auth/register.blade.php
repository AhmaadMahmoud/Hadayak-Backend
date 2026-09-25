<div class="mx-auto max-w-md">
    <div class="rounded-3xl border border-sand bg-white p-8 shadow-sm">
        <div class="text-center">
            <img src="{{ asset('images/logo-red.png') }}" alt="هداياك" class="mx-auto h-14 w-auto">
            <h1 class="mt-4 text-2xl font-extrabold text-ink">انضم لعيلة هداياك 🎁</h1>
            <p class="mt-1 text-sm text-mocha">دقيقة واحدة وتبدأ تبعت فرحة</p>
        </div>

        <form wire:submit="submit" class="mt-7 space-y-4">
            <input type="text" wire:model="name" placeholder="الاسم" autocomplete="name"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
            <input type="tel" wire:model="phone" placeholder="رقم الموبايل" autocomplete="tel"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
            <input type="email" wire:model="email" placeholder="البريد الإلكتروني" autocomplete="email"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
            <input type="password" wire:model="password" placeholder="كلمة السر" autocomplete="new-password"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">
            <input type="password" wire:model="password_confirmation" placeholder="تأكيد كلمة السر" autocomplete="new-password"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-sm font-bold text-ink placeholder:text-mocha/50 focus:border-brand focus:outline-none">

            @if ($error || $errors->any())
                <p class="rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $error ?? $errors->first() }}</p>
            @endif

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-full bg-brand py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98] disabled:opacity-60"
            >
                <span wire:loading.remove>إنشاء الحساب</span>
                <span wire:loading>ثواني…</span>
            </button>
        </form>

        <p class="mt-5 text-center text-sm font-bold text-mocha">
            عندك حساب؟
            <a href="{{ route('web.login') }}" wire:navigate class="text-brand hover:underline">سجل دخولك</a>
        </p>
    </div>
</div>
