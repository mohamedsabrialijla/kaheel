<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KaheelPackageTier extends Model
{
    protected $table = 'kaheel_package_tiers';

    protected $fillable = [
        'package_id', 'name_ar', 'name_en', 'description_ar', 'description_en',
        'price_excl_vat', 'price_incl_vat', 'supplier_cost', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'price_excl_vat' => 'float',
        'price_incl_vat' => 'float',
        'supplier_cost' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(KaheelPackage::class, 'package_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KaheelPackageItem::class, 'tier_id')->orderBy('sort_order');
    }
}
