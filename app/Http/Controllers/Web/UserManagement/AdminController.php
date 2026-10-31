<?php

namespace App\Http\Controllers\Web\UserManagement;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\Admin;
use App\Models\ModelHasRole;

use DB;



class AdminController extends Controller
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
        $id = Auth::guard('admin')->id();
        $user = Admin::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email'=>'required|email|unique:users',
            'mobile'=>'required|numeric|min:8|unique:users',
            'role_id' => 'required' ,
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = New Admin;
                $item->company_id = company_auth_id();
                $item->mobile = $request->mobile;
                $item->email = $request->email;
                $item->name = $request->name;
                $item->password = bcrypt($request->password);
                $item->pwd = $request->password;
                $item->role_id = $request->role_id;
                $item->created_by = $id;
               
                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/admin');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName)){$item->logo='uploads/admin/'.$fileName;}


                $item->save();

                if(isset($request->role_id) && $request->role_id != ''){
                   $role = New ModelHasRole;
                    $role->model_id = $item->id;
                    $role->model_type = 'App\Model\Admin';
                    $role->role_id = $request->role_id;
                    $role->save(); 
                }
                

                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }



    public function edit(Request $request)
    {
         $id = Auth::guard('admin')->id();
        $user = Admin::query()->findOrFail($request->Item_id);

       $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'required|numeric|min:8|unique:users,mobile,' . $user->id,
            'role_id' => 'required',
            // 'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
        ]);





        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = Admin::findOrFail($request->Item_id);
                $item->mobile = $request->mobile;
                $item->email = $request->email;
                $item->name = $request->name;
                $item->role_id = $request->role_id;
                $item->created_by = $id;
               
                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/admin');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName)){$item->logo='uploads/admin/'.$fileName;}


                $item->save();

                if(isset($request->role_id) && $request->role_id != ''){
                    ModelHasRole::where('model_id',$item->id)->delete();
                   $role = New ModelHasRole;
                    $role->model_id = $item->id;
                    $role->model_type = 'App\Model\Admin';
                    $role->role_id = $request->role_id;
                    $role->save(); 
                }
                

                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


     public function edit_password(Request $request)
    {
         $id = Auth::guard('admin')->id();
        $user = Admin::query()->findOrFail($request->Item_id);

       $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
        ]);





        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = Admin::findOrFail($request->Item_id);
                $item->password = bcrypt($request->password);
                $item->pwd = $request->password;

                $item->save();

                

                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


    public function getAll(Request $request)
        {
            $id = Auth::guard('admin')->id();

            $items = Admin::query();


            if(isset($request->ID) && $request->ID!= ''){

                $items->where('id',$request->ID);
            }else{
                $items->where('company_id', company_auth_id());
            }

             

            if($request->has('search') && !empty($request->search)) {
                $items->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%')
                          ->orWhere('mobile', 'like', '%' . $request->search . '%');
                });
            }

            if(isset($request->pagination) && $request->pagination == 1) {
                $items = $items->orderBy('id','DESC')->paginate(10); 
            } else {
                $items = $items->orderBy('id','DESC')->get();
            }

            $message = "success return";

            return mainResponse(true, $message, $items, 200, 'items', '');
        }




    public function getById(Request $request)
    {
        $items = Admin::query()->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {   
        Admin::where('id',$id)->delete();
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , '', 200, 'items','');  
    
    }




    public function getAuth(){

        $id = Auth::guard('admin')->id();
        $items = Admin::query()->findOrFail($id);
        if (($items->status ?? 'active') === 'not_active') {
            $items->tokens()->delete();
            Auth::guard('admin')->logout();

            return mainResponse(false, kaheel_msg('تم إيقاف هذا الحساب ولا يمكن استخدامه.', 'This account has been disabled.'), null, 401, 'items', '');
        }
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , $items, 200, 'items','');

    }

    

}