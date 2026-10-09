<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KaheelPackage extends Model
{
    protected $table = 'kaheel_packages';

    protected $fillable = [
        'code', 'name_ar', 'name_en', 'tagline_ar', 'tagline_en', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tiers(): HasMany
    {
        return $this->hasMany(KaheelPackageTier::class, 'package_id')->orderBy('sort_order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KaheelPackageItem::class, 'package_id')->orderBy('sort_order');
    }

    public function outline(): HasMany
    {
        return $this->hasMany(KaheelPackageItem::class, 'package_id')->whereNull('tier_id')->orderBy('sort_order');
    }
}
