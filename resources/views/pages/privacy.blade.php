<x-web.layout title="سياسة الخصوصية">
    <div class="mx-auto max-w-3xl">
        <h1 class="text-3xl font-extrabold text-ink">سياسة الخصوصية</h1>
        <p class="mt-2 text-sm text-mocha/70">آخر تحديث: {{ now()->translatedFormat('d F Y') }}</p>

        <div class="mt-8 space-y-8 rounded-3xl border border-sand bg-white p-8 leading-relaxed text-mocha">
            <section>
                <h2 class="text-lg font-extrabold text-brand">مين إحنا؟</h2>
                <p class="mt-2 text-sm">هداياك متجر إلكتروني مصري لبيع وتوصيل الهدايا من خلال تطبيق الموبايل والموقع الإلكتروني. خصوصيتك تهمنا، والسياسة دي بتوضح إيه البيانات اللي بنجمعها وبنستخدمها إزاي.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">البيانات اللي بنجمعها</h2>
                <p class="mt-2 text-sm">لما بتعمل حساب أو طلب بنجمع: الاسم، رقم الموبايل، البريد الإلكتروني، عناوين التوصيل (وممكن الموقع الجغرافي لو اخترت تحديده على الخريطة)، وتفاصيل طلباتك. لو الطلب هدية بنجمع اسم ورقم المستلم عشان التوصيل بس. كما بنستخدم ملف تعريف (كوكي) مجهول الهوية لفهم استخدام الموقع — زي الصفحات الأكثر زيارة — بهدف تحسين تجربتك، من غير أي مشاركة مع جهات خارجية.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">بنستخدم بياناتك في إيه؟</h2>
                <p class="mt-2 text-sm">تنفيذ وتوصيل طلباتك، التواصل معاك بخصوص الطلب، إرسال كود التحقق (OTP)، إرسال إيميلات تأكيد الطلب وتحديثات حالته، وتحسين خدمتنا. مش بنبيع بياناتك لأي طرف تالت، ومش بنشاركها إلا مع شركاء التوصيل والدفع بالقدر اللازم لتنفيذ طلبك.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">حماية البيانات</h2>
                <p class="mt-2 text-sm">كلمات السر بتتخزن مشفرة، والاتصال بالموقع والتطبيق بيتم عبر قنوات آمنة (HTTPS)، والوصول لبياناتك مقصور على فريق التشغيل المصرح له.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">حقوقك وحذف الحساب</h2>
                <p class="mt-2 text-sm">من حقك تطلب نسخة من بياناتك أو تصححها في أي وقت. وتقدر تحذف حسابك نهائيًا من داخل التطبيق (حسابي ← حذف حسابي) — الحذف بيشمل بياناتك وعناوينك وطلباتك ومش بيتراجع فيه.</p>
            </section>

            <section>
                <h2 class="text-lg font-extrabold text-brand">تواصل معنا</h2>
                <p class="mt-2 text-sm">لأي سؤال عن خصوصيتك راسلنا على <a href="mailto:info@hdayak.com" class="font-bold text-brand hover:underline">info@hdayak.com</a> أو من صفحة <a href="{{ route('web.contact') }}" class="font-bold text-brand hover:underline">تواصل معنا</a>.</p>
            </section>
        </div>
    </div>
</x-web.layout>
