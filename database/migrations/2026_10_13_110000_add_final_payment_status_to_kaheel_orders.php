<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'final_payment_status')) {
                $table->string('final_payment_status')->nullable()->after('payment_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_orders', 'final_payment_status')) {
                $table->dropColumn('final_payment_status');
            }
        });
    }
};
