<?php

namespace App\Console\Commands;

use App\Models\ItemsCategories;
use App\Models\ItemsTypes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportBrandsFromExcel extends Command
{
    protected $signature = 'brands:import
                            {file=brand.xlsx : Excel file path (relative to project root)}
                            {--dry-run : Preview without writing to database}
                            {--skip-categories : Do not create missing categories}
                            {--fresh : Delete all existing brands before import}';

    protected $description = 'Import brands from Excel (one sheet per category) into items_categories';

    /** English names for categories when creating from sheet title */
    protected array $categoryEnByAr = [
        'مجوهرات' => 'Jewelry',
        'أزياء' => 'Fashion',
        'أحذية' => 'Shoes',
        'أحزمة' => 'Belts',
        'نظارات' => 'Eyewear',
        'أقلام' => 'Pens',
        'ساعات' => 'Watches',
        'شنط' => 'Bags',
        'عطور' => 'Perfumes',
        'مكياج' => 'Makeup',
    ];

    protected array $categoryIconByAr = [
        'مجوهرات' => 'fas fa-gem',
        'أزياء' => 'fas fa-tshirt',
        'أحذية' => 'fas fa-shoe-prints',
        'أحزمة' => 'fas fa-ring',
        'نظارات' => 'fas fa-glasses',
        'أقلام' => 'fas fa-pen',
        'ساعات' => 'fas fa-clock',
        'شنط' => 'fas fa-shopping-bag',
        'عطور' => 'fas fa-spray-can',
        'مكياج' => 'fas fa-magic',
    ];

    public function handle(): int
    {
        $file = base_path($this->argument('file'));
        if (! is_file($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $jsonPath = storage_path('brand_import.json');
        $python = $this->resolvePython();
        $script = base_path('scripts/parse_brand_xlsx.py');
        $cmd = escapeshellarg($python).' '.escapeshellarg($script).' '.escapeshellarg($file).' 2>&1';
        exec($cmd, $output, $code);
        if ($code !== 0 || ! is_file($jsonPath)) {
            $this->error('Failed to parse Excel. Install Python openpyxl: pip install openpyxl');
            $this->line(implode("\n", $output));

            return self::FAILURE;
        }

        $sheets = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($sheets)) {
            $this->error('Invalid JSON from parser');

            return self::FAILURE;
        }

        $categoryTypeId = ItemsTypes::where('name', 'category')->value('id');
        $brandTypeId = ItemsTypes::where('name', 'brand')->value('id');
        if (! $categoryTypeId || ! $brandTypeId) {
            $this->error('Missing items_types: category or brand');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $skipCategories = (bool) $this->option('skip-categories');
        $fresh = (bool) $this->option('fresh');

        $stats = [
            'brands_deleted' => 0,
            'categories_created' => 0,
            'categories_matched' => 0,
            'brands_created' => 0,
            'brands_skipped' => 0,
            'errors' => [],
        ];

        if ($fresh) {
            $deleted = $this->deleteAllBrands($dryRun);
            $stats['brands_deleted'] = $deleted;
            if ($dryRun) {
                $this->warn("[dry-run] Would delete {$deleted} brands");
            } else {
                $this->warn("Deleted {$deleted} brands");
            }
        }

        $maxCategoryOrder = (int) ItemsCategories::where('type_id', $categoryTypeId)->max('order');

        foreach ($sheets as $sheet) {
            $categoryAr = trim($sheet['category_ar'] ?? '');
            $brands = $sheet['brands'] ?? [];
            if ($categoryAr === '') {
                continue;
            }

            $category = $this->findCategoryByArabicName($categoryTypeId, $categoryAr);

            if (! $category && ! $skipCategories) {
                $nameEn = $this->categoryEnByAr[$categoryAr] ?? Str::title($categoryAr);
                $icon = $this->categoryIconByAr[$categoryAr] ?? 'fas fa-tag';
                $maxCategoryOrder++;

                if ($dryRun) {
                    $this->line("[dry-run] Would create category: {$categoryAr} / {$nameEn}");
                    $stats['categories_created']++;
                    $categoryId = 0;
                } else {
                    $category = new ItemsCategories;
                    $category->type_id = $categoryTypeId;
                    $category->icon = $icon;
                    $category->slug = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nameEn) ?: 'CA', 0, 2));
                    $category->order = $maxCategoryOrder;
                    $category->status = 'active';
                    $category->created_by = 1;
                    $category->save();
                    $category->translateOrNew('ar')->name = $categoryAr;
                    $category->translateOrNew('en')->name = $nameEn;
                    $category->save();
                    $categoryId = $category->id;
                    $stats['categories_created']++;
                    $this->info("Created category: {$categoryAr} (id {$categoryId})");
                }
            } elseif ($category) {
                $categoryId = $category->id;
                $stats['categories_matched']++;
                $this->line("Matched category: {$categoryAr} (id {$categoryId})");
            } else {
                $stats['errors'][] = "Category not found: {$categoryAr}";
                $this->warn("Skip sheet (no category): {$categoryAr}");

                continue;
            }

            if ($dryRun && $categoryId === 0) {
                $stats['brands_created'] += count($brands);
                $this->line('  → '.count($brands).' brands would be imported');

                continue;
            }

            $order = (int) ItemsCategories::where('type_id', $brandTypeId)
                ->where('category', $categoryId)
                ->max('order');

            foreach ($brands as $row) {
                $nameAr = trim($row['ar'] ?? '');
                $nameEn = trim($row['en'] ?? '');
                if ($nameAr === '' || $nameEn === '') {
                    continue;
                }

                if (! $fresh && $this->brandExists($brandTypeId, $categoryId, $nameAr, $nameEn)) {
                    $stats['brands_skipped']++;

                    continue;
                }

                if ($dryRun) {
                    $stats['brands_created']++;

                    continue;
                }

                $order++;
                $brand = $this->createBrand($brandTypeId, $categoryId, $nameAr, $nameEn, $order);
                $stats['brands_created']++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            collect($stats)->except('errors')->map(fn ($v, $k) => [$k, $v])->values()->all()
        );
        if (! empty($stats['errors'])) {
            foreach ($stats['errors'] as $err) {
                $this->warn($err);
            }
        }

        if ($dryRun) {
            $this->comment('Dry run only — no database changes.');
        }

        return self::SUCCESS;
    }

    protected function resolvePython(): string
    {
        foreach (['python', 'python3', 'py'] as $bin) {
            exec(escapeshellarg($bin).' --version 2>&1', $out, $code);
            if ($code === 0) {
                return $bin;
            }
        }

        return 'python';
    }

    protected function deleteAllBrands(bool $dryRun): int
    {
        $brandTypeId = ItemsTypes::where('name', 'brand')->value('id');
        if (! $brandTypeId) {
            return 0;
        }

        $count = ItemsCategories::withTrashed()
            ->where('type_id', $brandTypeId)
            ->count();

        if ($dryRun || $count === 0) {
            return $count;
        }

        $ids = ItemsCategories::withTrashed()
            ->where('type_id', $brandTypeId)
            ->pluck('id');

        DB::table('items_categories_translations')
            ->whereIn('items_categories_id', $ids)
            ->delete();

        ItemsCategories::withTrashed()
            ->where('type_id', $brandTypeId)
            ->forceDelete();

        return $count;
    }

    protected function createBrand(int $brandTypeId, int $categoryId, string $nameAr, string $nameEn, int $order): ItemsCategories
    {
        $slugBase = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nameEn) ?: 'BR', 0, 2));
        $slug = $slugBase;
        $n = 0;
        while (ItemsCategories::withTrashed()->where('slug', $slug)->exists()) {
            $n++;
            $slug = $slugBase.$n;
        }

        $brand = new ItemsCategories;
        $brand->type_id = $brandTypeId;
        $brand->category = $categoryId;
        $brand->order = $order;
        $brand->status = 'active';
        $brand->created_by = 1;
        $brand->slug = $slug;
        $brand->save();

        $brand->translateOrNew('ar')->name = $nameAr;
        $brand->translateOrNew('en')->name = $nameEn;
        $brand->save();

        if ((int) $brand->fresh()->getAttributes()['category'] !== $categoryId) {
            DB::table('items_categories')
                ->where('id', $brand->id)
                ->update(['category' => $categoryId]);
            $brand->refresh();
        }

        return $brand;
    }

    protected function findCategoryByArabicName(int $categoryTypeId, string $nameAr): ?ItemsCategories
    {
        return ItemsCategories::where('type_id', $categoryTypeId)
            ->where('status', 'active')
            ->whereHas('translations', function ($q) use ($nameAr) {
                $q->where('locale', 'ar')->where('name', $nameAr);
            })
            ->orderBy('id')
            ->first();
    }

    protected function brandExists(int $brandTypeId, int $categoryId, string $nameAr, string $nameEn): bool
    {
        return ItemsCategories::where('type_id', $brandTypeId)
            ->where('category', $categoryId)
            ->where(function ($q) use ($nameAr, $nameEn) {
                $q->whereHas('translations', fn ($t) => $t->where('locale', 'ar')->where('name', $nameAr))
                    ->orWhereHas('translations', fn ($t) => $t->where('locale', 'en')->where('name', $nameEn));
            })
            ->exists();
    }
}
