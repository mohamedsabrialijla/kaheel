<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'birth_year')) {
                $table->unsignedSmallInteger('birth_year')->nullable();
            }
            if (!Schema::hasColumn('users', 'birth_month')) {
                $table->unsignedTinyInteger('birth_month')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'birth_month')) {
                $table->dropColumn('birth_month');
            }
            if (Schema::hasColumn('users', 'birth_year')) {
                $table->dropColumn('birth_year');
            }
        });
    }
};
