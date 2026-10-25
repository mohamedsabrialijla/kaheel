<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class StaffController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'staff.view',
            'store' => 'staff.create',
            'save' => 'staff.edit',
            default => null,
        };
    }

    public function index()
    {
        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'roles' => $this->roles(),
        ], 200, 'items', '');
    }

    public function store(Request $request)
    {
        $name = trim((string) $request->input('name'));
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $roleId = $request->input('role_id') ? (int) $request->input('role_id') : null;

        if ($name === '' || $email === '' || $password === '') {
            return mainResponse(false, kaheel_msg('الاسم والبريد وكلمة المرور مطلوبة.', 'Name, email and password are required.'), null, 422, 'items', '');
        }
        if (DB::table('admins')->where('email', $email)->exists()) {
            return mainResponse(false, kaheel_msg('البريد الإلكتروني مستخدم مسبقاً.', 'Email already exists.'), null, 422, 'items', '');
        }
        if ($roleId && !$this->activeRole($roleId)) {
            return mainResponse(false, kaheel_msg('الدور غير موجود.', 'Role not found.'), null, 422, 'items', '');
        }

        $now = now();
        $adminId = DB::table('admins')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'mobile' => (string) $request->input('phone', ''),
            'job_title' => $request->input('job_title') ?: null,
            'department' => $request->input('department') ?: null,
            'account_role' => 'admin_staff',
            'role_id' => $roleId,
            'staff_sort' => ((int) DB::table('admins')->max('staff_sort')) + 1,
            'status' => 'active',
            'created_by' => auth('admin')->id(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->syncRole($adminId, $roleId);
        $this->forget();

        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'roles' => $this->roles(),
        ], 200, 'items', '');
    }

    public function save(Request $request)
    {
        $admin = DB::table('admins')->where('id', $request->input('id'))->whereNotNull('staff_sort')->first();
        if (!$admin) {
            return mainResponse(false, kaheel_msg('الموظف غير موجود.', 'Employee not found.'), null, 404, 'items', '');
        }
        if ($admin->account_role === 'admin') {
            return mainResponse(false, kaheel_msg('لا يمكن تعديل مدير النظام.', 'The system administrator cannot be changed.'), null, 422, 'items', '');
        }

        $name = trim((string) $request->input('name', $admin->name));
        $email = trim((string) $request->input('email', $admin->email));
        $password = (string) $request->input('password', '');
        $roleId = $request->input('role_id') ? (int) $request->input('role_id') : null;

        if ($name === '' || $email === '') {
            return mainResponse(false, kaheel_msg('الاسم والبريد مطلوبان.', 'Name and email are required.'), null, 422, 'items', '');
        }
        if (DB::table('admins')->where('email', $email)->where('id', '!=', $admin->id)->exists()) {
            return mainResponse(false, kaheel_msg('البريد الإلكتروني مستخدم مسبقاً.', 'Email already exists.'), null, 422, 'items', '');
        }
        if ($roleId && !$this->activeRole($roleId)) {
            return mainResponse(false, kaheel_msg('الدور غير موجود.', 'Role not found.'), null, 422, 'items', '');
        }

        $values = [
            'name' => $name,
            'email' => $email,
            'mobile' => $request->has('phone') ? (string) $request->input('phone') : $admin->mobile,
            'job_title' => $request->has('job_title') ? ($request->input('job_title') ?: null) : $admin->job_title,
            'department' => $request->has('department') ? ($request->input('department') ?: null) : $admin->department,
            'role_id' => $roleId,
            'status' => $request->boolean('is_active') ? 'active' : 'not_active',
            'updated_at' => now(),
        ];
        if ($password !== '') {
            $values['password'] = Hash::make($password);
        }

        DB::table('admins')->where('id', $admin->id)->update($values);
        $this->syncRole((int) $admin->id, $roleId);
        $this->forget();

        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'roles' => $this->roles(),
        ], 200, 'items', '');
    }

    private function rows()
    {
        $roles = DB::table('roles')->whereNotNull('name_ar')->get(['id', 'name_ar', 'name_en'])->keyBy('id');
        $assigned = DB::table('model_has_roles')
            ->where('model_type', Admin::class)
            ->pluck('role_id', 'model_id');

        return DB::table('admins')
            ->whereNotNull('staff_sort')
            ->whereNull('deleted_at')
            ->orderBy('staff_sort')
            ->get()
            ->map(function ($admin) use ($roles, $assigned) {
                $roleId = $assigned[$admin->id] ?? $admin->role_id;
                $role = $roleId ? $roles->get($roleId) : null;

                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'phone' => $admin->mobile,
                    'job_title' => $admin->job_title,
                    'department' => $admin->department,
                    'account_role' => $admin->account_role,
                    'role_id' => $roleId,
                    'role_name_ar' => $role->name_ar ?? null,
                    'role_name_en' => $role->name_en ?? null,
                    'is_active' => $admin->status === 'active',
                    'locked' => $admin->account_role === 'admin',
                ];
            })
            ->values();
    }

    private function roles()
    {
        return DB::table('roles')
            ->where('guard_name', 'admin')
            ->whereNotNull('name_ar')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name_ar', 'name_en'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'name_ar' => $role->name_ar,
                'name_en' => $role->name_en,
            ])
            ->values();
    }

    private function activeRole(int $roleId): bool
    {
        return DB::table('roles')
            ->where('id', $roleId)
            ->where('guard_name', 'admin')
            ->whereNotNull('name_ar')
            ->where('is_active', true)
            ->exists();
    }

    private function syncRole(int $adminId, ?int $roleId): void
    {
        DB::table('model_has_roles')
            ->where('model_id', $adminId)
            ->where('model_type', Admin::class)
            ->delete();

        DB::table('model_has_permissions')
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

    private function forget(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
