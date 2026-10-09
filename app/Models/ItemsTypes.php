<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ItemsTypes extends Model
{


    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }
    
   
    






}


