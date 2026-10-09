<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'label_en')) {
                $table->string('label_en')->nullable()->after('display_name');
            }
            if (!Schema::hasColumn('permissions', 'category')) {
                $table->string('category')->nullable()->after('label_en');
            }
            if (!Schema::hasColumn('permissions', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('category');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('display_name');
            }
            if (!Schema::hasColumn('roles', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
            if (!Schema::hasColumn('roles', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('name_en');
            }
        });

        $now = now();
        $catalog = [
            ['dashboard.view', 'عرض لوحة الإدارة', 'View dashboard', 'dashboard', 1],
            ['orders.view', 'عرض جميع الطلبات', 'View all orders', 'orders', 10],
            ['orders.advance', 'تحديث مراحل الطلبات', 'Advance order stages', 'orders', 11],
            ['orders.assign_supplier', 'إسناد الموردين', 'Assign suppliers', 'orders', 12],
            ['reports.view', 'عرض التقارير والمؤشرات', 'View reports and analytics', 'reports', 13],
            ['finance.view', 'عرض البيانات المالية والتكاليف', 'View financials and costs', 'reports', 14],
            ['packages.manage', 'إدارة الباقات', 'Manage packages', 'catalog', 15],
            ['roles.manage', 'إدارة الأدوار والصلاحيات', 'Manage roles and permissions', 'staff', 16],
            ['purchase.manage', 'إدارة أوامر الشراء', 'Manage purchase orders', 'orders', 17],
            ['notes.manage', 'الملاحظات الداخلية', 'Internal notes', 'orders', 18],
            ['staff.view', 'عرض الموظفين', 'View staff', 'staff', 20],
            ['staff.manage', 'إنشاء وإدارة الموظفين', 'Manage staff', 'staff', 21],
            ['suppliers.view', 'عرض الموردين', 'View suppliers', 'suppliers', 30],
            ['suppliers.manage', 'إنشاء وإدارة الموردين', 'Manage suppliers', 'suppliers', 31],
            ['settings.view', 'عرض الإعدادات', 'View settings', 'settings', 40],
            ['settings.manage', 'تعديل الإعدادات', 'Manage settings', 'settings', 41],
            ['prices.manage', 'إدارة الأسعار', 'Manage prices', 'settings', 42],
            ['audit.view', 'عرض سجل الإدارة', 'View audit log', 'audit', 50],
        ];

        foreach ($catalog as $row) {
            $exists = DB::table('permissions')->where('name', $row[0])->where('guard_name', 'admin')->exists();
            if ($exists) {
                DB::table('permissions')->where('name', $row[0])->where('guard_name', 'admin')->update([
                    'display_name' => $row[1],
                    'label_en' => $row[2],
                    'category' => $row[3],
                    'sort_order' => $row[4],
                    'updated_at' => $now,
                ]);
                continue;
            }

            DB::table('permissions')->insert([
                'name' => $row[0],
                'guard_name' => 'admin',
                'display_name' => $row[1],
                'label_en' => $row[2],
                'category' => $row[3],
                'sort_order' => $row[4],
                'parent_id' => null,
                'admin' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->where('guard_name', 'admin')->whereNotNull('category')->delete();

        Schema::table('permissions', function (Blueprint $table) {
            foreach (['sort_order', 'category', 'label_en'] as $column) {
                if (Schema::hasColumn('permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            foreach (['is_active', 'name_en', 'name_ar'] as $column) {
                if (Schema::hasColumn('roles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
