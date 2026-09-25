<x-web.layout title="عن هداياك">
    <div class="mx-auto max-w-3xl">
        <div class="rounded-3xl bg-gradient-to-bl from-brand to-brand-deep p-10 text-center text-white shadow-lg">
            <img src="{{ asset('images/logo-white.png') }}" alt="هداياك" class="mx-auto h-16 w-auto">
            <h1 class="mt-4 text-3xl font-extrabold">فرحتك… هديتنا 🎁</h1>
        </div>

        <div class="mt-6 rounded-3xl border border-sand bg-white p-8">
            <p class="text-base leading-loose text-mocha">
                هداياك هو متجرك المتكامل للهدايا في مصر. بنساعدك تختار الهدية المناسبة لكل مناسبة —
                من الألعاب والعطور للزهور الطبيعية والهدايا المخصصة — ونغلفهالك بشكل يفرّح،
                ونوصلهالك لحد الباب أو لباب اللي بتحبهم، مع بطاقة معايدة مكتوب فيها كلماتك أنت.
            </p>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-blush p-6 text-center"><span class="text-3xl">🎀</span><p class="mt-2 text-sm font-extrabold text-ink">تغليف مميز</p><p class="mt-1 text-xs text-mocha">اختار شكل التغليف اللي يعجبك</p></div>
            <div class="rounded-2xl bg-blush p-6 text-center"><span class="text-3xl">🚚</span><p class="mt-2 text-sm font-extrabold text-ink">توصيل سريع</p><p class="mt-1 text-xs text-mocha">لحد باب البيت في كل مصر</p></div>
            <div class="rounded-2xl bg-blush p-6 text-center"><span class="text-3xl">💌</span><p class="mt-2 text-sm font-extrabold text-ink">بطاقة بكلماتك</p><p class="mt-1 text-xs text-mocha">رسالتك توصل مع الهدية</p></div>
        </div>

        <a href="{{ route('web.products') }}" wire:navigate class="mt-8 block rounded-full bg-brand py-4 text-center text-base font-extrabold text-white shadow-lg transition hover:bg-brand-dark">
            ابدأ التسوق دلوقتي
        </a>
    </div>
</x-web.layout>
