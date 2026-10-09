<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'device_type',
        'app_version',
        'ip',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
