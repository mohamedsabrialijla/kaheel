<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'national_id')) {
                $table->string('national_id', 20)->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'nafath_verified')) {
                $table->boolean('nafath_verified')->default(false)->after('national_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'national_id')) $table->dropColumn('national_id');
            if (Schema::hasColumn('users', 'nafath_verified')) $table->dropColumn('nafath_verified');
        });
    }
};
