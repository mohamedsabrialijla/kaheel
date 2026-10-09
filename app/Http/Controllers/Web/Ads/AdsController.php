<?php

namespace App\Http\Controllers\Web\Ads;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Ad;
use App\Models\Comment;



use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver; // لو بتستخدم GD




class AdsController extends Controller
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

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'product_type' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'invoice' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'images.*' => 'required|image|mimes:jpeg,jpg,png,bmp,webp,svg,gif|max:2048',
        ]);

        foreach ($this->locales as $locale) {
            $roles['title_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = New Ad;
                $item->user_id = $request->user_id;
                $item->category_id = $request->category_id;
                $item->brand_id = $request->brand_id;
                $item->country_id = $request->country_id;
                $item->city_id = $request->city_id;
                $item->price = $request->price;
                $item->product_type = $request->product_type;
                $item->mobile = $request->mobile;
              
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->title = $request->get('title_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }

                $item->save();


               if ($request->hasFile('invoice')) {
                    $file = $request->file('invoice');
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // تأكد من أن المجلد موجود أو أنشئه
                    $destinationPath = public_path('uploads/invoice');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    // نقل الملف إلى public/uploads/invoice
                    $file->move($destinationPath, $fileName);

                    // حفظ المسار في قاعدة البيانات
                    $item->invoice = 'uploads/invoice/' . $fileName;
                    $item->save();
                }


                // if ($request->hasFile('images')) {
                //     foreach ($request->file('images') as $imageFile) {
                //         $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                //         // تأكد من وجود المجلد
                //         $destinationPath = public_path('uploads/ads');
                //         if (!file_exists($destinationPath)) {
                //             mkdir($destinationPath, 0777, true);
                //         }

                //         // انقل الصورة
                //         $imageFile->move($destinationPath, $fileName);

                //         // احفظ المسار في قاعدة البيانات
                //         $item->images()->create([
                //             'url' => 'uploads/ads/' . $fileName
                //         ]);
                //     }
                // }


                if ($request->hasFile('images')) {
                    $manager = new ImageManager(Driver::class);

                    foreach ($request->file('images') as $imageFile) {
                        if (!$imageFile->isValid() || !in_array($imageFile->extension(), ['jpg', 'jpeg', 'png'])) {
                            return mainResponse(false, 'الملف المرفوع ليس صورة صالحة', null, 422, 'items', '');
                        }

                        $destinationPath = public_path('uploads/ads');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0777, true);
                        }

                        $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                        // ✅ قراءة الصورة
                        $img = $manager->read($imageFile->get());
                        $img = $img->cover(500, 500); // مربع

                        // ✅ قراءة وإضافة الشعار
                        $logoPath = public_path('assets/media/svg/brand-logos/logo_b.png');
                        $logo = $manager->read($logoPath);
                        $logo = $logo->scaleDown(50, 50);
                        $img = $img->place($logo, 'bottom-left', 10, 10);

                        // ✅ حفظ الصورة
                        $img->save($destinationPath . '/' . $fileName);

                        // ✅ ربط الصورة مع الإعلان
                        $ad->images()->create(['url' => 'uploads/ads/' . $fileName]);
                    }
                }




                $message ="__('common.create')";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


 
    public function edit(Request $request)
    {

      // dd($request->all());
        $id = auth('sanctum')->id();

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'product_type' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'invoice' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'images.*' => 'required|image|mimes:jpeg,jpg,png,bmp,webp,svg,gif|max:2048',
        ]);

        foreach ($this->locales as $locale) {
            $roles['title_' . $locale->lang] = 'required';
            $roles['description_' . $locale->lang] = 'required';
        }




        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = Ad::findOrFail($request->Item_id);
                $item->user_id = $request->user_id;
                $item->category_id = $request->category_id;
                $item->brand_id = $request->brand_id;
                $item->country_id = $request->country_id;
                $item->city_id = $request->city_id;
                $item->price = $request->price;
                $item->product_type = $request->product_type;
                $item->mobile = $request->mobile;
              
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->title = $request->get('title_' . $locale->lang);
                    $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }

                $item->save();


               if ($request->hasFile('invoice')) {
                    $file = $request->file('invoice');
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    $destinationPath = public_path('uploads/invoice');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $file->move($destinationPath, $fileName);
                    $item->invoice = 'uploads/invoice/' . $fileName;
                    $item->save();
                }


                if ($request->hasFile('images')) {
                    $manager = new ImageManager(Driver::class);

                    foreach ($request->file('images') as $imageFile) {
                        if (!$imageFile->isValid() || !in_array($imageFile->extension(), ['jpg', 'jpeg', 'png'])) {
                            return mainResponse(false, 'الملف المرفوع ليس صورة صالحة', null, 422, 'items', '');
                        }

                        $destinationPath = public_path('uploads/ads');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0777, true);
                        }

                        $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                        // ✅ قراءة الصورة
                        $img = $manager->read($imageFile->get());
                        $img = $img->cover(500, 500); // مربع

                        // ✅ قراءة وإضافة الشعار
                        $logoPath = public_path('assets/media/svg/brand-logos/logo_b.png');
                        $logo = $manager->read($logoPath);
                        $logo = $logo->scaleDown(50, 50);
                        $img = $img->place($logo, 'bottom-left', 10, 10);

                        // ✅ حفظ الصورة
                        $img->save($destinationPath . '/' . $fileName);

                        // ✅ ربط الصورة مع الإعلان
                        $item->images()->create(['url' => 'uploads/ads/' . $fileName]);
                    }
                }




                $message ="__('common.create')";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = Ad::query()->with('user','category','brand','country','city','images','comments','favoritedBy');

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
        $items = Ad::with('user','category','brand','country','city','images')->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        if(Ad::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }



    function deleteAllItem(Request $request){

        $selectedItems = explode(',', request()->query('selectedItems'));
        $action = $request->action ;
        // dd($action);
 
        try {

            if($action == 'delete'){
                Ad::whereIn('id',$selectedItems)->delete();
            }elseif ($action == 'active') {
                Ad::whereIn('id',$selectedItems)->update(['status'=>'active']);
            }else{
                Ad::whereIn('id',$selectedItems)->update(['status'=>'not_active']);
            }

            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');

 
        } catch (Exception $e) {
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
            
        }



    }




    public function deleteComment(Request $request)
    {
        $comment_id = $request->comment_id ;
        $comments = Comment::where('user_id',auth()->id())->where('id', $comment_id)->first();
        if(isset($comments) && $comments != '' ){
            $comments->delete();
        }else{

            return mainResponse(true, __('common.Not_Found'), '', 200, 'items', '');
        }

        return mainResponse(true, __('common.delete'), '', 200, 'items', '');
    }


    

}