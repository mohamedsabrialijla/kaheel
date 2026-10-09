<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes, Translatable;

    public $translatedAttributes = ['name', 'slug', 'description', 'key_words'];

    protected $fillable = ['image'];

    public function getImageAttribute($image)
    {
        if (!is_null($image)) {
            return url($image);
        }
    }


    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }
}
