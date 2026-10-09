<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdView extends Model
{
    protected $fillable = ['ad_id', 'user_id', 'ip'];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
