<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_alternatives')) {
            return;
        }

        Schema::create('kaheel_alternatives', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('price_delta', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['appliances', 'ثلاجة بوش بابين', 'Bosch double-door fridge', 1500, true],
            ['curtains', 'ستائر بلاك أوت مزدوجة', 'Double blackout curtains', 700, true],
            ['furniture', 'كنب جلد فاخر', 'Premium leather sofa', 3200, true],
            ['kitchen', 'رخام كوارتز للمطبخ', 'Quartz kitchen countertop', 2400, true],
            ['tv', 'تلفاز LG OLED 55 بوصة', 'LG OLED 55"', 2600, false],
            ['tv', 'تلفاز TCL 55 بوصة (الأساسي)', 'TCL 55" (base)', 0, false],
            ['tv', 'تلفاز سامسونج 65 بوصة', 'Samsung 65"', 1800, true],
            ['wardrobe', 'متر إضافي دولاب', 'Extra wardrobe meter', 950, true],
        ];

        foreach ($rows as $index => $row) {
            DB::table('kaheel_alternatives')->insert([
                'category' => $row[0],
                'name_ar' => $row[1],
                'name_en' => $row[2],
                'price_delta' => $row[3],
                'is_active' => $row[4],
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_alternatives');
    }
};
