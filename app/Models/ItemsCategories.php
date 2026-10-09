<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ItemsCategories extends Model
{
    use SoftDeletes, Translatable;
    protected $fillable = ['order_by'];
    protected $hidden = [  'pivot', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name','description','position'];
    protected $appends=['embed_url','thumbnail_url'];

    public function scopePublic($query, $isActive = 'active', $orderBy = 'asc')
    {
        return $query->where(['status' => $isActive])->orderBy('order_by', $orderBy);
    }

    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }

    public function type()
    {
        return $this->belongsTo(ItemsTypes::class,'type_id');
    }



    public function country()
    {
        return $this->belongsTo(ItemsCategories::class,'country');
    }


    public function category()
    {
        return $this->belongsTo(ItemsCategories::class,'category');
    }



    public function metal()
    {
        return $this->belongsTo(ItemsCategories::class,'metal');
    }



    public function getEmbedUrlAttribute()
    {
        preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^\"&?\/\s]{11})/", $this->youtube_url, $matches);
        return $matches[1] ?? null;
    }



    public function getThumbnailUrlAttribute()
    {
        return "https://img.youtube.com/vi/{$this->embed_url}/hqdefault.jpg";
    }


     public function getLogoAttribute($value)
    {
        return url($value);
        
    }


    public function templates()
    {
        return $this->hasMany(TemplateResume::class,'category_id');
    }


    public function brands(){

        return $this->hasMany(ItemsCategories::class, 'category');
    }

    public function genders()
    {
        return $this->hasMany(ItemsCategories::class, 'category');
    }

    public function subcategories()
    {
        return $this->hasMany(ItemsCategories::class, 'category');
    }

    public function cities(){

        return $this->hasMany(ItemsCategories::class, 'country');
    }
    
   
    






}


