<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_settings')) {
            return;
        }

        Schema::create('kaheel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label_ar');
            $table->string('label_en');
            $table->text('value')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['contact_phone_primary', 'رقم التواصل الرئيسي', 'Primary contact phone', '+966 59 920 2222', 1],
            ['contact_phone_secondary', 'رقم التواصل الإضافي', 'Secondary contact phone', '+966 53 592 0450', 2],
            ['delivery_working_days', 'مدة التسليم بأيام العمل', 'Delivery working days', '25', 3],
            ['deposit_percent', 'نسبة العربون %', 'Deposit percentage %', '25', 4],
        ];

        foreach ($rows as $row) {
            DB::table('kaheel_settings')->insert([
                'key' => $row[0],
                'label_ar' => $row[1],
                'label_en' => $row[2],
                'value' => $row[3],
                'sort_order' => $row[4],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_settings');
    }
};
