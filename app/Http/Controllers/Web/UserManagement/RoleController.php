<?php

namespace App\Http\Controllers\Web\UserManagement;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\User;
use App\Models\Role;
use App\Models\model_has_roles;
use App\Models\Permission;
use App\Models\role_has_permissions;


use DB;



class RoleController extends Controller
{
  
     public function __construct() {

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    public function image_extensions(){
        return array('jpg','png','jpeg','gif','bmp');
    }

   


    public function create(Request $request)
    {
        // dd($request->all());
        $id = auth()->id();
        $user = User::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions'=> ' required|array',
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                
        $role_name = str_replace(' ','-',$request->name);


        $item= New Role() ;

        $item->display_name = $request->name;
        $item->name = $role_name;
        $item->guard_name = 'user';
        $item->company_id = company_auth_id();
        $item->save();

         if($item){
            if($request->permissions != ''){
                foreach ($request->permissions as $key => $value) {
                    if(isset($value['checked']) && $value['checked']){
                        $items = New role_has_permissions();
                        $items->permission_id = $value['id'] ;
                        $items->role_id = $item->id ;
                        $items->save();
                    }
                   

                    foreach ($value['child'] as $key2 => $value2) {
                        if(isset($value2['checked']) && $value2['checked'] ){
                            $items2 = New role_has_permissions();
                            $items2->permission_id = $value2['id'] ;
                            $items2->role_id = $item->id ;
                            $items2->save();
                        }
                    }
               }
            }
        }

               
        $message ="success opearation";
        return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }



    public function edit(Request $request)
    {
        // dd($request->all());
        $id = auth()->id();
        $user = User::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'name' => 'required',
            'permissions'=> ' required|array',
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                
        $role_name = str_replace(' ','-',$request->name);


        $item= Role::findOrFail($request->item_id) ;

        $item->display_name = $request->name;
        $item->name = $role_name;
        $item->guard_name = 'user';
        $item->company_id = company_auth_id();
        $item->save();

         if($item){
            role_has_permissions::where('role_id',$item->id)->delete();
            if($request->permissions != ''){
                foreach ($request->permissions as $key => $value) {
                    if(isset($value['checked']) && $value['checked']){
                        $items = New role_has_permissions();
                        $items->permission_id = $value['id'] ;
                        $items->role_id = $item->id ;
                        $items->save();
                    }
                   

                    foreach ($value['child'] as $key2 => $value2) {
                        if(isset($value2['checked']) && $value2['checked'] ){
                            $items2 = New role_has_permissions();
                            $items2->permission_id = $value2['id'] ;
                            $items2->role_id = $item->id ;
                            $items2->save();
                        }
                    }
               }
            }
        }

               
        $message ="success opearation";
        return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


    public function getAll(Request $request)
    {
        $id = auth('sanctum')->id();
        $items = Role::query()->where('company_id',company_auth_id())->orderBy('id','DESC');
        if(isset($request->pagination) && $request->pagination == 0){
              $items=$items->get();  
        }else{
           $items=$items->paginate(10); 
        }
        
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items','');
    }



    public function getById(Request $request)
    {
        // dd($request->all());
        $items = Role::where('id',$request->ID)->first();
        $message ="success return";
        // return $items;
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

     public function delete($id)
    {   
        Role::where('id',$id)->delete();
        role_has_permissions::where('role_id',$id)->delete();
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , '', 200, 'items','');  
    
    }  


    public function getAllPermission(){

        $items = Permission::where('parent_id', null)->with('childPermissions')->get();

        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items','');

    }


           
    
   
 
        
        
    

    

}