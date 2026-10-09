<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kaheel_suppliers')) {
            return;
        }

        Schema::create('kaheel_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['مصنع كحيل للأثاث', 'Kaheel Furniture Works', 'مورد كحيل التجريبي', null, 'supplier.demo@forma-kaheel.test', 7.5, 0],
            ['نجّار الشرق للأثاث', 'East Carpenter Furniture', 'نجّار الشرق للأثاث', '+966524615526', 'supplier.east@forma-kaheel.test', 6, 1],
            ['بيت التصميم للتجهيزات', 'Design House Fit-out', 'بيت التصميم للتجهيزات', '+966552908630', 'supplier.house@forma-kaheel.test', 8, 2],
            ['الرواد للمطابخ والدواليب', 'Rowad Kitchens & Wardrobes', 'الرواد للمطابخ والدواليب', '+966531085994', 'supplier.rowad@forma-kaheel.test', 9, 3],
        ];

        foreach ($rows as $row) {
            DB::table('kaheel_suppliers')->insert([
                'name_ar' => $row[0],
                'name_en' => $row[1],
                'contact_name' => $row[2],
                'phone' => $row[3],
                'email' => $row[4],
                'password' => Hash::make(Str::random(32)),
                'commission_rate' => $row[5],
                'is_active' => true,
                'created_at' => $now->copy()->addSeconds($row[6]),
                'updated_at' => $now->copy()->addSeconds($row[6]),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_suppliers');
    }
};
