<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kaheel_package_images')) {
            Schema::create('kaheel_package_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('kaheel_packages')->cascadeOnDelete();
                $table->string('path');
                $table->string('original_name')->nullable();
                $table->string('mime')->nullable();
                $table->string('caption_ar')->nullable();
                $table->string('caption_en')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (DB::table('kaheel_package_images')->count() > 0) {
            return;
        }

        $sets = [
            'VIP' => [
                ['file' => 'vip-living.jpg', 'ar' => 'غرفة الطعام والمعيشة', 'en' => 'Dining & living'],
                ['file' => 'vip-kitchen.jpg', 'ar' => 'المطبخ ومنطقة الطعام', 'en' => 'Kitchen & dining'],
                ['file' => 'vip-master.jpg', 'ar' => 'غرفة النوم الرئيسية', 'en' => 'Master bedroom'],
                ['file' => 'vip-wardrobes.jpg', 'ar' => 'تفاصيل غرفة النوم والدواليب', 'en' => 'Master · wardrobes'],
                ['file' => 'vip-bedroom.jpg', 'ar' => 'غرفة النوم الثانية', 'en' => 'Second bedroom'],
                ['file' => 'vip-detail.jpg', 'ar' => 'تفاصيل جانب السرير', 'en' => 'Bedside detail'],
            ],
            'CLASSIC' => [
                ['file' => 'classic-living.jpg', 'ar' => 'غرفة المعيشة والطعام', 'en' => 'Living & dining'],
                ['file' => 'classic-kitchen.jpg', 'ar' => 'المطبخ', 'en' => 'Kitchen'],
                ['file' => 'classic-bedroom.jpg', 'ar' => 'غرفة النوم', 'en' => 'Bedroom'],
                ['file' => 'classic-kitchen-detail.jpg', 'ar' => 'تفاصيل المطبخ', 'en' => 'Kitchen · detail'],
                ['file' => 'classic-living-detail.jpg', 'ar' => 'تفاصيل غرفة المعيشة', 'en' => 'Living · detail'],
            ],
        ];

        $now = now();
        foreach ($sets as $code => $shots) {
            $packageId = DB::table('kaheel_packages')->where('code', $code)->value('id');
            if (!$packageId) {
                continue;
            }
            foreach ($shots as $index => $shot) {
                DB::table('kaheel_package_images')->insert([
                    'package_id' => $packageId,
                    'path' => 'public:'.$shot['file'],
                    'original_name' => $shot['file'],
                    'mime' => 'image/jpeg',
                    'caption_ar' => $shot['ar'],
                    'caption_en' => $shot['en'],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_package_images');
    }
};
