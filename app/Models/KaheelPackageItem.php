<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KaheelPackageItem extends Model
{
    protected $table = 'kaheel_package_items';

    protected $fillable = [
        'package_id', 'tier_id', 'category', 'name_ar', 'name_en',
        'product_name_ar', 'product_name_en', 'detail_ar', 'detail_en', 'quantity',
        'unit_label_ar', 'unit_label_en', 'price_per_unit',
        'allow_alternatives', 'sort_order',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price_per_unit' => 'float',
        'allow_alternatives' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(KaheelPackage::class, 'package_id');
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(KaheelPackageTier::class, 'tier_id');
    }
}
