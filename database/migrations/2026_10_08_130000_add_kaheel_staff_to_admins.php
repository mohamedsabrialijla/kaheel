<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'job_title')) {
                $table->string('job_title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('admins', 'department')) {
                $table->string('department')->nullable()->after('job_title');
            }
            if (!Schema::hasColumn('admins', 'account_role')) {
                $table->string('account_role', 40)->nullable()->after('department');
            }
            if (!Schema::hasColumn('admins', 'staff_sort')) {
                $table->unsignedInteger('staff_sort')->nullable()->after('account_role');
            }
        });

        DB::table('permissions')->where('name', 'staff.manage')->where('guard_name', 'admin')->update(['sort_order' => 22]);

        $extra = [
            ['supplier.orders.view', 'عرض الطلبات المسندة', 'View assigned orders', 19],
            ['supplier.orders.advance', 'تحديث مراحل الطلبات', 'Advance order stages', 21],
            ['supplier.staff.manage', 'إدارة مستخدمي المورد', 'Manage supplier users', 23],
            ['supplier.reports.view', 'عرض تقارير المورد', 'View supplier reports', 24],
        ];
        $now = now();
        foreach ($extra as $row) {
            $exists = DB::table('permissions')->where('name', $row[0])->where('guard_name', 'admin')->exists();
            if ($exists) {
                DB::table('permissions')->where('name', $row[0])->where('guard_name', 'admin')->update([
                    'display_name' => $row[1],
                    'label_en' => $row[2],
                    'category' => 'supplier',
                    'sort_order' => $row[3],
                    'updated_at' => $now,
                ]);
                continue;
            }
            DB::table('permissions')->insert([
                'name' => $row[0],
                'guard_name' => 'admin',
                'display_name' => $row[1],
                'label_en' => $row[2],
                'category' => 'supplier',
                'sort_order' => $row[3],
                'parent_id' => null,
                'admin' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $all = [
            'dashboard.view', 'orders.view', 'orders.advance', 'orders.assign_supplier', 'staff.view', 'staff.manage',
            'suppliers.view', 'suppliers.manage', 'settings.view', 'settings.manage', 'prices.manage', 'audit.view',
            'reports.view', 'finance.view', 'packages.manage', 'roles.manage', 'purchase.manage', 'notes.manage',
            'supplier.orders.view', 'supplier.orders.advance', 'supplier.staff.manage', 'supplier.reports.view',
        ];

        $people = [
            ['admin.demo@forma-kaheel.test', 'مدير النظام التجريبي', '+966 55 123 4003', 'مدير النظام التجريبي', 'admin', 'admin', 1, $all],
            ['design.demo@forma-kaheel.test', 'موظف التصميم التجريبي', '', 'موظف التصميم التجريبي', 'design', 'admin_staff', 2, ['dashboard.view', 'orders.view', 'orders.advance']],
            ['manager.demo@forma-kaheel.test', 'المدير التجريبي', '', 'المدير التجريبي', 'manager', 'admin_staff', 3, ['dashboard.view', 'orders.view', 'orders.advance', 'orders.assign_supplier', 'suppliers.view', 'audit.view']],
            ['ceo.demo@forma-kaheel.test', 'الرئيس التنفيذي التجريبي', '', 'الرئيس التنفيذي التجريبي', 'ceo', 'admin_staff', 4, ['dashboard.view', 'orders.view', 'staff.view', 'suppliers.view', 'settings.view', 'audit.view']],
            ['customer-service.demo@forma-kaheel.test', 'خدمة العملاء التجريبية', '', 'خدمة العملاء التجريبية', 'customer_service', 'admin_staff', 5, ['dashboard.view', 'orders.view']],
            ['finance.demo@forma-kaheel.test', 'موظف المالية التجريبي', '', 'موظف المالية التجريبي', 'finance', 'admin_staff', 6, ['dashboard.view', 'orders.view', 'prices.manage', 'settings.view']],
            ['execution.demo@forma-kaheel.test', 'فريق التنفيذ التجريبي', '', 'فريق التنفيذ التجريبي', 'execution', 'admin_staff', 7, ['dashboard.view', 'orders.view', 'orders.advance']],
            ['admin-staff.demo@forma-kaheel.test', 'موظف إدارة تجريبي', '', 'منسق طلبات', 'العمليات', 'admin_staff', 8, ['dashboard.view', 'orders.view', 'orders.advance']],
        ];

        $ids = DB::table('permissions')->where('guard_name', 'admin')->pluck('id', 'name');

        foreach ($people as $person) {
            $existing = DB::table('admins')->where('email', $person[0])->first();
            $values = [
                'name' => $person[1],
                'mobile' => $person[2],
                'job_title' => $person[3],
                'department' => $person[4],
                'account_role' => $person[5],
                'staff_sort' => $person[6],
                'status' => 'active',
                'updated_at' => $now,
            ];
            if ($existing) {
                DB::table('admins')->where('id', $existing->id)->update($values);
                $adminId = $existing->id;
            } else {
                $adminId = DB::table('admins')->insertGetId($values + [
                    'email' => $person[0],
                    'password' => Hash::make('KaheelDemo#2026!'),
                    'created_at' => $now,
                ]);
            }

            DB::table('model_has_permissions')->where('model_id', $adminId)->where('model_type', 'App\\Models\\Admin')->delete();
            foreach ($person[7] as $code) {
                if (!isset($ids[$code])) {
                    continue;
                }
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $ids[$code],
                    'model_type' => 'App\\Models\\Admin',
                    'model_id' => $adminId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            foreach (['staff_sort', 'account_role', 'department', 'job_title'] as $column) {
                if (Schema::hasColumn('admins', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
