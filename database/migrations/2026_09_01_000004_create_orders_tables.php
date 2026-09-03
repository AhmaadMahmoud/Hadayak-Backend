<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // HDK-2026-0001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();

            // حالة الطلب
            $table->string('status')->default('pending'); // pending, confirmed, preparing, delivering, delivered, cancelled

            // نوع التوصيل (شاشة إضافة العنوان)
            $table->string('delivery_type')->default('me'); // me = التوصيل لي, gift = التوصيل كهدية
            $table->string('recipient_name')->nullable();   // تفاصيل المستلم لو هدية
            $table->string('recipient_phone')->nullable();
            $table->boolean('hide_invoice')->default(false); // مش هنبعت فاتورة السعر مع الهدية 😉

            // التغليف والكارت (snapshot للسعر وقت الطلب)
            $table->foreignId('wrap_option_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('wrap_price', 10, 2)->default(0);
            $table->foreignId('card_design_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('card_price', 10, 2)->default(0);
            $table->text('card_message')->nullable(); // الكتابة على الكارت

            // الدفع
            $table->string('payment_method')->default('card'); // card, vodafone_cash, instapay, cod
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('payment_reference')->nullable();

            // الحسابات
            $table->decimal('items_total', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');            // snapshot وقت الطلب
            $table->decimal('price', 10, 2);           // snapshot وقت الطلب
            $table->unsignedInteger('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
