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
        Schema::create('ratings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('ad_id')->constrained('ads')->onDelete('cascade');
    $table->tinyInteger('rating'); // من 1 إلى 5 مثلاً
    $table->text('comment')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'ad_id']); // كل مستخدم يقيّم الإعلان مرة واحدة فقط
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
