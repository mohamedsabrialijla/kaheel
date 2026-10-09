<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function imageable()
    {
        return $this->morphTo();
    }


    public function getUrlAttribute($value){

        return url($value);
    } 



    






}


