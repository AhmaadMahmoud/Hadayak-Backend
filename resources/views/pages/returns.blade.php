<x-web.layout title="سياسة الاستبدال والإرجاع">
    <div class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-extrabold text-ink">سياسة الاستبدال والإرجاع</h1>
        <p class="mt-2 text-sm text-mocha/70">آخر تحديث: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="mt-8 space-y-8 rounded-3xl border border-sand bg-white p-8 leading-relaxed text-mocha">
            <section>
                <h2 class="text-lg font-extrabold text-brand">قبل التسليم — إلغاء مجاني</h2>
                <p class="mt-2 text-sm">تقدر تلغي طلبك مجانًا طول ما حالته لسه «جديد» — من صفحة الطلب مباشرة أو بالتواصل معانا. بعد ما الطلب يدخل مرحلة التجهيز، كلمنا بأسرع وقت وهنحاول نساعدك قبل ما يتشحن.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">عند الاستلام — افحص هديتك</h2>
                <p class="mt-2 text-sm">من حقك تفحص الطلب وقت الاستلام. لو المنتج وصل تالف أو مكسور أو مختلف عن اللي طلبته، ارفض الاستلام أو كلمنا خلال <b>48 ساعة</b> بصور توضح الحالة، وهنستبدله أو نرجعلك المبلغ كاملًا شامل الشحن — من غير أي تكلفة عليك.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">الاستبدال والإرجاع — خلال 14 يوم</h2>
                <p class="mt-2 text-sm">للمنتجات العادية (غير المخصصة)، تقدر تطلب استبدال أو إرجاع خلال <b>14 يوم</b> من الاستلام، بشرط إن المنتج بحالته الأصلية: غير مستخدم، بتغليفه وملحقاته كاملة. مصاريف شحن الإرجاع في الحالة دي بيتحملها العميل، واسترداد المبلغ بيتم بنفس طريقة الدفع خلال 7–14 يوم عمل من استلامنا للمنتج وفحصه.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">المنتجات المخصصة — حالة خاصة 🎨</h2>
                <p class="mt-2 text-sm">المنتجات المنفذة خصيصًا ليك (تيشرت بصورتك، مج بتصميمك، ستيكرات مخصصة وما شابه) <b>غير قابلة للاستبدال أو الإرجاع</b> لأنها بتتصنع بناءً على طلبك — ودا السبب اللي بنراجع معاك التصميم والسعر قبل التنفيذ. استثناء واحد: لو وصلك المنتج المخصص تالفًا أو مختلفًا عن التصميم المتفق عليه، بنعيد تنفيذه أو نرد المبلغ كاملًا.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">الزهور الطبيعية والمنتجات سريعة التلف</h2>
                <p class="mt-2 text-sm">بحكم طبيعتها، غير قابلة للإرجاع — ولو وصلت بحالة غير مرضية، كلمنا خلال 24 ساعة من الاستلام بصورة وهنعوضك.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">عايز تستبدل أو ترجع؟</h2>
                <p class="mt-2 text-sm">كلمنا من صفحة <a href="{{ route('web.contact') }}" class="font-bold text-brand hover:underline">تواصل معنا</a> أو على <a href="mailto:info@hdayak.com" class="font-bold text-brand hover:underline">info@hdayak.com</a> ومعاك رقم الطلب — وهنرد عليك في أقرب وقت.</p>
            </section>
        </div>
    </div>
</x-web.layout>
