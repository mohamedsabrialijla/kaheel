<?php

namespace App\Http\Controllers\Web\HR;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\User;
use App\Models\EmployeePosition;
use App\Models\ModelHasRole;
use DB;


class EmployeePositionsController extends Controller
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
            'position_id' => 'required' ,
            'employee_id' => 'required' ,
           
        ]); 




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = New EmployeePosition;
                $item->company_id = company_auth_id();
                $item->position_id = $request->position_id;
                $item->employee_id = $request->employee_id;
                $item->created_by = $id;
                $item->save();


                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
       
        }
    }



    public function edit(Request $request)
    {
         $id = auth()->id();

        $item = EmployeePosition::query()->findOrFail($request->Item_id);


        $validator = Validator::make($request->all(), [
           'position_id' => 'required' ,
            'employee_id' => 'required' ,
           
        ]); 




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

                $item = EmployeePosition::findOrFail($request->Item_id);
                $item->company_id = company_auth_id();
                $item->position_id = $request->position_id;
                $item->employee_id = $request->employee_id;
                $item->created_by = $id;+

                $item->save();

            }
                $message ="success opearation";
                return mainResponse(true, $message , $item, 200, 'items','');
    }
 


    public function getAll(Request $request)
        {
            $id = auth('sanctum')->id();

            $items = EmployeePosition::with('position','employee')->where('company_id', company_auth_id());

            if ($request->has('search') && !empty($request->search)) {
                $search = '%' . $request->search . '%';

                $items->whereHas('employee', function($q)use($search){
                    $q->where('full_name', 'like', $search);
                })->orWhereHas('position', function ($q2) use ($search) {
                        $query2->whereTranslationLike('name', $search);
                }); 

                // $items->where(function ($query) use ($search) {
                //     $query->where('code', 'like', $search)  
                //           ->orWhere('first_name','like', $search)
                //           ->orWhere('middle_name','like', $search)
                //           ->orWhere('last_name','like', $search)
                //           ->orWhere('mobile','like', $search);
                // });
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
        $items = EmployeePosition::with('position','employee')->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {   
        EmployeePosition::where('id',$id)->delete();
        $message ="Ddeleted Item successfully";
        return mainResponse(true, $message , '', 200, 'items','');  
    
    }

    

}