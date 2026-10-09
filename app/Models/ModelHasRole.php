<?php

namespace App\Models;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelHasRole extends Model
{
    // use SoftDeletes;
    protected $table = 'model_has_roles';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
    ];

  
    public function role(){
        return $this->belongsTo(\App\Models\Role::class,'role_id');
    }

    public function user(){
        return $this->belongsTo(\App\Models\User::class,'model_id');
    }


   

}
