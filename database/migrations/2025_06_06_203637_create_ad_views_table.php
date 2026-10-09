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
        Schema::create('ad_views', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ad_id')->constrained()->onDelete('cascade');
    $table->unsignedBigInteger('user_id')->nullable(); // إن وجد
    $table->string('ip')->nullable(); // للزائر
    $table->timestamps();

    $table->unique(['ad_id', 'user_id']); // لمنع تكرار المشاهدة من نفس المستخدم
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_views');
    }
};
