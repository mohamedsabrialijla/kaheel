<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class EmployeePosition extends Model
{
    use SoftDeletes;



    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }

    public function Position()
    {
        return $this->belongsTo(Position::class,'position_id');
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    
   
    






}


