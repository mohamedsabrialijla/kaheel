<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ItemsCategories;
use App\Models\ItemsTypes;
use Illuminate\Http\Request;

trait CategoryGenderFilterTrait
{
    protected function genderTypeId(): ?int
    {
        return ItemsTypes::where('name', 'gender')->value('id');
    }

    protected function subcategoryTypeId(): ?int
    {
        return ItemsTypes::where('name', 'subcategory')->value('id');
    }

    protected function categoryTypeId(): ?int
    {
        return ItemsTypes::where('name', 'category')->value('id');
    }

    protected function brandTypeId(): ?int
    {
        return ItemsTypes::where('name', 'brand')->value('id');
    }

    /** يحل gender_id من رقم، أو gender_id=male|female، أو gender=male|female مع category_id */
    protected function resolveGenderId(Request $request): ?int
    {
        $rawGenderId = $request->input('gender_id');
        $rawGender = $request->input('gender');

        $slugAliases = [
            'male' => 'male',
            'm' => 'male',
            'mens' => 'male',
            'man' => 'male',
            'men' => 'male',
            'female' => 'female',
            'f' => 'female',
            'woman' => 'female',
            'women' => 'female',
        ];

        $slugCandidate = null;
        if (is_string($rawGenderId) && $rawGenderId !== '' && ! ctype_digit($rawGenderId)) {
            $slugCandidate = strtolower(trim($rawGenderId));
        } elseif (is_string($rawGender) && $rawGender !== '') {
            $slugCandidate = strtolower(trim($rawGender));
        }

        if ($slugCandidate !== null) {
            if (! isset($slugAliases[$slugCandidate]) || ! $request->filled('category_id')) {
                return null;
            }

            $slug = $slugAliases[$slugCandidate];
            $genderTypeId = $this->genderTypeId();
            if (! $genderTypeId) {
                return null;
            }

            return $this->activeCategoryQuery(
                ItemsCategories::where('type_id', $genderTypeId)
                    ->where('category', (int) $request->category_id)
                    ->where('slug', $slug)
            )->value('id');
        }

        if ($request->filled('gender_id') && ctype_digit((string) $rawGenderId)) {
            return (int) $rawGenderId;
        }

        return null;
    }

