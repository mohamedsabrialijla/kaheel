<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Rating extends Model
{
    protected $fillable = ['user_id', 'seller_id', 'rating', 'comment'];

    // public function ad()
    // {
    //     return $this->belongsTo(Ad::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }
}
