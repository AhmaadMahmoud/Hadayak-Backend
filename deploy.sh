#!/bin/bash
# سكريبت نشر هداياك على Hostinger — بيتشغل على السيرفر
set -e

cd ~/domains/hdayak.com/app

echo "⬇️  بنسحب آخر نسخة من GitHub..."
git pull origin main

echo "📦 بنحدث الحزم (لو اتغيرت)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🗄  بنشغل الميجريشن..."
php artisan migrate --force

echo "🧹 بنجدد الكاش..."
php artisan optimize:clear
php artisan optimize

echo "✅ تم النشر بنجاح — hdayak.com محدث!"
