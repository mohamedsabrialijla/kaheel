<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kaheel_packages')) {
            Schema::create('kaheel_packages', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('tagline_ar')->nullable();
                $table->string('tagline_en')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kaheel_package_tiers')) {
            Schema::create('kaheel_package_tiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('kaheel_packages')->cascadeOnDelete();
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('description_ar')->nullable();
                $table->string('description_en')->nullable();
                $table->decimal('price_excl_vat', 14, 2)->default(0);
                $table->decimal('price_incl_vat', 14, 2)->default(0);
                $table->decimal('supplier_cost', 14, 2)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kaheel_package_items')) {
            Schema::create('kaheel_package_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('kaheel_packages')->cascadeOnDelete();
                $table->foreignId('tier_id')->nullable()->constrained('kaheel_package_tiers')->cascadeOnDelete();
                $table->string('category')->default('general');
                $table->string('name_ar');
                $table->string('name_en');
                $table->string('product_name_ar')->nullable();
                $table->string('product_name_en')->nullable();
                $table->decimal('quantity', 10, 2)->default(1);
                $table->string('unit_label_ar')->default('قطعة');
                $table->string('unit_label_en')->default('piece');
                $table->decimal('price_per_unit', 14, 2)->default(0);
                $table->boolean('allow_alternatives')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (DB::table('kaheel_packages')->count() > 0) {
            return;
        }

        $path = database_path('data/kaheel_packages.json');
        if (!is_file($path)) {
            return;
        }

        $packages = json_decode(file_get_contents($path), true) ?: [];
        $now = now();

        foreach ($packages as $package) {
            $packageId = DB::table('kaheel_packages')->insertGetId([
                'code' => $package['code'],
                'name_ar' => $package['name_ar'],
                'name_en' => $package['name_en'],
                'tagline_ar' => $package['tagline_ar'] ?? null,
                'tagline_en' => $package['tagline_en'] ?? null,
                'sort_order' => $package['sort_order'] ?? 0,
                'is_active' => (bool) ($package['is_active'] ?? true),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($package['items'] ?? [] as $index => $item) {
                if (!empty($item['tier_id'])) {
                    continue;
                }
                $this->insertItem($packageId, null, $item, $index, $now);
            }

            foreach ($package['tiers'] ?? [] as $tier) {
                $tierId = DB::table('kaheel_package_tiers')->insertGetId([
                    'package_id' => $packageId,
                    'name_ar' => $tier['name_ar'],
                    'name_en' => $tier['name_en'],
                    'description_ar' => $tier['description_ar'] ?? null,
                    'description_en' => $tier['description_en'] ?? null,
                    'price_excl_vat' => $tier['price_excl_vat'] ?? 0,
                    'price_incl_vat' => $tier['price_incl_vat'] ?? 0,
                    'supplier_cost' => $tier['supplier_cost'] ?? 0,
                    'sort_order' => $tier['sort_order'] ?? 0,
                    'is_active' => (bool) ($tier['is_active'] ?? true),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($tier['items'] ?? [] as $index => $item) {
                    $this->insertItem($packageId, $tierId, $item, $index, $now);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kaheel_package_items');
        Schema::dropIfExists('kaheel_package_tiers');
        Schema::dropIfExists('kaheel_packages');
    }

    private function insertItem(int $packageId, ?int $tierId, array $item, int $index, $now): void
    {
        DB::table('kaheel_package_items')->insert([
            'package_id' => $packageId,
            'tier_id' => $tierId,
            'category' => $item['category'] ?? 'general',
            'name_ar' => $item['name_ar'],
            'name_en' => $item['name_en'],
            'product_name_ar' => $item['product_name_ar'] ?? null,
            'product_name_en' => $item['product_name_en'] ?? null,
            'quantity' => $item['quantity'] ?? 1,
            'unit_label_ar' => $item['unit_label_ar'] ?? 'قطعة',
            'unit_label_en' => $item['unit_label_en'] ?? 'piece',
            'price_per_unit' => $item['price_per_unit'] ?? 0,
            'allow_alternatives' => (bool) ($item['allow_alternatives'] ?? false),
            'sort_order' => $item['sort_order'] ?? ($index + 1),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
};
