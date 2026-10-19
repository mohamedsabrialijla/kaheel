<?php

use App\Support\KaheelPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $keep = KaheelPermissions::codes();

        foreach (KaheelPermissions::rows() as $row) {
            $exists = DB::table('permissions')
                ->where('name', $row['name'])
                ->where('guard_name', 'admin')
                ->exists();

            if ($exists) {
                DB::table('permissions')
                    ->where('name', $row['name'])
                    ->where('guard_name', 'admin')
                    ->update([
                        'display_name' => $row['display_name'],
                        'label_en' => $row['label_en'],
                        'category' => $row['category'],
                        'sort_order' => $row['sort_order'],
                        'updated_at' => $now,
                    ]);
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

        $obsolete = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->where(function ($query) {
                $query->whereNull('category')
                    ->orWhere('category', '!=', 'supplier');
            })
            ->whereNotIn('name', $keep)
            ->pluck('id');

        if ($obsolete->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $obsolete)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $obsolete)->delete();
            DB::table('permissions')->whereIn('id', $obsolete)->delete();
        }
    }

    public function down(): void
    {
        // Intentionally left empty: previous catalog was replaced for Kaheel sidebar modules.
    }
};
