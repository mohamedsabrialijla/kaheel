<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_order_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_order_messages', 'kind')) {
                $table->string('kind')->default('text')->after('sender_name');
            }
            if (!Schema::hasColumn('kaheel_order_messages', 'media_path')) {
                $table->string('media_path')->nullable()->after('body');
            }
            if (!Schema::hasColumn('kaheel_order_messages', 'media_mime')) {
                $table->string('media_mime')->nullable()->after('media_path');
            }
            if (!Schema::hasColumn('kaheel_order_messages', 'media_name')) {
                $table->string('media_name')->nullable()->after('media_mime');
            }
            if (!Schema::hasColumn('kaheel_order_messages', 'duration')) {
                $table->unsignedInteger('duration')->nullable()->after('media_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kaheel_order_messages', function (Blueprint $table) {
            foreach (['duration', 'media_name', 'media_mime', 'media_path', 'kind'] as $column) {
                if (Schema::hasColumn('kaheel_order_messages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
