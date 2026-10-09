<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type', 50)->nullable()->after('body');
            }
            if (!Schema::hasColumn('notifications', 'ad_id')) {
                $table->unsignedBigInteger('ad_id')->nullable()->after('type');
            }
            if (!Schema::hasColumn('notifications', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable()->after('ad_id');
            }
        });

        if (Schema::hasTable('notification_user')) {
            Schema::table('notification_user', function (Blueprint $table) {
                $table->index(['user_id', 'read'], 'notification_user_user_read_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notification_user')) {
            Schema::table('notification_user', function (Blueprint $table) {
                $table->dropIndex('notification_user_user_read_index');
            });
        }

        Schema::table('notifications', function (Blueprint $table) {
            foreach (['sender_id', 'ad_id', 'type'] as $column) {
                if (Schema::hasColumn('notifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
