<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'final_payment_method')) {
                $table->string('final_payment_method')->nullable()->after('final_payment_status');
            }
            if (!Schema::hasColumn('kaheel_orders', 'final_payment_note')) {
                $table->text('final_payment_note')->nullable()->after('final_payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_orders', 'final_payment_note')) {
                $table->dropColumn('final_payment_note');
            }
            if (Schema::hasColumn('kaheel_orders', 'final_payment_method')) {
                $table->dropColumn('final_payment_method');
            }
        });
    }
};
