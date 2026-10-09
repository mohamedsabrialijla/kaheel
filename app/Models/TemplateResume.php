<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TemplateResume extends Model
{
    use SoftDeletes, Translatable;
    protected $fillable = ['order_by'];
    protected $appends = [  'test'];
    protected $hidden = [  'pivot', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name','description'];

    public function scopePublic($query, $isActive = 'active', $orderBy = 'asc')
    {
        return $query->where(['status' => $isActive])->orderBy('order_by', $orderBy);
    }

    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }


    public function category()
    {
        return $this->belongsTo(ItemsCategories::class,'category_id');
    }
    

     public function getLogoAttribute($value)
    {
        return url($value);
        
    }


    public function getTagsEnAttribute($value)
    {
        return explode(',', $value);
        
    }


    public function getTestAttribute(){
        return json_decode($this->selectedSections, true);
    }

   
    
   
    






}


