<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kaheel_orders')) {
            Schema::create('kaheel_orders', function (Blueprint $table) {
                $table->id();
                $table->string('number')->unique();
                $table->unsignedBigInteger('bricks_user_id')->index();
                $table->string('username');
                $table->string('customer_name')->nullable();
                $table->string('package_code');
                $table->string('package_name_ar');
                $table->string('package_name_en');
                $table->string('tier_name_ar');
                $table->string('tier_name_en');
                $table->string('compound_id')->nullable();
                $table->string('compound_name');
                $table->string('compound_meta')->nullable();
                $table->string('unit_id')->nullable();
                $table->string('unit_no');
                $table->unsignedInteger('bedrooms_number')->nullable();
                $table->decimal('squar_meters', 10, 2)->nullable();
                $table->decimal('price_excl', 14, 2);
                $table->decimal('price_incl', 14, 2);
                $table->decimal('deposit_percent', 5, 2);
                $table->decimal('deposit_amount', 14, 2);
                $table->longText('contract_body');
                $table->longText('signature')->nullable();
                $table->string('status')->default('deposit_pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kaheel_order_files')) {
            Schema::create('kaheel_order_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('kaheel_orders')->cascadeOnDelete();
                $table->string('original_name');
                $table->string('path');
                $table->string('mime')->nullable();
                $table->unsignedInteger('size')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_order_files');
        Schema::dropIfExists('kaheel_orders');
    }
};
