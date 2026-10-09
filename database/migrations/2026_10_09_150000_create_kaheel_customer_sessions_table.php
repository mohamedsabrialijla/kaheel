<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_customer_sessions')) {
            return;
        }

        Schema::create('kaheel_customer_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 128)->unique();
            $table->unsignedBigInteger('bricks_user_id')->nullable();
            $table->string('username');
            $table->string('name')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->text('auth_token');
            $table->string('slug', 40);
            $table->json('units')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_customer_sessions');
    }
};
