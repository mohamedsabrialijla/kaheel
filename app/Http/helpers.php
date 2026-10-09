<?php


function company_auth_id(){

    $company_id = auth()->guard()->user()->company_id;

        if($company_id == '' ){
            $company_id = auth()->guard()->id();
        }


    return $company_id;

}





function image_extensions(){

    return array('jpg','png','jpeg','gif','bmp');

}

function admin_assets($dir)
{
    return url('/admin_assets/assets/' . $dir);
}

function getLocal()
{
    return app()->getLocale();
}


function mainResponse($status, $message, $data, $code, $key,$validator)
{
    try {
        $result['status'] = $status;
        $result['code'] = $code;
        $result['message'] = $message;

        if ($validator && $validator->fails()) {
            $errors = $validator->errors();
            $errors = $errors->toArray(); 
            $message = '';
            foreach ($errors as $key => $value) {
                $message .= $value[0] . ',';
            }
            // $result['message'] = $validator->errors();
            $result['message'] = $message;
            return response()->json($result, $code);
        }elseif (!is_null($data)) {


            if ($status) {
                if ($data != null && is_array($data) && array_key_exists('data', $data)) {
                    $result[$key] = $data['data'];
                } else {
                    $result[$key] = $data;
                }
            } else {
                $result[$key] = $data;
            }
        }
        return response()->json($result, $code);
    } catch (Exception $ex) {
        return response()->json([
            'line' => $ex->getLine(),
            'message' => $ex->getMessage(),
            'getFile' => $ex->getFile(),
            'getTrace' => $ex->getTrace(),
            'getTraceAsString' => $ex->getTraceAsString(),
        ], $code);
    }
}




function convertAr2En($string)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
    return $englishNumbersOnly;
}






function random_number($digits)
{
    return str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
}




function fcmPush($title,$message)

{ 
    
    try {

        
        $API_ACCESS_KEY = 'AAAAWTHvxJI:APA91bGaRqAm5ngFUMdY65nL58hHy_9RKaTyXzrDRRZy3j16J92-q1kPsTsDRfrt8VpqtvRQDMdtWDCmp2McleQwj3hwDVTQ1byeatycrscVUBv4m324xvgillViTpBorEoCYm04eR0D';

        $msg = [
            'body' => $message,
            'type' => "notify",
            // 'title' => $title,
            'title' => $title,
            'icon' => 'ic_launcher',//Default Icon
            'sound' => 'mySound',//Default sound
        ];
        //return $msg;
        $fields = [
            'registration_ids' => ["AAAAWTHvxJI:APA91bGaRqAm5ngFUMdY65nL58hHy_9RKaTyXzrDRRZy3j16J92-q1kPsTsDRfrt8VpqtvRQDMdtWDCmp2McleQwj3hwDVTQ1byeatycrscVUBv4m324xvgillViTpBorEoCYm04eR0D"],
            'data' => $msg
        ];
        $headers = [
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        ];
        
        
       
        
        //return $data;
        $notification= [
            "to"=> '/topics/seel',
            "notification"=>[
            'body' => $message,
                'type' => "notify",
                // 'title' => $title,
                'title' => $title,
                'icon' => 'myicon',//Default Icon
                'sound' => 'mySound'//Default sound
            ],
            
        ];
        //return $notification;
      // return json_encode($data);
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
        
        
        
        $result = curl_exec($ch);
        curl_close($ch);
        // dd(json_decode($result, true));
        // return json_decode($result, true);
        return back()->with('success','Edit SuccessFully');
    } catch (\Exception $ex) {
        return $ex->getMessage();
     }
     
}


 

// function branchSession(){
//     $id = auth('api')->id();
//     $branch_id_session = \App\Models\Branchs::where('company_id',$id)->where('status','active')->where('session',1)->pluck('id')->first();

//     $id = auth('api')->id();
//     $user = \App\Models\User::query()->findOrFail($id);
//     $user->session_branch = $branch_id_session ;
//     $user->save();

//     return $branch_id_session;
// }

function kaheel_customer(): ?\App\Models\KaheelCustomerSession
{
    $id = session('kaheel_customer_session_id');
    if (!$id) {
        return null;
    }

    $row = \App\Models\KaheelCustomerSession::query()->find($id);
    if (!$row || $row->session_id !== session()->getId()) {
        session()->forget('kaheel_customer_session_id');

        return null;
    }

    return $row;
}

