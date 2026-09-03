<?php

namespace Database\Seeders;

use App\Models\CardDesign;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\WrapOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HadayakSeeder extends Seeder
{
    public function run(): void
    {
        // الأقسام التسعة زي شاشة الهوم بالظبط
        $categories = [
            'ألعاب أولاد', 'ألعاب بنات', 'هدايا نسائية',
            'هدايا رجالية', 'زهور طبيعية', 'فخار ومجات',
            'إلكترونيات', 'عطور', 'دباديب',
        ];

        foreach ($categories as $i => $name) {
            Category::firstOrCreate(
                ['slug' => 'cat-'.($i + 1)],
                ['name' => $name, 'sort_order' => $i, 'is_active' => true],
            );
        }

        // منتج الساعة السمارت من شاشة التفاصيل
        $electronics = Category::where('name', 'إلكترونيات')->first();
        Product::firstOrCreate(
            ['slug' => 'hoco-y31-smart-watch'],
            [
                'category_id' => $electronics->id,
                'name' => 'ساعة سمارت',
                'description' => 'تتميز ساعة Hoco Y31 الذكية المزودة بخاصية الاتصال عبر البلوتوث بشاشة لمس HD مقاس 1.46 بوصة بدقة 360×360 بكسل، مما يوفر عرضًا واضحًا للتفاصيل واستجابة لمس سلسة. يأتي التصميم بواجهة دائرية أنيقة تمنح الساعة مظهرًا عصريًا وجذابًا، مع سهولة قراءة الإشعارات والبيانات الصحية والرياضية واستخدام الوظائف الذكية المختلفة طوال اليوم.',
                'price' => 1920,
                'is_active' => true,
            ],
        );

        // خيارات التغليف
        foreach (range(1, 6) as $i) {
            WrapOption::firstOrCreate(
                ['name' => 'تغليف أزرق بفيونكة '.$i],
                ['price' => 100, 'sort_order' => $i, 'is_active' => true],
            );
        }

        // أشكال الكروت
        CardDesign::firstOrCreate(['name' => 'كارت تهنئة مزخرف'], ['price' => 50, 'sort_order' => 0, 'is_active' => true]);
        foreach (range(1, 3) as $i) {
            CardDesign::firstOrCreate(
                ['name' => 'كارت أبيض بوردة '.$i],
                ['price' => 50, 'sort_order' => $i, 'is_active' => true],
            );
        }

        // الخدمات الخاصة
        foreach (['عمل تيشرت مخصص', 'عمل مج مخصص', 'عمل ستيكر مخصص'] as $i => $name) {
            Service::firstOrCreate(['name' => $name], ['sort_order' => $i, 'is_active' => true]);
        }

        // الإعدادات الافتراضية
        Setting::set('delivery_fee', '50');
        Setting::set('cod_enabled', '0'); // الدفع عند الاستلام معطل زي التصميم
    }
}
