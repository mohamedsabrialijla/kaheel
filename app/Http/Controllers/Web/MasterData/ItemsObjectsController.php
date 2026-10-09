<?php

namespace App\Http\Controllers\Web\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Language;
use App\Models\ItemsCategories;
use App\Models\ItemsObjects;
use App\Models\ItemsTypes;



class ItemsObjectsController extends Controller
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
        // dd($request->all());

        $id = auth('sanctum')->id();
        $type_id = ItemsTypes::where('name',$request->type)->pluck('id')->first();

        $validator = Validator::make($request->all(), [
            'code' => [
            'required',
            'alpha_num',
                Rule::unique('items_objects')->where(function ($query) use ($request,$type_id) {
                    return $query->where('type_id', $type_id);
                }),
            ],
            'link' => 'nullable|url',
            'logo' => 'nullable|file|max:2048'
        ]);


        if($request->type == 'standards' || $request->type == 'drawings' ){
            $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);


        }

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = New ItemsObjects;
                $item->company_id = company_auth_id();
                $item->code = $request->code;
                $item->link = $request->link;
                $item->category_id = $request->category_id;
                $item->type_id = $type_id;
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/objects/'.$request->category_id.'/');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        // if(in_array($extension,$array))
                        // {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        // }else{
                        //     return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        // }
                    }
                }

                if(isset($fileName)){$item->logo='uploads/objects/'.$request->category_id.'/'.$fileName;}



                $item->save();

                $message ="تمت العملية بنجاح";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }
 


    public function edit(Request $request)
    {
      // dd($request->all());
        $id = auth('sanctum')->id();
        $item = ItemsObjects::query()->findOrFail($request->Item_id);
        $type_id = ItemsTypes::where('name',$request->type)->pluck('id')->first();

        $validator = Validator::make($request->all(), [
            'code' => [
            'required',
            'alpha_num',
            Rule::unique('items_objects', 'code')
                ->ignore($item->id) // استثناء العنصر الحالي
                ->where(function ($query) use ($request,$type_id) {
                    return $query->where('type_id', $type_id); // التحقق من type_id
                }),
            ],
            

        ]);

         if($request->type == 'standards' || $request->type == 'drawings' ){
            $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'link' => 'nullable|url',
            'logo' => 'nullable|file|max:2048'
            ]);
        }


        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = ItemsObjects::findOrFail($request->Item_id);
                $item->company_id = company_auth_id();
                $item->code = $request->code;
                $item->category_id = $request->category_id;
                $item->type_id = $type_id;
                $item->link = $request->link;
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/objects/'.$request->category_id.'/');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        // if(in_array($extension,$array))
                        // {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        // }else{
                        //     return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        // }
                    }
                }

                if(isset($fileName)){$item->logo='uploads/objects/'.$request->category_id.'/'.$fileName;}


                $item->save();

                $message ="تمت العملية بنجاح";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = ItemsObjects::with('category','type')->where('company_id', company_auth_id());

            if($request->has('search') && !empty($request->search)) {
                $items->where(function($query) use ($request) {
                    $query->whereTranslationLike('name','%'.$request->search.'%')
                          ->orWhereTranslationLike('description', '%' . $request->search . '%');
                });
            }

            // return $items->get();


            if($request->has('object') && !empty($request->object)) {
                $items->whereHas('type', function($query) use ($request) {
                    $query->where('name',$request->object);
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
        $items = ItemsObjects::with('category')->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        if(ItemsObjects::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }

    

}