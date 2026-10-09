<?php

namespace App\Http\Controllers\Web\Setting;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Page;



class PageController extends Controller
{
  
     public function __construct() {
        // $this->middleware('auth', ['except' => ['login', 'register']]);

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

        $validator = Validator::make($request->all(), [
            // 'code' => 'required|alpha_num|unique:departments',
        ]);

        foreach ($this->locales as $locale) {
             $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
            $roles['key_words_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
               $page = New Page();
                foreach ($this->locales as $locale) {
                    $page->translateOrNew($locale->lang)->name = ucwords($request->get('name_' . $locale->lang));
                    // $slugify = new Slugify();
                    $slug = $request->get('name_' . $locale);
                    $page->translateOrNew($locale->lang)->slug = trim($request->get('name_' . $locale->lang));
                    $page->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                    $page->translateOrNew($locale->lang)->key_words = $request->get('key_words_' . $locale->lang);
                   
                }



                    $page->save();




                $message = __('common.success');
                return mainResponse(true, $message , $page, 200, 'items','');

               

       
        }
    }



    public function edit(Request $request)
    {
        // dd($request->all());
      
        $id = auth('sanctum')->id();

        $validator = Validator::make($request->all(), [
            // 'code' => 'required|alpha_num|unique:departments',
        ]);

        foreach ($this->locales as $locale) {
             $roles['name_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
            $roles['key_words_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
               $page = Page::findOrFail($request->Item_id);
                foreach ($this->locales as $locale) {
                    $page->translateOrNew($locale->lang)->name = ucwords($request->get('name_' . $locale->lang));
                    // $slugify = new Slugify();
                    $slug = $request->get('name_' . $locale);
                    // $page->translateOrNew($locale->lang)->slug = trim($request->get('name_' . $locale->lang));
                    $page->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                    $page->translateOrNew($locale->lang)->key_words = $request->get('key_words_' . $locale->lang);
                   
                }



                    $page->save();




                $message = __('common.success');
                return mainResponse(true, $message , $page, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = Page::query();

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
        $items = Page::where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        if(Page::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }

    

}