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



class PermissionController extends Controller
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
            'display_name'=> ' required',
            'parent_id'=> ' nullable',
            'descriptions'=> ' required',
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                
        $role_name = strtolower(str_replace(' ','-',$request->name));


        $item= New Permission() ;

        $item->display_name = $request->display_name;
        $item->name = $role_name;
        $item->guard_name = 'user';
        $item->parent_id = $request->parent_id ;
        $item->descriptions = $request->descriptions ;
        $item->company_id = company_auth_id();
        $item->save();

        
               
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
            'name' => 'required',
            'display_name'=> ' required',
            'parent_id'=> ' nullable',
            'descriptions'=> ' required',
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                
        $role_name = strtolower(str_replace(' ','-',$request->name));


        $item= Permission::findOrFail($request->Item_id) ;

        $item->display_name = $request->display_name;
        $item->name = $role_name;
        $item->guard_name = 'user';
        $item->parent_id = $request->parent_id ;
        $item->descriptions = $request->descriptions ;
        $item->company_id = company_auth_id();
        $item->save();

               
        $message ="success opearation";
        return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


    public function getAll(Request $request)
    {
        $id = auth('sanctum')->id();
        $items = Permission::query(); 

        if(isset($request->group) && $request->group == 1){
              $items=$items->whereNull('parent_id');  
        }elseif (isset($request->group) && $request->group == 0) {
            $items=$items->whereNotNull('parent_id');
        }


        if(isset($request->role_id) && $request->role_id != ''){
              $ides_permision =  DB::table('role_has_permissions')->where('role_id',$request->role_id)->pluck('permission_id');
              $items=$items->whereIn('id',$ides_permision);  
        }


        if(isset($request->admin) && $request->admin != 1){
              $items=$items->where('admin',0);  
        }



      



        if(isset($request->pagination) && $request->pagination == 0){
              $items=$items->orderBy('id','Asc')->get();  
        }else{
           $items=$items->orderBy('id','DESC')->paginate(10); 
        }


        


        
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items','');
    }



    public function getById(Request $request)
    {
        $items = Permission::where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

     public function delete($id)
    {   
        Permission::where('id',$id)->delete();
        $message ="Deleted Item successfully";
        return mainResponse(true, $message , '', 200, 'items','');  
    
    }  


  
 
        
        
    

    

}