<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaheelCustomerSession extends Model
{
    protected $table = 'kaheel_customer_sessions';

    protected $fillable = [
        'session_id',
        'bricks_user_id',
        'username',
        'name',
        'name_ar',
        'email',
        'mobile',
        'auth_token',
        'slug',
        'units',
    ];

    protected $hidden = [
        'auth_token',
    ];

    protected $casts = [
        'bricks_user_id' => 'integer',
        'units' => 'array',
    ];
}
