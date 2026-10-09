<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\User;
use App\Models\Role;
use App\Models\model_has_roles;
use App\Models\Permission;
use App\Models\role_has_permissions;
use Illuminate\Support\Facades\App;


use DB;



class LanguageController extends Controller
{
  
     public function __construct() {

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    

    public function getAllItems(Request $request)
    {
        $items = Language::where('status','active')->get();  
        
        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items','');
    }


public function changeItem(Request $request)
{


    $newLanguage = $request->input('lang');  
    
    $availableLanguages = ['en', 'ar']; 
    if (in_array($newLanguage, $availableLanguages)) {
        App::setLocale($newLanguage);
    } else {
        App::setLocale('en');
    }


    $items = Language::where('status', 'active')->get();  
    $message = "Success return";
    return mainResponse(true, $message, $items, 200, 'items', '');
}


public function translations(Request $request) {
    $locale = $request->locale;

    $availableLocales = ['en', 'ar'];
    if (!in_array($locale, $availableLocales)) {
        return response()->json(['error' => 'Locale not supported'], 400);
    }

     $translationsFile = resource_path("lang/{$locale}/common.php");
    $translationsFile = str_replace("\\", "/", $translationsFile); // التأكد من استبدال الفواصل

    // dd($translationsFile);

    if (!file_exists($translationsFile)) {
        return response()->json(['error' => 'Translations file not found'], 404);
    }


    $translations = require $translationsFile ;

    return response()->json($translations);
}

      
    
   
 
        
        
    

    

}