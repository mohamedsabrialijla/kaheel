<?php

use App\Support\KaheelPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kaheel_inquiries')) {
            Schema::create('kaheel_inquiries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 40);
                $table->string('customer_type', 32);
                $table->text('message');
                $table->timestamps();
            });
        }

        $now = now();
        foreach (KaheelPermissions::rows() as $row) {
            if (!str_starts_with($row['name'], 'inquiries.')) {
                continue;
            }
            $exists = DB::table('permissions')
                ->where('name', $row['name'])
                ->where('guard_name', 'admin')
                ->exists();
            if ($exists) {
                continue;
            }
            DB::table('permissions')->insert([
                'name' => $row['name'],
                'guard_name' => 'admin',
                'display_name' => $row['display_name'],
                'label_en' => $row['label_en'],
                'category' => $row['category'],
                'sort_order' => $row['sort_order'],
                'parent_id' => null,
                'admin' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_inquiries');
    }
};
