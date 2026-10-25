<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\Admin;
use App\Support\KaheelPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'roles.view',
            'save' => $request->filled('id') ? 'roles.edit' : 'roles.create',
            'delete' => 'roles.delete',
            'assign' => 'staff.edit',
            default => null,
        };
    }

    public function index()
    {
        return mainResponse(true, 'Success', $this->payload(), 200, 'items', '');
    }

    public function save(Request $request)
    {
        $roleId = $request->input('id');
        $nameAr = trim((string) $request->input('name_ar'));
        $nameEn = trim((string) $request->input('name_en'));
        $allowed = KaheelPermissions::codes();
        $codes = array_values(array_unique(array_filter(
            (array) $request->input('permissions', []),
            fn ($code) => in_array($code, $allowed, true)
        )));

        if ($nameAr === '' || $nameEn === '') {
            return mainResponse(false, kaheel_msg('الاسم بالعربية والإنجليزية مطلوب.', 'Name is required.'), null, 422, 'items', '');
        }

        $permissionIds = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereIn('name', $codes)
            ->pluck('id', 'name');

        $now = now();
        $slug = $this->uniqueSlug($nameEn, $roleId);

        if ($roleId) {
            $role = DB::table('roles')->where('id', $roleId)->whereNotNull('name_ar')->first();
            if (!$role) {
                return mainResponse(false, kaheel_msg('الدور غير موجود.', 'Role not found.'), null, 404, 'items', '');
            }
            DB::table('roles')->where('id', $roleId)->update([
                'name' => $slug,
                'display_name' => $nameAr,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'is_active' => $request->boolean('is_active', true),
                'updated_at' => $now,
            ]);
        } else {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $slug,
                'guard_name' => 'admin',
                'display_name' => $nameAr,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
                'is_active' => $request->boolean('is_active', true),
                'order' => 0,
                'created_by' => auth('admin')->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
        foreach ($permissionIds as $id) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $id,
                'role_id' => $roleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->forget();

        return mainResponse(true, 'Success', $this->payload(), 200, 'items', '');
    }

    public function delete(Request $request)
    {
        $roleId = $request->input('id');
        $role = DB::table('roles')->where('id', $roleId)->whereNotNull('name_ar')->first();
        if (!$role) {
            return mainResponse(false, kaheel_msg('الدور غير موجود.', 'Role not found.'), null, 404, 'items', '');
        }

        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
        DB::table('model_has_roles')->where('role_id', $roleId)->delete();
        DB::table('admins')->where('role_id', $roleId)->update(['role_id' => null]);
        DB::table('roles')->where('id', $roleId)->delete();
        $this->forget();

        return mainResponse(true, 'Success', $this->payload(), 200, 'items', '');
    }

    public function assign(Request $request)
    {
        $admin = DB::table('admins')
            ->where('id', $request->input('user_id'))
            ->whereNotNull('staff_sort')
            ->whereNull('deleted_at')
            ->first();

        if (!$admin || $admin->account_role === 'admin') {
            return mainResponse(false, kaheel_msg('لا يمكن تعيين دور لهذا الحساب.', 'This account cannot be assigned.'), null, 422, 'items', '');
        }

        $roleId = $request->input('role_id') ?: null;
        if ($roleId && !DB::table('roles')->where('id', $roleId)->whereNotNull('name_ar')->where('is_active', true)->exists()) {
            return mainResponse(false, kaheel_msg('الدور غير موجود.', 'Role not found.'), null, 404, 'items', '');
        }

        $this->syncRole((int) $admin->id, $roleId ? (int) $roleId : null);
        $this->forget();

        return mainResponse(true, 'Success', $this->payload(), 200, 'items', '');
    }

    private function payload(): array
    {
        $permissionRows = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereIn('name', KaheelPermissions::codes())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'display_name', 'label_en', 'category', 'sort_order']);

        $modules = KaheelPermissions::groupedCatalog($permissionRows);
        $catalog = collect($modules)->flatMap(fn ($module) => collect($module['permissions'])->map(fn ($perm) => [
            'code' => $perm['code'],
            'label_ar' => $module['label_ar'].' — '.$perm['label_ar'],
            'label_en' => $module['label_en'].' — '.$perm['label_en'],
            'module' => $module['code'],
            'action' => $perm['action'],
        ]))->values();

        $codesByRole = DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->select('role_has_permissions.role_id', 'permissions.name')
            ->get()
            ->groupBy('role_id')
            ->map(fn ($rows) => $rows->pluck('name')->values());

        $members = DB::table('model_has_roles')
            ->select('role_id', DB::raw('count(*) as total'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        $roles = DB::table('roles')
            ->where('guard_name', 'admin')
            ->whereNotNull('name_ar')
            ->orderBy('id')
            ->get()
            ->map(fn ($role) => [
                'id' => $role->id,
                'name_ar' => $role->name_ar,
                'name_en' => $role->name_en,
                'is_active' => (bool) $role->is_active,
                'permissions' => $codesByRole->get($role->id, collect())->all(),
                'members' => (int) ($members[$role->id] ?? 0),
            ])
            ->values();

        return [
            'roles' => $roles,
            'modules' => $modules,
            'catalog' => $catalog,
        ];
    }

    private function syncRole(int $adminId, ?int $roleId): void
    {
        DB::table('model_has_roles')
            ->where('model_id', $adminId)
            ->where('model_type', Admin::class)
            ->delete();

        if ($roleId) {
            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => Admin::class,
                'model_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('admins')->where('id', $adminId)->update(['role_id' => $roleId]);
    }

    private function uniqueSlug(string $name, $ignoreId): string
    {
        $base = Str::slug($name) ?: 'role';
        $slug = $base;
        $i = 2;
        while (DB::table('roles')
            ->where('guard_name', 'admin')
            ->where('name', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function forget(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
