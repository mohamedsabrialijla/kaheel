<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Language;





class HomeController extends Controller
{
    public function __construct()
    {
        view()->share([
             'locales' => Language::all(),
         ]);


    } 


    public function showLandingPage()
    {
        return view('website.landing');
    }



public function handle(Request $request)
{
    $message = strtolower($request->input('Body'));
    $from = $request->input('From');
    
    
    
   Log::info("Received message: {$message}");
    Log::info("Sess ion status: " . session('status'));

    // استرجاع الحالة الحالية من الجلسة
    $status = session('status', 'initial'); 
    $contractOrProject = session('contractOrProject', null);



   Log::info("Received message: {$message}");
    Log::info("Sess ion status: " . session('status'));
    $reply = '';
    
    if ($status == 'initial') {
                    Log::info("Receivtest");

        if (str_contains($message, 'مرحبا') || str_contains($message, 'السلام عليكم')) {
            $reply = "أهلا بك تفضل بطلب المساعدة ";
        } elseif (str_contains($message, 'عقد') || str_contains($message, 'مشروع')) {
            $reply = "يرجى تزويدي باسم العقد أو المشروع";
            session(['status' => 'awaiting_contract_name']);
        } else {
            $reply = "عذرًا، لم أفهم طلبك. يرجى إعادة المحاولة.";
        }
    } elseif ($status == 'awaiting_contract_name') {
        if ($this->isProjectName($message)) {
            $reply = "يرجى تزويدي بكود العقد.";
            session(['status' => 'awaiting_contract_code']);
        } else {
            $reply = "عذرًا، لم أتمكن من العثور على العقد أو المشروع. يرجى المحاولة مجددًا.";
        }
    } elseif ($status == 'awaiting_contract_code') {
        if ($this->isProjectCode($message)) {
            $reply = "يرجى تزويدي بكود الصلاحية.";
            session(['status' => 'awaiting_user_code']);
        } else {
            $reply = "كود العقد غير صحيح. يرجى إعادة المحاولة.";
        }
    } elseif ($status == 'awaiting_user_code') {
        if ($this->isUserCode($message)) {
            $reply = "ما هي المعلومات التي تريدها (مثل التاريخ أو المدة)؟";
            session(['status' => 'awaiting_info_request']);
        } else {
            $reply = "كود الصلاحية غير صحيح. يرجى إعادة المحاولة.";
        }
    } elseif ($status == 'awaiting_info_request') {
        $reply = $this->getProjectDetails($message, session('contractOrProject'));
        // session(['status' => 'initial']); // إعادة تعيين الحالة إلى البداية بعد الإجابة
    }
    
    
   Log::info("Received message: {$reply}");
    Log::info("Sess ion status: " . session('status'));

    $this->sendWhatsAppMessage($from, $reply);
}




     protected function sendWhatsAppMessage($to, $message)
    {
        $twilioSid = config('services.twilio.sid');
        $twilioAuthToken = config('services.twilio.token');
        $twilio = new Client($twilioSid, $twilioAuthToken);

        $twilio->messages->create($to, [
            "from" => config('services.twilio.from'),
            "body" => $message
        ]);
    }

    protected function isProjectName($message) {
        return Contract::where('Contract Name','like', '%'.$message.'%')->exists();
    }
    
    protected function isProjectCode($message) {
        // تحقق من اسم المشروع
        session(['contractOrProject_id' => Contract::where('Contract_No',$message)->pluck('id')->first()]);
        return Contract::where('Contract_No',$message)->exists();
    }



  


    protected function isUserCode($message) {
        // تحقق من كود الصلاحية
        return Admin::where('username', $message)->exists();
    }

   protected function getProjectDetails($message)
{
    $value = session('contractOrProject_id', 'default');
    $contract = Contract::where('id', $value)->first();
    
    if (!$contract) {
        return "لم يتم العثور على مشروع بهذا الكود.";
    }

  
       if (str_contains(strtolower($message), 'التاريخ')) {
            return "تاريخ العقد: " . $contract->{"Start Date"} . " - " . $contract->{"End Date"};
        } elseif (str_contains(strtolower($message), 'الفترة') || str_contains(strtolower($message), 'المدة') || str_contains(strtolower($message), 'duration')) {
            return "مدة العقد: " . $contract->{"Duration"};
        } elseif (str_contains(strtolower($message), 'بائع')) {
            return "البائع: " . $contract->{"Vendor"};
        } elseif (str_contains(strtolower($message), 'نوع العقد')) {
            return "نوع العقد: " . $contract->{"Contract Type"};
        }elseif (str_contains(strtolower($message), 'حالة')) {
            return "الحالة: " . $contract->{"Status"};
        }elseif (str_contains(strtolower($message), 'منطق') || str_contains(strtolower($message), 'دول')) {
            return "المنطقة : " . $contract->{"Country/Region Name"};
        }elseif (str_contains(strtolower($message), 'يام')) {
            return "الأيام: " . $contract->{"Days Left"};
        }elseif (str_contains(strtolower($message), 'شكر') || str_contains(strtolower($message), 'thank')) {
            session(['status' => 'initial']);
            return "العفو " ;

        }
    
    

    return "عذرًا، لم أتمكن من تحديد المعلومات المطلوبة. يرجى التحقق من الطلب وإعادة المحاولة.";
}


    
    
}
