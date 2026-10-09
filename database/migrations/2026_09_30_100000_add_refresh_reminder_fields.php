<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'refresh_reminder_days')) {
                $table->unsignedInteger('refresh_reminder_days')->nullable()->default(0);
            }
        });

        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'refresh_reminded_at')) {
                $table->timestamp('refresh_reminded_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'refresh_reminded_at')) {
                $table->dropColumn('refresh_reminded_at');
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'refresh_reminder_days')) {
                $table->dropColumn('refresh_reminder_days');
            }
        });
    }
};
