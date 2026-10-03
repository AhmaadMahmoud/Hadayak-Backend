<x-web.layout title="تواصل معنا">
    @php
        $phone = \App\Models\Setting::get('support_phone', '');
        $whatsapp = preg_replace('/\D/', '', \App\Models\Setting::get('support_whatsapp', ''));
        $email = \App\Models\Setting::get('support_email', 'info@hdayak.com');
        $service = request('service');
        $waText = rawurlencode($service ? 'أهلًا هداياك 🎁 عايز أطلب خدمة: '.$service : 'أهلًا هداياك 🎁');
    @endphp

    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl bg-gradient-to-bl from-brand to-brand-deep p-10 text-center text-white shadow-lg">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-white/15 text-3xl">💬</span>
            <h1 class="mt-4 text-3xl font-extrabold">إحنا في خدمتك</h1>
            <p class="mt-2 text-sm text-white/85">
                {{ $service ? 'بخصوص خدمة: '.$service.' — كلمنا وهنظبطك' : 'عندك سؤال أو محتاج مساعدة في طلبك؟ كلمنا في أي وقت' }}
            </p>
        </div>

        <div class="mt-6 space-y-3">
            @if ($whatsapp)
                <a href="https://wa.me/{{ $whatsapp }}?text={{ $waText }}" target="_blank" rel="noopener"
                   class="flex items-center justify-between rounded-2xl border border-sand bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <span class="flex items-center gap-4">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-[#E8F5E9] text-2xl">💚</span>
                        <span><span class="block text-sm font-extrabold text-ink">واتساب</span><span class="text-xs text-mocha">أسرع طريقة للرد عليك</span></span>
                    </span>
                    <span class="text-brand">←</span>
                </a>
            @endif

            @if ($phone)
                <a href="tel:{{ $phone }}"
                   class="flex items-center justify-between rounded-2xl border border-sand bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <span class="flex items-center gap-4">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-blush text-2xl">📞</span>
                        <span><span class="block text-sm font-extrabold text-ink">اتصل بينا</span><span class="text-xs text-mocha" dir="ltr">{{ $phone }}</span></span>
                    </span>
                    <span class="text-brand">←</span>
                </a>
            @endif

            <a href="mailto:{{ $email }}"
               class="flex items-center justify-between rounded-2xl border border-sand bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="flex items-center gap-4">
                    <span class="flex size-11 items-center justify-center rounded-xl bg-[#DCE2F3]/50 text-2xl">✉️</span>
                    <span><span class="block text-sm font-extrabold text-ink">راسلنا بالإيميل</span><span class="text-xs text-mocha" dir="ltr">{{ $email }}</span></span>
                </span>
                <span class="text-brand">←</span>
            </a>
        </div>

        <p class="mt-8 text-center text-xs text-mocha/60">شغالين يوميًا من ١٠ صباحًا لـ ١٠ مساءً</p>
    </div>
</x-web.layout>
