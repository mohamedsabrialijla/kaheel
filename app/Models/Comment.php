<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

use DB;

class Comment extends Model
{
    protected $fillable = ['ad_id', 'user_id', 'body'];
    protected $appends = ['isOwner','created_at_human'];

    protected $casts = [
        'ad_id'     => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }


    public function getIsOwnerAttribute(){
        $ad =  DB::table('ads')->where('id',$this->ad_id)->first();
        if($ad->user_id == $this->user_id){
            return true;
        }else{
            return false;
        }
    }

    public function getCreatedAtHumanAttribute()
    {
        Carbon::setLocale(App::getLocale());

        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
