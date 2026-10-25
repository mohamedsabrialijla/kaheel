<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelAlternative;
use App\Models\KaheelCategory;
use App\Models\KaheelOrder;
use App\Models\KaheelPackageItem;
use Illuminate\Http\Request;

class CategoryController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'alternatives.view',
            'save' => 'alternatives.create',
            'delete' => 'alternatives.delete',
            default => null,
        };
    }

    public function index()
    {
        return mainResponse(true, 'Success', KaheelCategory::catalog(), 200, 'items', '');
    }

    public function save(Request $request)
    {
        $nameAr = trim((string) $request->input('name_ar'));
        $nameEn = trim((string) $request->input('name_en'));
        $isAr = app()->getLocale() !== 'en';

        if ($nameAr === '' || $nameEn === '') {
            return mainResponse(false, $isAr ? 'أدخل اسم التصنيف بالعربية والإنجليزية.' : 'Enter the category name in Arabic and English.', null, 422, 'items', '');
        }

        $exists = KaheelCategory::query()
            ->where(function ($query) use ($nameAr, $nameEn) {
                $query->where('name_ar', $nameAr)->orWhere('name_en', $nameEn);
            })
            ->exists();
        if ($exists) {
            return mainResponse(false, $isAr ? 'هذا التصنيف موجود مسبقاً.' : 'This category already exists.', null, 422, 'items', '');
        }

        KaheelCategory::query()->create([
            'code' => KaheelCategory::makeCode($nameEn, $nameAr),
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'unit_label_ar' => trim((string) $request->input('unit_label_ar')) ?: 'قطعة',
            'unit_label_en' => trim((string) $request->input('unit_label_en')) ?: 'piece',
            'sort_order' => ((int) KaheelCategory::query()->max('sort_order')) + 10,
            'is_active' => true,
        ]);

        return mainResponse(true, $isAr ? 'تم إضافة التصنيف.' : 'Category added.', KaheelCategory::catalog(), 200, 'items', '');
    }

    public function delete(Request $request)
    {
        $isAr = app()->getLocale() !== 'en';
        $code = trim((string) $request->input('code'));
        $id = $request->integer('id');
        if ($code === '' && $id < 1) {
            return mainResponse(false, $isAr ? 'التصنيف غير موجود.' : 'Category not found.', null, 404, 'items', '');
        }

        $row = KaheelCategory::query()
            ->when($code !== '', fn ($query) => $query->where('code', $code))
            ->when($code === '' && $id > 0, fn ($query) => $query->where('id', $id))
            ->first();
        if (!$row) {
            return mainResponse(false, $isAr ? 'التصنيف غير موجود.' : 'Category not found.', null, 404, 'items', '');
        }

        $alts = KaheelAlternative::query()->where('category', $row->code)->count();
        $items = KaheelPackageItem::query()->where('category', $row->code)->count();
        $orders = KaheelOrder::query()
            ->where('design_components', 'like', '%"category":"'.$row->code.'"%')
            ->count();
        if ($alts || $items || $orders) {
            return mainResponse(false, $isAr
                ? 'لا يمكن حذف التصنيف لأنه مستخدم في بدائل أو باقات أو طلبات.'
                : 'This category cannot be deleted because it is used in alternatives, packages, or orders.', null, 422, 'items', '');
        }

        $row->delete();

        return mainResponse(true, $isAr ? 'تم حذف التصنيف.' : 'Category deleted.', KaheelCategory::catalog(), 200, 'items', '');
    }
}
