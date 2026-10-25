<?php

namespace App\Support;

class KaheelPermissions
{
    public static function modules(): array
    {
        return [
            'overview' => [
                'label_ar' => 'نظرة عامة',
                'label_en' => 'Overview',
                'actions' => ['view'],
            ],
            'orders' => [
                'label_ar' => 'الطلبات',
                'label_en' => 'Orders',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'staff' => [
                'label_ar' => 'الموظفون',
                'label_en' => 'Staff',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'suppliers' => [
                'label_ar' => 'الموردون',
                'label_en' => 'Suppliers',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'packages' => [
                'label_ar' => 'الباقات',
                'label_en' => 'Packages',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'alternatives' => [
                'label_ar' => 'البدائل والأسعار',
                'label_en' => 'Alternatives',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'roles' => [
                'label_ar' => 'الأدوار والصلاحيات',
                'label_en' => 'Roles and permissions',
                'actions' => ['view', 'create', 'edit', 'delete'],
            ],
            'settings' => [
                'label_ar' => 'الإعدادات',
                'label_en' => 'Settings',
                'actions' => ['view', 'edit'],
            ],
        ];
    }

    public static function actions(): array
    {
        return [
            'view' => ['label_ar' => 'عرض', 'label_en' => 'View'],
            'create' => ['label_ar' => 'إضافة', 'label_en' => 'Create'],
            'edit' => ['label_ar' => 'تعديل', 'label_en' => 'Edit'],
            'delete' => ['label_ar' => 'حذف', 'label_en' => 'Delete'],
        ];
    }

    public static function codes(): array
    {
        $codes = [];
        foreach (self::modules() as $module => $meta) {
            foreach ($meta['actions'] as $action) {
                $codes[] = $module.'.'.$action;
            }
        }

        return $codes;
    }

    public static function rows(): array
    {
        $actions = self::actions();
        $rows = [];
        $sort = 10;
        foreach (self::modules() as $module => $meta) {
            foreach ($meta['actions'] as $action) {
                $actionMeta = $actions[$action];
                $rows[] = [
                    'name' => $module.'.'.$action,
                    'display_name' => $meta['label_ar'].' — '.$actionMeta['label_ar'],
                    'label_en' => $meta['label_en'].' — '.$actionMeta['label_en'],
                    'category' => $module,
                    'sort_order' => $sort,
                ];
                $sort += 10;
            }
        }

        return $rows;
    }

    public static function isFullAdmin($admin): bool
    {
        if (!$admin) {
            return false;
        }

        $accountRole = $admin->account_role ?? null;

        return $accountRole === 'admin'
            || (empty($admin->staff_sort) && $accountRole !== 'admin_staff');
    }

    public static function codesFor($admin): array
    {
        if (!$admin) {
            return [];
        }
        if (self::isFullAdmin($admin)) {
            return self::codes();
        }

        $codes = $admin->role ?? [];

        return is_array($codes) ? array_values($codes) : [];
    }

    public static function allows($admin, string $code): bool
    {
        if ($code === '' || !$admin) {
            return false;
        }
        if (self::isFullAdmin($admin)) {
            return true;
        }

        return in_array($code, self::codesFor($admin), true);
    }

    public static function denyUnless(string $code)
    {
        if (self::allows(auth('admin')->user(), $code)) {
            return null;
        }

        return mainResponse(false, kaheel_msg('لا تملك صلاحية لهذا الإجراء.', 'You do not have permission for this action.'), null, 403, 'items', '');
    }

    public static function groupedCatalog(iterable $permissions): array
    {
        $byCode = collect($permissions)->keyBy('name');
        $actions = self::actions();
        $modules = [];

        foreach (self::modules() as $module => $meta) {
            $items = [];
            foreach ($meta['actions'] as $action) {
                $code = $module.'.'.$action;
                $row = $byCode->get($code);
                if (!$row) {
                    continue;
                }
                $items[] = [
                    'code' => $code,
                    'action' => $action,
                    'label_ar' => $actions[$action]['label_ar'],
                    'label_en' => $actions[$action]['label_en'],
                ];
            }
            if (!$items) {
                continue;
            }
            $modules[] = [
                'code' => $module,
                'label_ar' => $meta['label_ar'],
                'label_en' => $meta['label_en'],
                'permissions' => $items,
            ];
        }

        return $modules;
    }
}
