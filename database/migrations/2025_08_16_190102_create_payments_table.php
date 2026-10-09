<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('track_id')->unique();   // معرف العملية المرسل ل URWAY
            $table->string('payment_id')->nullable(); // PaymentId من URWAY
            $table->unsignedBigInteger('ad_id')->nullable(); // الإعلان المرتبط
            $table->decimal('amount', 10, 2);       // الكمية المدفوعة
            $table->string('currency')->default('SAR');
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->json('raw_response')->nullable(); // نخزن رد URWAY كـ JSON كامل
            $table->timestamps();

            // لو عندك جدول ads
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
