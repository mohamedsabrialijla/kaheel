<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_package_items', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_package_items', 'detail_ar')) {
                $table->text('detail_ar')->nullable()->after('product_name_en');
            }
            if (!Schema::hasColumn('kaheel_package_items', 'detail_en')) {
                $table->text('detail_en')->nullable()->after('detail_ar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_package_items', function (Blueprint $table) {
            if (Schema::hasColumn('kaheel_package_items', 'detail_en')) {
                $table->dropColumn('detail_en');
            }
            if (Schema::hasColumn('kaheel_package_items', 'detail_ar')) {
                $table->dropColumn('detail_ar');
            }
        });
    }
};
