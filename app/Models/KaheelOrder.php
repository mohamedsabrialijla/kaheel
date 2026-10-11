<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KaheelOrder extends Model
{
    protected $table = 'kaheel_orders';

    protected $fillable = [
        'number', 'bricks_user_id', 'username', 'customer_name',
        'package_code', 'package_name_ar', 'package_name_en',
        'tier_name_ar', 'tier_name_en',
        'compound_id', 'compound_name', 'compound_meta',
        'unit_id', 'unit_no', 'bedrooms_number', 'squar_meters',
        'price_excl', 'price_incl', 'deposit_percent', 'deposit_amount',
        'contract_body', 'signature', 'status',
    ];

    protected $casts = [
        'bricks_user_id' => 'integer',
        'bedrooms_number' => 'integer',
        'squar_meters' => 'float',
        'price_excl' => 'float',
        'price_incl' => 'float',
        'deposit_percent' => 'float',
        'deposit_amount' => 'float',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(KaheelOrderFile::class, 'order_id');
    }
}
