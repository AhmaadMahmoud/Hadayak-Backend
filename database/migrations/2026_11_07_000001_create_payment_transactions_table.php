<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('paymob');
            $table->string('transaction_id')->index();
            $table->unsignedBigInteger('amount_cents')->default(0);
            $table->boolean('success')->default(false);
            $table->boolean('is_refund')->default(false);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
