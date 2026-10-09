<?php

namespace App\Http\Controllers\Web\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\TemplateResume;



class ResumeTemplateController extends Controller
{
  
     public function __construct() {
        $this->middleware('auth', ['except' => ['login', 'register']]);

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    public function image_extensions(){
        return array('jpg','png','jpeg','gif','bmp', 'web', 'jfif');
    }

   


    public function create(Request $request)
    {
        // dd($request->all());
        $id = auth('admin')->id();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'content_html' => 'required',
            // 'tags_en' => 'nullable|array',
            // 'tags_ar' => 'nullable|array',
            'logo' => 'required|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{


                $tags_ar = $request->input('tags_ar');
                $tags_en = $request->input('tags_en');

                
                $item = New TemplateResume;
                $item->tags_en = $tags_en;
                $item->tags_ar = $tags_ar;
                $item->category_id = $request->category_id;
                $item->content_html = $request->content_html;
                $item->content_css = $request->content_css;
                $item->selectedSections = json_decode(json_encode($request->selectedSections) , true );
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                 if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/resume');

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
                if(isset($fileName)){$item->logo='uploads/resume/'.$fileName;}


                $item->save();

                $message = __('common.success');
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }



    public function edit(Request $request)
     {
        // dd($request->all());
        $id = auth('admin')->id();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'content_html' => 'required',
            // 'tags_en' => 'nullable|array',
            // 'tags_ar' => 'nullable|array',
            // 'logo' => 'required|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{


                $tags_ar = $request->input('tags_ar');
                $tags_en = $request->input('tags_en');

                
                $item = TemplateResume::findOrFail($request->Item_id);
                $item->tags_en = $tags_en;
                $item->tags_ar = $tags_ar;
                $item->category_id = $request->category_id;
                $item->content_html = $request->content_html;
                $item->content_css = $request->content_css;
                $item->selectedSections = json_decode(json_encode($request->selectedSections) , true );
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                 if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/resume');

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
                if(isset($fileName)){$item->logo='uploads/resume/'.$fileName;}


                $item->save();

                $message = __('common.success');
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = TemplateResume::with('category');

            if($request->has('search') && !empty($request->search)) {
                $items->where(function($query) use ($request) {
                    $query->whereTranslationLike('name','%'.$request->search.'%')
                          ->orWhereTranslationLike('description', '%' . $request->search . '%');
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
        $items = TemplateResume::with('category')->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        if(TemplateResume::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }  
    }

    

}