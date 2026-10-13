<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_orders') && !Schema::hasColumn('kaheel_orders', 'payment_method')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->string('payment_method')->nullable()->after('status');
            });
        }

        if (Schema::hasTable('kaheel_order_files') && !Schema::hasColumn('kaheel_order_files', 'kind')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->string('kind')->default('plan')->after('order_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kaheel_orders', 'payment_method')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }

        if (Schema::hasColumn('kaheel_order_files', 'kind')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->dropColumn('kind');
            });
        }
    }
};
