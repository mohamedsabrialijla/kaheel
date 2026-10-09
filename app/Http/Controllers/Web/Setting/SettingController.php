<?php

namespace App\Http\Controllers\Web\Setting;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Setting;



class SettingController extends Controller
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

   


    public function edit(Request $request)
    {
       // dd($request->all());
        $locales = Language::all()->pluck('lang');
        $roles = [
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
            'email' => 'required|email',
            'mobile' => 'required',
            'whatsapp' => 'required',
            'vat' => 'required',
            'offer_period' => 'required',
            'alert_delete_offer' => 'required',
            'days_delete_offer' => 'required',
            'refresh_reminder_days' => 'nullable|integer|min:0|max:365',
           
        ];
       
        foreach ($locales as $locale) {
            $roles['name_' . $locale] = 'required';
            $roles['key_words_' . $locale] = 'required';
            $roles['alert_gold_' . $locale] = 'required';
            $roles['condition1_' . $locale] = 'required';
            $roles['condition2_' . $locale] = 'required';
        }


        $this->validate($request, $roles);


        $setting = Setting::query()->findOrFail(1);
        $setting->play_store = trim($request->get('play_store'));
        $setting->apple_store = trim($request->get('apple_store'));
        $setting->email = trim($request->get('email'));
        $setting->mobile = trim($request->get('mobile'));
        $setting->whatsapp = trim($request->get('whatsapp'));
        $setting->vat = trim($request->get('vat'));
        $setting->offer_period = trim($request->get('offer_period'));
        $setting->alert_delete_offer = trim($request->get('alert_delete_offer'));
        $setting->days_delete_offer = trim($request->get('days_delete_offer'));
        if ($request->has('refresh_reminder_days')) {
            $setting->refresh_reminder_days = (int) $request->get('refresh_reminder_days');
        }




         if($request->file("logo")&&$request->file("logo")!= '' )
        {
            if ($request->file("logo")->isValid())
            {
                $destinationPath=public_path('uploads/settings');

                $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                //dd($extension);
              
                    $fileName_logo=uniqid().'.'.$extension;
                    $request->file("logo")->move($destinationPath, $fileName_logo);
                
            }
        }




        foreach ($locales as $locale) {
            $setting->translate($locale)->name = trim(ucwords($request->get('name_' . $locale)));
            $setting->translate($locale)->key_words = trim(ucwords($request->get('key_words_' . $locale)));
            $setting->translate($locale)->alert_gold = trim(ucwords($request->get('alert_gold_' . $locale)));
            $setting->translate($locale)->condition1 = ucwords($request->get('condition1_' . $locale));
            $setting->translate($locale)->condition2 = ucwords($request->get('condition2_' . $locale));
        }
        if(isset($fileName_logo)){$setting->logo= $fileName_logo;}
        $setting->save(); 

        $message = __('common.success');
        return mainResponse(true, $message, $setting, 200, 'items', '');    

    }


 



    public function getById(Request $request)
    {
        $items = Setting::query()->first();
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }


    

}