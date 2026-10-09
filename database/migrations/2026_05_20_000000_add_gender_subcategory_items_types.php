<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (['gender', 'subcategory'] as $name) {
            $exists = DB::table('items_types')->where('name', $name)->exists();
            if (!$exists) {
                DB::table('items_types')->insert([
                    'name' => $name,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('items_types')->whereIn('name', ['gender', 'subcategory'])->delete();
    }
};
