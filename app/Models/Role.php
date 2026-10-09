<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{

    protected $appends = ['by' , 'Permission_check','permissions'];

    public function getByAttribute() {
       $f_name= User::where('id',$this->created_by)->pluck('name')->first();
    
       return $f_name ;
    }
	
    public function getCreatedAtAttribute($value){
        return date('Y-m-d H:i', strtotime($value));
    }


    public function getPermissionCheckAttribute() {
       $ides_permision =  role_has_permissions::where('role_id',$this->id)->pluck('permission_id');
       return $ides_permision;
    }


     public function getpermissionsAttribute() {
       
        $all_permissions = Permission::whereNull('parent_id')->get();
        $role_permissions = $this->permission_check;

        // دالة لتحديث status لكل مستوى من الصلاحيات
        $updateStatus = function ($permissions) use ($role_permissions, &$updateStatus) {
            return $permissions->map(function ($permission) use ($role_permissions, $updateStatus) {
                // إضافة status بناءً على وجود الصلاحية في role_permissions
                $permission->checked = $role_permissions->contains($permission->id) ? true : false;

                // التحقق من الصلاحيات الفرعية (child, deep_child, child_permissions)
                if (isset($permission->child) && $permission->child != '' ) {
                    $permission->child = $updateStatus(collect($permission->child))->toArray();
                }
               

                return $permission;
            });
        };

        // تطبيق الدالة على كافة الصلاحيات
        return $updateStatus($all_permissions);
    
    }



}
