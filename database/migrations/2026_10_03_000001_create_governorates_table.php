<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('shipping_fee', 8, 2)->default(65);
            $table->string('delivery_days')->default('٣–٥ أيام عمل');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('label')->constrained()->nullOnDelete();
        });

        // تعبئة المحافظات — الأسعار والمدد قابلة للتعديل من الداشبورد
        $now = now();
        $fast = '١–٣ أيام عمل';
        $std = '٣–٥ أيام عمل';
        $rows = [
            ['القاهرة', 50, $fast], ['الجيزة', 50, $fast], ['القليوبية', 55, $std],
            ['الإسكندرية', 60, $std], ['البحيرة', 65, $std], ['كفر الشيخ', 65, $std],
            ['الدقهلية', 65, $std], ['الغربية', 65, $std], ['المنوفية', 65, $std],
            ['الشرقية', 65, $std], ['دمياط', 65, $std], ['بورسعيد', 65, $std],
            ['الإسماعيلية', 65, $std], ['السويس', 65, $std], ['الفيوم', 70, $std],
            ['بني سويف', 70, $std], ['المنيا', 70, $std], ['أسيوط', 70, $std],
            ['سوهاج', 70, $std], ['قنا', 75, $std], ['الأقصر', 75, $std],
            ['أسوان', 75, $std], ['البحر الأحمر', 85, $std], ['شمال سيناء', 85, $std],
            ['جنوب سيناء', 85, $std], ['مطروح', 85, $std], ['الوادي الجديد', 85, $std],
        ];

        foreach ($rows as $i => [$name, $fee, $days]) {
            DB::table('governorates')->insert([
                'name' => $name,
                'shipping_fee' => $fee,
                'delivery_days' => $days,
                'sort_order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('governorate_id');
        });
        Schema::dropIfExists('governorates');
    }
};
