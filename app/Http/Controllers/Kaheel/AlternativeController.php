<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelAlternative;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    public const CATEGORIES = ['furniture', 'kitchen', 'wardrobe', 'appliances', 'tv', 'curtains', 'accessories', 'soft', 'lighting'];

    public function index()
    {
        return mainResponse(true, 'Success', $this->rows(), 200, 'items', '');
    }

    public function save(Request $request)
    {
        $category = (string) $request->input('category');
        $nameAr = trim((string) $request->input('name_ar'));
        $nameEn = trim((string) $request->input('name_en'));

        if (!in_array($category, self::CATEGORIES, true) || $nameAr === '' || $nameEn === '') {
            return mainResponse(false, 'Category and both names are required', null, 422, 'items', '');
        }
        if (!is_numeric($request->input('price_delta'))) {
            return mainResponse(false, 'Invalid price', null, 422, 'items', '');
        }

        $item = $request->filled('id') ? KaheelAlternative::query()->find($request->input('id')) : new KaheelAlternative();
        if ($request->filled('id') && !$item) {
            return mainResponse(false, 'Alternative not found', null, 404, 'items', '');
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
