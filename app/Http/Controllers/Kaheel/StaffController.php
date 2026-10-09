<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class StaffController extends Controller
{
    public function index()
    {
        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'catalog' => $this->catalog(),
        ], 200, 'items', '');
    }

    public function store(Request $request)
    {
        $name = trim((string) $request->input('name'));
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $role = $request->input('account_role') === 'admin' ? 'admin' : 'admin_staff';

        if ($name === '' || $email === '' || $password === '') {
            return mainResponse(false, 'Name, email and password are required', null, 422, 'items', '');
        }
        if (DB::table('admins')->where('email', $email)->exists()) {
            return mainResponse(false, 'Email already exists', null, 422, 'items', '');
        }

        $now = now();
        $adminId = DB::table('admins')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'mobile' => (string) $request->input('phone', ''),
            'job_title' => $request->input('job_title') ?: null,
            'department' => $request->input('department') ?: null,
            'account_role' => $role,
            'staff_sort' => ((int) DB::table('admins')->max('staff_sort')) + 1,
            'status' => 'active',
            'created_by' => auth('admin')->id(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->syncPermissions($adminId, (array) $request->input('permissions', []));
        $this->forget();

        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'catalog' => $this->catalog(),
        ], 200, 'items', '');
    }

    public function save(Request $request)
    {
        $admin = DB::table('admins')->where('id', $request->input('id'))->whereNotNull('staff_sort')->first();
        if (!$admin) {
            return mainResponse(false, 'Employee not found', null, 404, 'items', '');
        }
        if ($admin->account_role === 'admin') {
            return mainResponse(false, 'The system administrator cannot be changed', null, 422, 'items', '');
        }

        $role = $request->input('account_role') === 'admin' ? 'admin' : 'admin_staff';
        DB::table('admins')->where('id', $admin->id)->update([
            'account_role' => $role,
            'status' => $request->boolean('is_active') ? 'active' : 'not_active',
            'updated_at' => now(),
        ]);
        $this->syncPermissions($admin->id, (array) $request->input('permissions', []));
        $this->forget();

        return mainResponse(true, 'Success', [
            'staff' => $this->rows(),
            'catalog' => $this->catalog(),
        ], 200, 'items', '');
    }

    private function rows()
    {
        $codes = DB::table('model_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
            ->where('model_has_permissions.model_type', Admin::class)
            ->select('model_has_permissions.model_id', 'permissions.name')
            ->get()
            ->groupBy('model_id')
            ->map(fn ($rows) => $rows->pluck('name')->values()->all());

        return DB::table('admins')
            ->whereNotNull('staff_sort')
            ->whereNull('deleted_at')
            ->orderBy('staff_sort')
            ->get()
            ->map(fn ($admin) => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->mobile,
                'job_title' => $admin->job_title,
                'department' => $admin->department,
                'account_role' => $admin->account_role,
                'is_active' => $admin->status === 'active',
                'locked' => $admin->account_role === 'admin',
                'permissions' => $codes->get($admin->id, []),
            ])
            ->values();
    }

    private function catalog()
    {
        return DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereNotNull('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['name', 'display_name', 'label_en'])
            ->map(fn ($row) => [
                'code' => $row->name,
                'label_ar' => $row->display_name,
                'label_en' => $row->label_en,
            ])
            ->values();
    }

    private function syncPermissions(int $adminId, array $codes): void
    {
        $codes = array_values(array_unique(array_filter($codes)));
        $ids = DB::table('permissions')
            ->where('guard_name', 'admin')
            ->whereNotNull('category')
            ->whereIn('name', $codes)
            ->pluck('id');

        DB::table('model_has_permissions')
            ->where('model_id', $adminId)
            ->where('model_type', Admin::class)
            ->delete();

        foreach ($ids as $id) {
            DB::table('model_has_permissions')->insert([
                'permission_id' => $id,
                'model_type' => Admin::class,
                'model_id' => $adminId,
            ]);
        }
    }

    private function forget(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
