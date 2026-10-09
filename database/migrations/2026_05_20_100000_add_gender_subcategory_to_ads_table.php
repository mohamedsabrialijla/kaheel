<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'gender_id')) {
                $table->unsignedBigInteger('gender_id')->nullable()->after('brand_id');
            }
            if (!Schema::hasColumn('ads', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('gender_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'subcategory_id')) {
                $table->dropColumn('subcategory_id');
            }
            if (Schema::hasColumn('ads', 'gender_id')) {
                $table->dropColumn('gender_id');
            }
        });
    }
};
