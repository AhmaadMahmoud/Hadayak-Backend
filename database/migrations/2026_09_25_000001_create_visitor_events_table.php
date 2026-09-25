<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_events', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 40)->index();   // كوكي مجهولة ثابتة للزائر
            $table->string('session_id', 60)->index();   // جلسة التصفح الحالية
            $table->foreignId('user_id')->nullable()->index(); // لو مسجل دخول
            $table->string('type', 30)->index();         // page_view, add_to_cart, search, ...
            $table->string('page')->nullable();          // اسم الصفحة/الراوت
            $table->string('label', 500)->nullable();    // وصف مقروء: اسم المنتج، كلمة البحث...
            $table->json('meta')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('device', 20)->nullable();    // mobile / desktop
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_events');
    }
};
