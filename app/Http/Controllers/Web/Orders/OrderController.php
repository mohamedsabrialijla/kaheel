<?php

namespace App\Http\Controllers\Web\Orders;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Ad;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;



class OrderController extends Controller
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
            'images.*' => 'required|image|mimes:jpeg,jpg,png|max:2048',
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
                    $item->images()->delete();

                    foreach ($request->file('images') as $imageFile) {
                        $fileName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                        $destinationPath = public_path('uploads/ads');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0777, true);
                        }

                        $imageFile->move($destinationPath, $fileName);
                        $item->images()->create([
                            'url' => 'uploads/ads/' . $fileName
                        ]);
                    }
                }




                $message ="__('common.create')";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }


    public function getAll(Request $request)
    {
         $id = auth('sanctum')->id();

            $items = Payment::query()->with('user','ad.user','ad.category','ad.brand','payment_type');

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
        $items = Payment::with('user','ad','payment_type')->where('id',$request->ID)->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    // public function delete($id)
    // {
    //     if(Ad::where('id',$id)->delete()){
    //         $message ="success return";
    //         return mainResponse(true, $message , '', 200, 'items','');
    //     }else{
    //         $message ="Error happens";
    //         return mainResponse(false, $message , '', 203, 'items','');
    //     }
        
    // }




    public function show($id)
    {
        dd($id);
        $invoice = Payment::with('user','ad','payment_type')->findOrFail($id);
        return response()->json($invoice);
    }

    public function downloadPdf($id)
    {
        $invoice = Payment::with('user','ad','payment_type')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))->setPaper('a4');
        return $pdf->download("invoice-{$id}.pdf");
    }



    

    

}