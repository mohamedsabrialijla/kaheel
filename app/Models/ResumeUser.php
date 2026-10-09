<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ResumeUser extends Model
{
    use SoftDeletes;
    protected $fillable = ['order_by'];

    
    public function getCreatedAtAttribute($value){
        // return date('Y-m-d H:i', strtotime($value));
        return Carbon::parse($value)->translatedFormat('d F Y');

    }


    public function template()
    {
        return $this->belongsTo(TemplateResume::class,'resume_template_id');
    }
    

     public function getLogoCvAttribute($value)
    {
        return url($value);
        
    }





}


