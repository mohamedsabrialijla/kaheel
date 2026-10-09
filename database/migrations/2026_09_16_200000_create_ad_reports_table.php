<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_reports', function (Blueprint $table) {
            $table->id();
            // بدون foreign keys: users.id و ads.id من نوع int والجداول MyISAM
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id')->index();
            $table->text('message');
            $table->timestamps();

            $table->unique(['user_id', 'ad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_reports');
    }
};
