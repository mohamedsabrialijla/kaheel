<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'alternatives_amount')) {
                $table->decimal('alternatives_amount', 14, 2)->default(0)->after('addons_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_orders', 'alternatives_amount')) {
                $table->dropColumn('alternatives_amount');
            }
        });
    }
};
