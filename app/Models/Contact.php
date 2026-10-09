<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contact extends Model
{
    protected $fillable = ['message'];
    protected $appends = ['reasons'];
    protected $casts = [
        'reasons_ids' => 'string',
    ];
  
    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }
    


    public function user(){

        return $this->belongsTo(User::class, 'user_id');
    }



    public function getReasonsAttribute()
    {
        $ids = explode(',', $this->reasons_ids);
        return ItemsCategories::whereIn('id', $ids)->get();
    }
   
    






}


