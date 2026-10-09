<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdTranslation extends Model
{
    use  SoftDeletes;
    public $fillable = ['title', 'description'];
}
