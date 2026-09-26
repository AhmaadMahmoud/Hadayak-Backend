# 🚀 دليل نشر هداياك — hdayak.com

## الروتين المعتاد (تعديل → لايف)

```bash
# ١) على جهازك:
cd ~/GreenCodes-Laravel/Hadayak-Backend

# ٢) لو عدلت شاشات/تنسيقات (blade / CSS / JS):
npm run build

# ٣) ارفع على GitHub:
git add -A
git commit -m "وصف التعديل"
git push origin main

# ٤) انشر:
ssh hadayak 'cd domains/hdayak.com/app && bash deploy.sh'
```

## دخول السيرفر يدوي

```bash
ssh hadayak    # الدخول (اختصار معرف في ~/.ssh/config على الماك)
exit           # الخروج
```

- بروبمت `ahmed@Ahmeds-MacBook-Air` = جهازك (منه: git push / ssh)
- بروبمت `u292193554@us-bos...` = السيرفر (جواه: deploy.sh / artisan)

## إسعافات أولية على السيرفر

```bash
cd ~/domains/hdayak.com/app
tail -50 storage/logs/laravel-$(date +%F).log      # آخر الأخطاء + أكواد OTP
php artisan optimize:clear && php artisan optimize # تصفير الكاش
```

## معلومات البنية

- الكود على السيرفر: `~/domains/hdayak.com/app` (مربوط بـ GitHub بمفتاح deploy قراءة-فقط)
- الدومين بيتقدم من: `public_html` ← وصلة لـ `app/public`
- الداتابيز: MySQL `u292193554_hadayak` (بياناتها في `.env` على السيرفر — مش في git)
- الصور المرفوعة: `storage/app/public` ← وصلة من `public/storage` (مش في git)
- تعديل المنتجات/الطلبات/الإعدادات: من الداشبورد `/admin` مباشرة — مش محتاجة نشر

## لسه ناقص قبل الإطلاق الفعلي

- [ ] SMTP حقيقي للإيميلات (`MAIL_MAILER` لسه `log` — Hostinger Emails + تعديل .env على السيرفر)
- [ ] مزود SMS للـ OTP (الأكواد حاليًا في اللوج)
- [ ] تحويل التطبيق الموبايل على `API_BASE_URL=https://hdayak.com/api`
