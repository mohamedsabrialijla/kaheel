<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_order_files') && !Schema::hasColumn('kaheel_order_files', 'note')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->string('note', 500)->nullable()->after('size');
            });
        }

        if (Schema::hasTable('kaheel_orders') && !Schema::hasColumn('kaheel_orders', 'design_history')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->json('design_history')->nullable()->after('design_components');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kaheel_order_files', 'note')) {
            Schema::table('kaheel_order_files', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (Schema::hasColumn('kaheel_orders', 'design_history')) {
            Schema::table('kaheel_orders', function (Blueprint $table) {
                $table->dropColumn('design_history');
            });
        }
    }
};
