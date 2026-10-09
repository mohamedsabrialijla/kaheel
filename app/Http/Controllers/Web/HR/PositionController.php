<?php

namespace App\Http\Controllers\Web\HR;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Position;



class PositionController extends Controller
{
  
     public function __construct() {
        $this->middleware('auth', ['except' => ['login', 'register']]);

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
      
        $id = auth('sanctum')->id();

        $validator = Validator::make($request->all(), [
            'code' => 'required|alpha_num|unique:positions',
        ]);

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = New Position;
                $item->company_id = company_auth_id();
                $item->code = $request->code;
                $item->unit_rate_price = $request->unit_rate_price;
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                $item->save();

                $message ="تمت العملية بنجاح";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }

 

    public function edit(Request $request)
    {
      
        $id = auth('sanctum')->id();
        $item = Position::query()->findOrFail($request->Item_id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|alpha_num|unique:positions,code,' . $item->id, 
        ]);

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = Position::findOrFail($request->Item_id);
                $item->company_id = company_auth_id();
                $item->code = $request->code;
                $item->unit_rate_price = $request->unit_rate_price;
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                $item->save();

                $message ="تمت العملية بنجاح";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = Position::query()->where('company_id', company_auth_id());

            if ($request->has('search') && !empty($request->search)) {
                $search = '%' . $request->search . '%';

                $items->where(function ($query) use ($search) {
                    $query->where('code', 'like', $search)  // فحص الكود مباشرة هنا
                          ->orWhereTranslationLike('name', $search)
                          ->orWhereTranslationLike('description', $search);
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
        $items = Position::where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        if(Position::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }

    

}