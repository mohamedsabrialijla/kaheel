<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaheel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('kaheel_orders', 'design_status')) {
                $table->string('design_status')->default('none')->after('status');
            }
            if (!Schema::hasColumn('kaheel_orders', 'addons_total')) {
                $table->decimal('addons_total', 14, 2)->default(0)->after('deposit_amount');
            }
            if (!Schema::hasColumn('kaheel_orders', 'design_components')) {
                $table->json('design_components')->nullable()->after('signature');
            }
            if (!Schema::hasColumn('kaheel_orders', 'supplier_assignments')) {
                $table->json('supplier_assignments')->nullable()->after('design_components');
            }
        });

        DB::table('kaheel_orders')->where('status', 'awaiting_approval')->update(['status' => 'admin_review']);

        if (!Schema::hasTable('kaheel_order_events')) {
            Schema::create('kaheel_order_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('kaheel_orders')->cascadeOnDelete();
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->string('note')->nullable();
                $table->string('actor_name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kaheel_order_messages')) {
            Schema::create('kaheel_order_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('kaheel_orders')->cascadeOnDelete();
                $table->string('sender_role')->default('admin');
                $table->string('sender_name')->nullable();
                $table->text('body');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_order_messages');
        Schema::dropIfExists('kaheel_order_events');
    }
};
