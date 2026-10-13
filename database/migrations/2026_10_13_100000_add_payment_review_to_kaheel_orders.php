<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('kaheel_orders', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('payment_status');
            }
        });

        DB::table('kaheel_orders')
            ->whereNotNull('payment_method')
            ->whereNull('payment_status')
            ->update(['payment_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_orders', 'payment_note')) {
                $table->dropColumn('payment_note');
            }
            if (Schema::hasColumn('kaheel_orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
