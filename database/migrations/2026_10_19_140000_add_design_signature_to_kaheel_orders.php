<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'design_signature')) {
                $table->longText('design_signature')->nullable()->after('design_components');
            }
            if (!Schema::hasColumn('kaheel_orders', 'design_signed_at')) {
                $table->timestamp('design_signed_at')->nullable()->after('design_signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_orders', 'design_signed_at')) {
                $table->dropColumn('design_signed_at');
            }
            if (Schema::hasColumn('kaheel_orders', 'design_signature')) {
                $table->dropColumn('design_signature');
            }
        });
    }
};