    protected function activeCategoryQuery($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')->orWhereNull('status');
        });
    }

    /** يظهر الجنس فقط إذا له أبناء (أنواع) مُعرّفة في الأدمن */
    protected function genderHasSubcategories(int $genderId): bool
    {
        $subcategoryTypeId = $this->subcategoryTypeId();
        if (!$subcategoryTypeId) {
            return false;
        }

        return $this->activeCategoryQuery(
            ItemsCategories::where('type_id', $subcategoryTypeId)->where('category', $genderId)
        )->exists();
    }

    public function getFilterGenders(Request $request)
    {
        return $this->getGenders($request);
    }

    public function getFilterSubcategories(Request $request)
    {
        return $this->getSubcategories($request);
    }

    public function getGenders(Request $request)
    {
        if (!$request->filled('category_id')) {
            return mainResponse(true, __('common.return'), [], 200, 'items', '');
        }

        $genderTypeId = $this->genderTypeId();
        if (!$genderTypeId) {
            return mainResponse(true, __('common.return'), [], 200, 'items', '');
        }

        $categoryId = (int) $request->category_id;

        $items = $this->activeCategoryQuery(
            ItemsCategories::where('type_id', $genderTypeId)
                ->where('category', $categoryId)
                ->whereIn('slug', ['male', 'female'])
        )
            ->orderBy('order')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
            ])
            ->values();

        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }

    public function getSubcategories(Request $request)
    {
        if (!$request->filled('gender_id')) {
            return mainResponse(true, __('common.return'), [], 200, 'items', '');
        }

        $items = $this->typesForGender((int) $request->gender_id);

        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }

    /**
     * GET basic/getCategories
     * - بدون params: التصنيفات الرئيسية
     * - category_id فقط: الماركات
     * - category_id + withGender=1:
     *   { mens:{id,visible,name}, woman:{id,visible,name} }
     * - category_id + gender_id=<id من withGender>: الأنواع (type)
     * - category_id + gender_id=female|male أو gender=male|female: الأنواع
     */
    public function getCategories(Request $request)
    {
        $genderId = $this->resolveGenderId($request);

        if ($genderId) {
            $items = $this->typesForGender((int) $genderId);

            return mainResponse(true, __('common.return'), $items, 200, 'items', '');
        }

        if ($request->filled('category_id') && $this->wantsGenderAvailability($request)) {
            return mainResponse(
                true,
                __('common.return'),
                $this->genderAvailabilityForCategory((int) $request->category_id),
                200,
                'items',
                ''
            );
        }

        if ($request->filled('category_id')) {
            $brandTypeId = $this->brandTypeId() ?? 30;

            $items = $this->activeCategoryQuery(
                ItemsCategories::where('type_id', $brandTypeId)->where('category', (int) $request->category_id)
            )
                ->orderBy('order')
                ->get()
                ->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                ])
                ->values();

            return mainResponse(true, __('common.return'), $items, 200, 'items', '');
        }

        $categoryTypeId = $this->categoryTypeId() ?? 2;

        $items = $this->activeCategoryQuery(ItemsCategories::where('type_id', $categoryTypeId))
            ->orderBy('order')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->values();

        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }

    protected function wantsGenderAvailability(Request $request): bool
    {
        $value = $request->input('withGender');

        return $request->boolean('withGender')
            || $value === 1
            || $value === '1'
            || $value === true
            || $value === 'true';
    }

    /**
     * بيانات الجنس لكل قسم (من اللوحة) للاستخدام في الواجهة والجوال.
     * visible=1 فقط إذا وُجدت أنواع (subcategories) تحت هذا الجنس.
     *
     * @return array{
     *   mens: array{id:int|null, visible:int, name:string},
     *   woman: array{id:int|null, visible:int, name:string}
     * }
     */
    protected function genderAvailabilityForCategory(int $categoryId): array
    {
        $empty = static fn (): array => [
            'id' => null,
            'visible' => 0,
            'name' => '',
        ];

        $result = [
            'mens' => $empty(),
            'woman' => $empty(),
        ];

        $genderTypeId = $this->genderTypeId();
        if (! $genderTypeId) {
            return $result;
        }

        $map = [
            'male' => 'mens',
            'female' => 'woman',
        ];

        foreach ($map as $slug => $key) {
            $gender = $this->activeCategoryQuery(
                ItemsCategories::where('type_id', $genderTypeId)
                    ->where('category', $categoryId)
                    ->where('slug', $slug)
            )->first();

            if (! $gender) {
                continue;
            }

            $result[$key] = [
                'id' => (int) $gender->id,
                'visible' => $this->genderHasSubcategories((int) $gender->id) ? 1 : 0,
                'name' => (string) ($gender->name ?? ''),
            ];
        }

        return $result;
    }

    /** @return \Illuminate\Support\Collection<int, array{id:int, name:string}> */
    protected function typesForGender(int $genderId)
    {
        $subcategoryTypeId = $this->subcategoryTypeId();
        if (! $subcategoryTypeId) {
            return collect();
        }

        return $this->activeCategoryQuery(
            ItemsCategories::where('type_id', $subcategoryTypeId)->where('category', $genderId)
        )
            ->orderBy('order')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->values();
    }

    /**
     * gender_id و subcategory_id اختياريان دائماً (إنشاء + تعديل).
     * القيم الفارغة تُحوَّل إلى null حتى لا تفشل قاعدة integer ولتمسح الحقول عند التعديل.
     * brand_id مطلوب فقط عندما لا يُرسل مسار الجنس.
     */
    protected function applyGenderBrandRules(Request $request, array &$rules): void
    {
        foreach (['gender_id', 'subcategory_id', 'brand_id'] as $key) {
            if (! $request->exists($key)) {
                continue;
            }
            $value = $request->input($key);
            if ($value === '' || $value === false) {
                $request->merge([$key => null]);
            }
        }

        $rules['gender_id'] = 'nullable|integer';
        $rules['subcategory_id'] = 'nullable|integer';

        if ($request->filled('gender_id') || $request->filled('subcategory_id')) {
            $rules['brand_id'] = 'nullable|integer';
        } else {
            $rules['brand_id'] = 'required|integer';
        }
    }
}
