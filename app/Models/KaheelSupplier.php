<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class KaheelSupplier extends Model
{
    protected $table = 'kaheel_suppliers';

    protected $fillable = [
        'name_ar',
        'name_en',
        'contact_name',
        'phone',
        'email',
        'password',
        'commission_rate',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'commission_rate' => 'float',
        'is_active' => 'boolean',
    ];

    public function setPasswordAttribute($value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['password'] = Hash::isHashed($value) ? $value : Hash::make($value);
    }
}
