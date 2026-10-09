<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait AdListingQueryTrait
{
    protected function adListingRelations(): array
    {
        return ['images', 'user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city'];
    }

    protected function blockedUserIds(): array
    {
        $user = auth('sanctum')->user();
        if (!$user instanceof \App\Models\User) {
            return [];
        }

        return $user->blockedUsers()->pluck('users.id')->map(fn ($id) => (int) $id)->values()->toArray();
    }

    /**
     * استعلام موحّد للقائمة الرئيسية / الفلتر / الترتيب.
     */
    protected function baseAdListingQuery(Request $request): Builder
    {
        $blockedUserIds = $this->blockedUserIds();

        $query = Ad::query()
            ->with($this->adListingRelations())
            ->where('status', 'active')
            ->when($blockedUserIds, fn (Builder $q) => $q->whereNotIn('user_id', $blockedUserIds))
            ->when($request->filled('user_id'), fn (Builder $q) => $q->where('user_id', $request->user_id));

        $this->applyAdFilters($query, $request);
        $this->applyAdSort($query, $request);

        return $query;
    }

    protected function applyAdFilters(Builder $query, Request $request): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('gender_id')) {
            $query->where('gender_id', $request->gender_id);
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('invoice')) {
            $invoice = strtolower(trim((string) $request->invoice));
            if (in_array($invoice, ['yes', '1', 'true'], true)) {
                $query->whereNotNull('invoice')->where('invoice', '!=', '');
            } elseif (in_array($invoice, ['no', '0', 'false'], true)) {
                $query->where(function (Builder $q) {
                    $q->whereNull('invoice')->orWhere('invoice', '');
                });
            }
        }

        if ($request->filled('product_type')) {
            $productType = strtolower(trim((string) $request->product_type));
            if (in_array($productType, ['new', 'used'], true)) {
                $query->where('product_type', $productType);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            if ($search !== '') {
                $query->where(function (Builder $q) use ($search) {
                    $q->whereTranslationLike('title', '%' . $search . '%')
                        ->orWhereTranslationLike('description', '%' . $search . '%');
                });
            }
        }
    }

    protected function normalizeSortBy(Request $request): string
    {
        $raw = strtolower(trim((string) ($request->input('sort_by') ?? $request->input('sort') ?? 'latest')));
        $raw = str_replace([' ', '-'], '_', $raw);

        $aliases = [
            'latest' => ['latest', 'newest', 'new', 'recent', 'date', 'updated_at', 'created_at'],
            'most_viewed' => ['most_viewed', 'most_view', 'most_views', 'views', 'viewed', 'most_viewd'],
            'more_rate' => ['more_rate', 'most_rate', 'most_rated', 'highest_rate', 'highest_rating', 'rating', 'rate', 'top_rated'],
            'highest_price' => ['highest_price', 'high_price', 'highest', 'price_desc', 'max_price', 'highest_value', 'more_price'],
            'less_price' => ['less_price', 'lowest_price', 'low_price', 'lowest', 'price_asc', 'min_price', 'lowest_value', 'less_value'],
        ];

        foreach ($aliases as $sort => $values) {
            if (in_array($raw, $values, true)) {
                return $sort;
            }
        }

        return 'latest';
    }

    /**
     * السعر مخزّن varchar وقد يحتوي أرقام عربية أو فواصل آلاف أو نص عملة.
     */
    protected function numericPriceExpression(): string
    {
        $expr = "TRIM(COALESCE(ads.price, ''))";

        $map = [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٫' => '.', '٬' => '', ',' => '', ' ' => '',
        ];

        foreach ($map as $from => $to) {
            $expr = "REPLACE({$expr}, '{$from}', '{$to}')";
        }

        return "CAST(NULLIF({$expr}, '') AS DECIMAL(15,2))";
    }

    protected function applyAdSort(Builder $query, Request $request): void
    {
        $sortBy = $this->normalizeSortBy($request);
        $priceExpr = $this->numericPriceExpression();

        if ($sortBy === 'more_rate') {
            $query->select('ads.*')->selectSub(function ($q) {
                $q->from('ratings')
                    ->selectRaw('COALESCE(AVG(rating), 0)')
                    ->whereColumn('ratings.seller_id', 'ads.user_id');
            }, 'average_rating');
        }

        switch ($sortBy) {
            case 'most_viewed':
                $query->orderByDesc('ads.views');
                break;

            case 'more_rate':
                $query->orderByDesc('average_rating');
                break;

            case 'highest_price':
                $query->orderByRaw("({$priceExpr}) IS NULL ASC")
                    ->orderByRaw("({$priceExpr}) DESC");
                break;

            case 'less_price':
                $query->orderByRaw("({$priceExpr}) IS NULL ASC")
                    ->orderByRaw("({$priceExpr}) ASC");
                break;

            case 'latest':
            default:
                $query->orderByDesc('ads.updated_at');
                break;
        }

        // Tie-breaker ثابت حتى ما يختلف الترتيب من جهاز لجهاز
        $query->orderByDesc('ads.id');
    }

    protected function paginateAdListing(Request $request, ?Builder $query = null)
    {
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        if ($perPage > 50) {
            $perPage = 50;
        }

        return ($query ?? $this->baseAdListingQuery($request))->paginate($perPage);
    }
}
