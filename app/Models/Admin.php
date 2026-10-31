<?php

namespace App\Models;


use App\Models\Permission;
use App\Models\Subscribe;
use App\Models\ModelHasRole;
use App\Models\role_has_permissions;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;




class Admin extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;


    protected $fillable = [
        'name', 'email', 'password', 'mobile', 'profile_image'];
        
    protected $guard_name = 'admin';    

    protected $appends = ['role','permission','role_name'];

 
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function getLogoAttribute($value)
    {
        if ($value != null)
            return url($value);
        return "";
    }
    


      public function getRoleAttribute()
    {
        $role = ModelHasRole::where('model_id', $this->id)->pluck('role_id')->first()
            ?: ($this->attributes['role_id'] ?? null);
        if (!$role) {
            return [];
        }
        $active = \Illuminate\Support\Facades\DB::table('roles')
            ->where('id', $role)
            ->where('is_active', true)
            ->exists();
        if (!$active) {
            return [];
        }
        $Permissions = role_has_permissions::where('role_id',$role)->pluck('permission_id')->toArray();
        $per = Permission::whereIn('id',$Permissions)->pluck('name')->toArray();
        
        return $per;
    }



    public function getPermissionAttribute()
    {
        $p = ModelHasRole::where('model_id',$this->id)->first();
        return $p ;
        
    }

    public function getRoleNameAttribute()
    {
        $p = ModelHasRole::where('model_id',$this->id)->first();
        if($p != '')
            return Role::where('id',$p->role_id)->first();
        return '';
        
    }


    public function subscribe(){

        return $this->hasOne(Subscribe::class,'company_id')->where('status','active')->orderBy('id','desc')->latest();
    }
    


     
    
}
