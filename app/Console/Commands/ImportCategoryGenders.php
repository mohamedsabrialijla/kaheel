<?php

namespace App\Console\Commands;

use App\Models\ItemsCategories;
use App\Models\ItemsTypes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCategoryGenders extends Command
{
    protected $signature = 'categories:import-genders
                            {--dry-run : Preview only}
                            {--fresh : Remove existing genders/types for listed categories first}';

    protected $description = 'Import gender (male/female) and subcategory types for the 10 main categories';

    protected const GENDER_LABELS = [
        'male' => ['ar' => 'رجالي', 'en' => "Men's"],
        'female' => ['ar' => 'نسائي', 'en' => "Women's"],
    ];

    /** @var array<string, array{mode: string, male?: list<array{ar: string, en: string}>, female?: list<array{ar: string, en: string}>}> */
    protected array $catalog = [
        'مجوهرات' => [
            'mode' => 'both',
            'male' => [
                ['ar' => 'خاتم', 'en' => 'Ring'],
                ['ar' => 'سوار', 'en' => 'Bracelet'],
                ['ar' => 'بروش', 'en' => 'Brooch'],
            ],
            'female' => [
                ['ar' => 'خاتم', 'en' => 'Ring'],
                ['ar' => 'سوار', 'en' => 'Bracelet'],
                ['ar' => 'خلخال', 'en' => 'Anklet'],
                ['ar' => 'حلق', 'en' => 'Earring'],
                ['ar' => 'عقد', 'en' => 'Necklace'],
                ['ar' => 'سلسال', 'en' => 'Chain'],
                ['ar' => 'بروش', 'en' => 'Brooch'],
                ['ar' => 'طقم', 'en' => 'Set'],
            ],
        ],
        'أحذية' => [
            'mode' => 'both',
            'male' => [
                ['ar' => 'رسمي', 'en' => 'Formal'],
                ['ar' => 'سنيكرز', 'en' => 'Sneakers'],
            ],
            'female' => [
                ['ar' => 'كعب', 'en' => 'Heels'],
                ['ar' => 'سليبر', 'en' => 'Slippers'],
                ['ar' => 'سنيكرز', 'en' => 'Sneakers'],
                ['ar' => 'صندل', 'en' => 'Sandals'],
            ],
        ],
        'شنط' => [
            'mode' => 'both',
            'male' => [
                ['ar' => 'حقيبة ظهر', 'en' => 'Backpack'],
                ['ar' => 'حقيبة سفر', 'en' => 'Travel bag'],
                ['ar' => 'محفظة', 'en' => 'Wallet'],
            ],
            'female' => [
                ['ar' => 'حقيبة يد', 'en' => 'Handbag'],
                ['ar' => 'حقيبة ظهر', 'en' => 'Backpack'],
                ['ar' => 'حقيبة سفر', 'en' => 'Travel bag'],
                ['ar' => 'محفظة', 'en' => 'Wallet'],
                ['ar' => 'توتباق', 'en' => 'Tote bag'],
            ],
        ],
        'أزياء' => [
            'mode' => 'both',
            'male' => [
                ['ar' => 'قميص', 'en' => 'Shirt'],
                ['ar' => 'تي شيرت', 'en' => 'T-shirt'],
                ['ar' => 'بنطلون', 'en' => 'Pants'],
                ['ar' => 'جينز', 'en' => 'Jeans'],
                ['ar' => 'شورت', 'en' => 'Shorts'],
                ['ar' => 'جاكيت', 'en' => 'Jacket'],
                ['ar' => 'بدلة', 'en' => 'Suit'],
                ['ar' => 'بليزر', 'en' => 'Blazer'],
                ['ar' => 'سويت بانتس', 'en' => 'Sweatpants'],
                ['ar' => 'هودي', 'en' => 'Hoodie'],
                ['ar' => 'سويتر', 'en' => 'Sweater'],
                ['ar' => 'كوت', 'en' => 'Coat'],
                ['ar' => 'كارديغان', 'en' => 'Cardigan'],
                ['ar' => 'بيجامة', 'en' => 'Pajamas'],
                ['ar' => 'ملابس رياضية', 'en' => 'Sportswear'],
            ],
            'female' => [
                ['ar' => 'فستان', 'en' => 'Dress'],
                ['ar' => 'قميص', 'en' => 'Shirt'],
                ['ar' => 'بلوزة', 'en' => 'Blouse'],
                ['ar' => 'تنورة', 'en' => 'Skirt'],
                ['ar' => 'جينز', 'en' => 'Jeans'],
                ['ar' => 'جاكيت', 'en' => 'Jacket'],
                ['ar' => 'بليزر', 'en' => 'Blazer'],
                ['ar' => 'سويت بانتس', 'en' => 'Sweatpants'],
                ['ar' => 'هودي', 'en' => 'Hoodie'],
                ['ar' => 'سويتر', 'en' => 'Sweater'],
                ['ar' => 'كوت', 'en' => 'Coat'],
                ['ar' => 'كارديغان', 'en' => 'Cardigan'],
                ['ar' => 'جمبسوت', 'en' => 'Jumpsuit'],
                ['ar' => 'بيجامة', 'en' => 'Pajamas'],
                ['ar' => 'ملابس رياضية', 'en' => 'Sportswear'],
            ],
        ],
        'مكياج' => [
            'mode' => 'female_only',
            'female' => [
                ['ar' => 'أحمر شفاه', 'en' => 'Lipstick'],
                ['ar' => 'فاونديشن', 'en' => 'Foundation'],
                ['ar' => 'باليت', 'en' => 'Palette'],
                ['ar' => 'مسكارا', 'en' => 'Mascara'],
                ['ar' => 'برونزر', 'en' => 'Bronzer'],
                ['ar' => 'هايلايتر', 'en' => 'Highlighter'],
                ['ar' => 'أيلاينر', 'en' => 'Eyeliner'],
                ['ar' => 'كونسيلر', 'en' => 'Concealer'],
                ['ar' => 'بلاشر', 'en' => 'Blush'],
                ['ar' => 'برايمر', 'en' => 'Primer'],
                ['ar' => 'سيتينج باودر', 'en' => 'Setting powder'],
                ['ar' => 'لب لاينر', 'en' => 'Lip liner'],
                ['ar' => 'أي شادو', 'en' => 'Eyeshadow'],
                ['ar' => 'كونتور', 'en' => 'Contour'],
                ['ar' => 'بي بي كريم', 'en' => 'BB cream'],
                ['ar' => 'لب غلوس', 'en' => 'Lip gloss'],
                ['ar' => 'لاش سيروم', 'en' => 'Lash serum'],
                ['ar' => 'سيروم بشرة', 'en' => 'Skin serum'],
            ],
        ],
        'أقلام' => [
            'mode' => 'male_only',
            'male' => [
                ['ar' => 'قلم حبر', 'en' => 'Fountain pen'],
                ['ar' => 'قلم جاف', 'en' => 'Ballpoint pen'],
                ['ar' => 'قلم رولربول', 'en' => 'Rollerball pen'],
                ['ar' => 'قلم ميكانيكي', 'en' => 'Mechanical pencil'],
                ['ar' => 'طقم أقلام', 'en' => 'Pen set'],
            ],
        ],
        'ساعات' => ['mode' => 'both', 'male' => [], 'female' => []],
        'أحزمة' => ['mode' => 'both', 'male' => [], 'female' => []],
        'عطور' => ['mode' => 'both', 'male' => [], 'female' => []],
        'نظارات' => ['mode' => 'both', 'male' => [], 'female' => []],
    ];

    public function handle(): int
    {
        $categoryTypeId = ItemsTypes::where('name', 'category')->value('id');
        $genderTypeId = ItemsTypes::where('name', 'gender')->value('id');
        $subcategoryTypeId = ItemsTypes::where('name', 'subcategory')->value('id');

        if (! $categoryTypeId || ! $genderTypeId || ! $subcategoryTypeId) {
            $this->error('Run migration for gender/subcategory types first.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $fresh = (bool) $this->option('fresh');
        $adminId = 1;

        $stats = ['categories' => 0, 'genders' => 0, 'subcategories' => 0, 'skipped' => 0];

        foreach ($this->catalog as $categoryAr => $config) {
            $category = $this->findCategory($categoryTypeId, $categoryAr);
            if (! $category) {
                $this->warn("Category not found: {$categoryAr}");
                $stats['skipped']++;

                continue;
            }

            $this->line("→ {$categoryAr} (id {$category->id}) [{$config['mode']}]");
            $stats['categories']++;

            if ($fresh && ! $dryRun) {
                $this->purgeGendersForCategory($category->id, $genderTypeId, $subcategoryTypeId);
            } elseif ($fresh && $dryRun) {
                $this->comment('  [dry-run] would purge existing genders');
            }

            $mode = $config['mode'];
            $slugs = match ($mode) {
                'male_only' => ['male'],
                'female_only' => ['female'],
                default => ['male', 'female'],
            };

            if ($fresh && ! $dryRun && in_array($mode, ['male_only', 'female_only'], true)) {
                $removeSlug = $mode === 'male_only' ? 'female' : 'male';
                $this->purgeGenderSlug($category->id, $genderTypeId, $subcategoryTypeId, $removeSlug);
            }

            foreach ($slugs as $slug) {
                $rows = $config[$slug] ?? [];
                if ($dryRun) {
                    $stats['genders']++;
                    $stats['subcategories'] += count($rows);
                    $this->comment("  [dry-run] {$slug}: ".count($rows).' types');

                    continue;
                }

                $gender = $this->ensureGender($category->id, $slug, $genderTypeId, $adminId);
                $stats['genders']++;
                $stats['subcategories'] += $this->syncSubcategories($gender, $rows, $subcategoryTypeId, $adminId);
            }
        }

        $this->newLine();
        $this->table(['Metric', 'Count'], collect($stats)->map(fn ($v, $k) => [$k, $v])->values());

        if ($dryRun) {
            $this->comment('Dry run — no changes saved.');
        }

        return self::SUCCESS;
    }

    protected function findCategory(int $categoryTypeId, string $nameAr): ?ItemsCategories
    {
        return ItemsCategories::where('type_id', $categoryTypeId)
            ->where('status', 'active')
            ->whereHas('translations', fn ($q) => $q->where('locale', 'ar')->where('name', $nameAr))
            ->orderBy('id')
            ->first();
    }

    protected function purgeGendersForCategory(int $categoryId, int $genderTypeId, int $subcategoryTypeId): void
    {
        $genderIds = ItemsCategories::where('type_id', $genderTypeId)
            ->where('category', $categoryId)
            ->pluck('id');

        if ($genderIds->isEmpty()) {
            return;
        }

        $subIds = ItemsCategories::where('type_id', $subcategoryTypeId)
            ->whereIn('category', $genderIds)
            ->pluck('id');

        $allIds = $genderIds->merge($subIds);
        if ($allIds->isNotEmpty()) {
            DB::table('items_categories_translations')->whereIn('items_categories_id', $allIds)->delete();
        }

        if ($subIds->isNotEmpty()) {
            ItemsCategories::whereIn('id', $subIds)->delete();
        }

        ItemsCategories::whereIn('id', $genderIds)->delete();
    }

    protected function purgeGenderSlug(int $categoryId, int $genderTypeId, int $subcategoryTypeId, string $slug): void
    {
        $gender = ItemsCategories::where('type_id', $genderTypeId)
            ->where('category', $categoryId)
            ->where('slug', $slug)
            ->first();

        if (! $gender) {
            return;
        }

        $subIds = ItemsCategories::where('type_id', $subcategoryTypeId)
            ->where('category', $gender->id)
            ->pluck('id');

        if ($subIds->isNotEmpty()) {
            DB::table('items_categories_translations')->whereIn('items_categories_id', $subIds)->delete();
            ItemsCategories::whereIn('id', $subIds)->delete();
        }

        DB::table('items_categories_translations')->where('items_categories_id', $gender->id)->delete();
        $gender->delete();
    }

    protected function ensureGender(int $categoryId, string $slug, int $genderTypeId, int $adminId): ItemsCategories
    {
        $labels = self::GENDER_LABELS[$slug];
        $gender = ItemsCategories::where('type_id', $genderTypeId)
            ->where('category', $categoryId)
            ->where('slug', $slug)
            ->first();

        if (! $gender) {
            $gender = new ItemsCategories;
            $gender->type_id = $genderTypeId;
            $gender->category = $categoryId;
            $gender->slug = $slug;
            $gender->order = $slug === 'male' ? 1 : 2;
            $gender->created_by = $adminId;
            $gender->status = 'active';
        }

        $gender->translateOrNew('ar')->name = $labels['ar'];
        $gender->translateOrNew('en')->name = $labels['en'];
        $gender->save();

        return $gender;
    }

    protected function syncSubcategories(ItemsCategories $gender, array $rows, int $subcategoryTypeId, int $adminId): int
    {
        $keptIds = [];
        $created = 0;

        foreach ($rows as $index => $row) {
            $nameAr = trim($row['ar'] ?? '');
            $nameEn = trim($row['en'] ?? $nameAr);
            if ($nameAr === '') {
                continue;
            }

            $sub = ItemsCategories::where('type_id', $subcategoryTypeId)
                ->where('category', $gender->id)
                ->whereHas('translations', fn ($q) => $q->where('locale', 'ar')->where('name', $nameAr))
                ->first();

            if (! $sub) {
                $sub = new ItemsCategories;
                $sub->type_id = $subcategoryTypeId;
                $sub->category = $gender->id;
                $sub->created_by = $adminId;
                $sub->status = 'active';
                $created++;
            }

            $sub->order = $index + 1;
            $sub->slug = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nameEn) ?: 'TY', 0, 2));
            $sub->translateOrNew('ar')->name = $nameAr;
            $sub->translateOrNew('en')->name = $nameEn;
            $sub->save();
            $keptIds[] = $sub->id;
        }

        $deleteQuery = ItemsCategories::where('type_id', $subcategoryTypeId)->where('category', $gender->id);
        if (count($keptIds) > 0) {
            $deleteQuery->whereNotIn('id', $keptIds);
        }
        $toDelete = $deleteQuery->pluck('id');
        if ($toDelete->isNotEmpty()) {
            DB::table('items_categories_translations')->whereIn('items_categories_id', $toDelete)->delete();
            ItemsCategories::whereIn('id', $toDelete)->delete();
        }

        return $created;
    }
}
