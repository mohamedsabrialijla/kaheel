<?php

namespace App\Http\Controllers\Kaheel;

use App\Models\KaheelAlternative;
use App\Models\KaheelCategory;
use Illuminate\Http\Request;

class AlternativeController extends KaheelAdminController
{
    protected function kaheelPermissionFor(string $method, $request): ?string
    {
        return match ($method) {
            'index' => 'alternatives.view',
            'save' => $request->filled('id') ? 'alternatives.edit' : 'alternatives.create',
            default => null,
        };
    }

    public function index()
    {
        return mainResponse(true, 'Success', $this->rows(), 200, 'items', '');
    }

    public function save(Request $request)
    {
        $category = (string) $request->input('category');
        $nameAr = trim((string) $request->input('name_ar'));
        $nameEn = trim((string) $request->input('name_en'));
        $codes = KaheelCategory::codes();

        if (!in_array($category, $codes, true) || $nameAr === '' || $nameEn === '') {
            return mainResponse(false, kaheel_msg('التصنيف والاسم بالعربية والإنجليزية مطلوبة.', 'Category and both names are required.'), null, 422, 'items', '');
        }
        if (!is_numeric($request->input('price_delta'))) {
            return mainResponse(false, kaheel_msg('السعر غير صحيح.', 'Invalid price.'), null, 422, 'items', '');
        }

        $item = $request->filled('id') ? KaheelAlternative::query()->find($request->input('id')) : new KaheelAlternative();
        if ($request->filled('id') && !$item) {
            return mainResponse(false, kaheel_msg('البديل غير موجود.', 'Alternative not found.'), null, 404, 'items', '');
        }

        $item->fill([
            'category' => $category,
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'price_delta' => round((float) $request->input('price_delta'), 2),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $item->exists ? $item->sort_order : ((int) KaheelAlternative::query()->max('sort_order')) + 1,
        ]);
        $item->save();

        return mainResponse(true, 'Success', $this->rows(), 200, 'items', '');
    }

    private function rows()
    {
        return KaheelAlternative::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (KaheelAlternative $item) => [
                'id' => $item->id,
                'category' => $item->category,
                'name_ar' => $item->name_ar,
                'name_en' => $item->name_en,
                'price_delta' => $item->price_delta,
                'is_active' => $item->is_active,
            ])
            ->values();
    }
}
