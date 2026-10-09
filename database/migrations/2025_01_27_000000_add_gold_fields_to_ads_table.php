<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'piece_id')) {
                $table->unsignedBigInteger('piece_id')->nullable()->after('brand_id');
            }
            if (!Schema::hasColumn('ads', 'metal_id')) {
                $table->unsignedBigInteger('metal_id')->nullable()->after('piece_id');
            }
            if (!Schema::hasColumn('ads', 'caliber_id')) {
                $table->unsignedBigInteger('caliber_id')->nullable()->after('metal_id');
            }
            if (!Schema::hasColumn('ads', 'weight')) {
                $table->string('weight', 50)->nullable()->after('caliber_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'piece_id')) $table->dropColumn('piece_id');
            if (Schema::hasColumn('ads', 'metal_id')) $table->dropColumn('metal_id');
            if (Schema::hasColumn('ads', 'caliber_id')) $table->dropColumn('caliber_id');
            if (Schema::hasColumn('ads', 'weight')) $table->dropColumn('weight');
        });
    }
};
