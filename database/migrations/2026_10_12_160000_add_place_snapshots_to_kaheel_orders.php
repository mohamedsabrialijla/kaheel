<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_orders') && !Schema::hasColumn('kaheel_orders', 'compound_snapshot')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->json('compound_snapshot')->nullable()->after('compound_meta');
                $table->json('unit_snapshot')->nullable()->after('squar_meters');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kaheel_orders', 'unit_snapshot')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->dropColumn('unit_snapshot');
            });
        }
        if (Schema::hasColumn('kaheel_orders', 'compound_snapshot')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->dropColumn('compound_snapshot');
            });
        }
    }
};
