<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kaheel_categories')) {
            Schema::create('kaheel_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code', 40)->unique();
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('unit_label_ar')->default('قطعة');
                $table->string('unit_label_en')->default('piece');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $now = now();
        $rows = [
            ['furniture', 'الأثاث', 'Furniture', 'قطعة', 'piece'],
            ['kitchen', 'المطبخ', 'Kitchen', 'متر طولي', 'linear meter'],
            ['wardrobe', 'الدواليب', 'Wardrobes', 'متر طولي', 'linear meter'],
            ['appliances', 'الأجهزة', 'Appliances', 'جهاز', 'unit'],
            ['tv', 'الشاشات والتلفزيون', 'TV & screens', 'جهاز', 'unit'],
            ['curtains', 'الستائر', 'Curtains', 'متر', 'meter'],
            ['accessories', 'الإكسسوارات', 'Accessories', 'قطعة', 'piece'],
            ['soft', 'المفروشات', 'Soft furnishings', 'قطعة', 'piece'],
            ['lighting', 'الإنارة', 'Lighting', 'نقطة', 'point'],
            ['general', 'عام', 'General', 'قطعة', 'piece'],
        ];

        foreach ($rows as $index => $row) {
            if (DB::table('kaheel_categories')->where('code', $row[0])->exists()) {
                continue;
            }
            DB::table('kaheel_categories')->insert([
                'code' => $row[0],
                'name_ar' => $row[1],
                'name_en' => $row[2],
                'unit_label_ar' => $row[3],
                'unit_label_en' => $row[4],
                'sort_order' => ($index + 1) * 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_categories');
    }
};
