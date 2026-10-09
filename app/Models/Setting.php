<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use Translatable;
    public $translatedAttributes = ['name', 'description', 'address', 'key_words','condition1','condition2','alert_gold'];

    protected $appends=['video_url'];

 
    public function getLogoAttribute($logo)
    {
        //return url('uploads/settings/'.$logo);
        return !is_null($logo)?url('uploads/settings/'.$logo):null;
    }

   

    

   
    
     public function getVideoUrlAttribute()
    {
        $vedio = $this->vedio;
    	if($vedio){
    	//return $vedio;
    	if (isset(explode("v=",$vedio)[1])){
        $x = explode("v=",$vedio)[1];
        return 'https://www.youtube.com/embed/'.$x ;
        }else{
        
        return $vedio;
        }
        //return $x;
	//$xx = explode("&",$x)[0];
	

        }else{
        return ""; }
    }


}
