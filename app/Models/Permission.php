<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = ['name', 'display_name', 'parent_id'];

    public function childPermissions(){
        return $this->hasMany('App\Models\Permission', 'parent_id');
    }

    protected $appends = ['child'];

    public function getChildAttribute() {
        $children = $this->childPermissions;
        if(isset($children) && sizeof($children) > 0) {
            foreach ($children as $child) {
                $deep_child = Permission::where('parent_id', $child->id)->get();
                $child->deep_child = $deep_child;
            }
        }
        return $children;
    }


    
}
