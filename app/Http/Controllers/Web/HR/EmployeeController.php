<?php

namespace App\Http\Controllers\Web\HR;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\Employee;

use DB;



class EmployeeController extends Controller
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
        $id = auth()->id();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email'=>'required|email|unique:employees',
            'mobile'=>'required|min:8|unique:employees',
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
           
        ]);




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = New Employee;
                $item->mobile = $request->mobile;
                $item->email = $request->email;
                $item->name = $request->name;
                $item->password = bcrypt($request->password);
                $item->pwd = $request->password;
                $item->created_by = $id;
               
                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/employee');

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
                if(isset($fileName)){$item->logo='uploads/employee/'.$fileName;}


                $item->save();


                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }



    public function edit(Request $request)
    {
        // dd($request->all());
         $id = auth()->id();
        $user = Employee::query()->findOrFail($request->Item_id);

       $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:employees,email,' . $user->id,
            'mobile' => 'required|min:8|unique:employees,mobile,' . $user->id,
            'logo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048'
        ]);





        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = Employee::findOrFail($request->Item_id);
                $item->mobile = $request->mobile;
                $item->email = $request->email;
                $item->name = $request->name;
                $item->created_by = $id;
               
                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/employee');

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
                if(isset($fileName)){$item->logo='uploads/employee/'.$fileName;}


                $item->save();


                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


     public function edit_password(Request $request)
    {
         $id = auth()->id();
        $user = Employee::query()->findOrFail($request->Item_id);

       $validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
        ]);





        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = Employee::findOrFail($request->Item_id);
                $item->password = bcrypt($request->password);
                $item->pwd = $request->password;

                $item->save();

                

                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }


    public function getAll(Request $request)
        {
            $id = auth('sanctum')->id();

            $items = Employee::query();

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
        $items = Employee::query()->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {   
        Employee::where('id',$id)->delete();
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , '', 200, 'items','');  
    
    }


 

    public function getAuth(){

        $id = auth('sanctum')->id();
        $items = Employee::query()->findOrFail($id);
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , $items, 200, 'items','');

    }


    function deleteAllItem(Request $request){

        $selectedItems = explode(',', request()->query('selectedItems'));
        $action = $request->action ;
        

        try {

            if($action == 'delete'){
                Employee::whereIn('id',$selectedItems)->delete();
            }elseif ($action == 'active') {
                Employee::whereIn('id',$selectedItems)->update(['status'=>'active']);
            }else{
                Employee::whereIn('id',$selectedItems)->update(['status'=>'not_active']);
            }

            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');

 
        } catch (Exception $e) {
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
            
        }



    }

    

}