<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_order_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_order_messages', 'customer_seen_at')) {
                $table->timestamp('customer_seen_at')->nullable()->after('duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_order_messages', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_order_messages', 'customer_seen_at')) {
                $table->dropColumn('customer_seen_at');
            }
        });
    }
};
