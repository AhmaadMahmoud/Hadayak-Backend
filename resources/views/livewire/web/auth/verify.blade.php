<div class="mx-auto max-w-md">
    <div class="rounded-3xl border border-sand bg-white p-8 text-center shadow-sm">
        <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-blush text-3xl">📲</span>
        <h1 class="mt-4 text-2xl font-extrabold text-ink">أكد رقمك</h1>
        <p class="mt-1 text-sm text-mocha">بعتنالك كود على <span dir="ltr" class="font-extrabold">{{ session('web_pending_phone') }}</span></p>

        <form wire:submit="submit" class="mt-7 space-y-4">
            <input
                type="text"
                inputmode="numeric"
                wire:model="code"
                placeholder="اكتب الكود"
                class="w-full rounded-2xl border border-sand bg-cream px-5 py-3.5 text-center text-xl font-extrabold tracking-[0.5em] text-ink placeholder:tracking-normal placeholder:text-mocha/50 focus:border-brand focus:outline-none"
            >

            @if ($error || $errors->any())
                <p class="rounded-2xl bg-blush p-3 text-center text-sm font-bold text-brand">{{ $error ?? $errors->first() }}</p>
            @endif

            @if ($notice)
                <p class="rounded-2xl bg-[#E8F5E9] p-3 text-center text-sm font-bold text-[#2E7D32]">{{ $notice }}</p>
            @endif

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full rounded-full bg-brand py-3.5 text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark active:scale-[0.98] disabled:opacity-60"
            >
                <span wire:loading.remove>تأكيد</span>
                <span wire:loading>ثواني…</span>
            </button>
        </form>

        <button type="button" wire:click="resend" class="mt-5 text-sm font-bold text-mocha hover:text-brand">
            الكود موصلش؟ ابعته تاني
        </button>
    </div>
</div>
