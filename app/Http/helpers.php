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

function kaheel_msg(string $ar, string $en): string
{
    return app()->getLocale() === 'en' ? $en : $ar;
}

function kaheel_iso($time): ?string
{
    if (!$time) {
        return null;
    }
    $carbon = $time instanceof \Carbon\CarbonInterface ? $time->copy() : \Carbon\Carbon::parse($time);

    return $carbon->utc()->format('Y-m-d\TH:i:s\Z');
}

function kaheel_local_time($time, ?string $locale = null): string
{
    if (!$time) {
        return '';
    }
    $locale = $locale ?: app()->getLocale();
    $carbon = $time instanceof \Carbon\CarbonInterface ? $time->copy() : \Carbon\Carbon::parse($time);

    return $carbon->clone()->utc()->timezone('Asia/Riyadh')
        ->locale($locale === 'en' ? 'en' : 'ar')
        ->isoFormat($locale === 'en' ? 'D MMMM YYYY, HH:mm' : 'D MMMM YYYY، HH:mm');
}

function kaheel_customer(): ?\App\Models\KaheelCustomerSession
{
    $id = session('kaheel_customer_session_id');
    $sid = session()->getId();

    $row = $id
        ? \App\Models\KaheelCustomerSession::query()->find($id)
        : \App\Models\KaheelCustomerSession::query()->where('session_id', $sid)->latest('id')->first();

    if (!$row) {
        session()->forget('kaheel_customer_session_id');

        return null;
    }

    if ($row->session_id !== $sid) {
        $row->session_id = $sid;
        $row->save();
    }

    session(['kaheel_customer_session_id' => $row->id]);

    return $row;
}

function kaheel_upload_parts($file): array
{
    $mime = strtolower(trim(explode(';', (string) $file->getMimeType())[0]));
    $client = strtolower(trim(explode(';', (string) $file->getClientMimeType())[0]));
    $ext = strtolower((string) $file->getClientOriginalExtension());
    if (in_array($mime, ['image/jpg', 'image/pjpeg', 'image/jfif'], true)) {
        $mime = 'image/jpeg';
    }
    if (in_array($client, ['image/jpg', 'image/pjpeg', 'image/jfif'], true)) {
        $client = 'image/jpeg';
    }

    return [$mime, $client, $ext];
}

function kaheel_detected_weak(string $mime): bool
{
    return in_array($mime, ['', 'application/octet-stream', 'binary/octet-stream', 'application/x-empty', 'application/x-download'], true);
}

function kaheel_upload_blocked(string $mime, string $client, string $ext): bool
{
    foreach (['svg', 'html', 'htm', 'php', 'js', 'xml'] as $bad) {
        if ($ext === $bad || str_contains($mime, $bad) || str_contains($client, $bad)) {
            return true;
        }
    }

    return false;
}

function kaheel_file_is_pdf(string $mime, string $client, string $ext): bool
{
    $pdf = ['application/pdf', 'application/x-pdf'];
    if (in_array($mime, $pdf, true)) {
        return true;
    }

    return kaheel_detected_weak($mime) && (in_array($client, $pdf, true) || $ext === 'pdf');
}

function kaheel_file_is_image(string $mime, string $client, string $ext): bool
{
    if (str_contains($mime, 'svg') || str_contains($client, 'svg') || in_array($ext, ['svg', 'svgz'], true)) {
        return false;
    }
    if (str_starts_with($mime, 'image/')) {
        return true;
    }
    $extensions = ['jpg', 'jpeg', 'jpe', 'jfif', 'png', 'webp', 'gif', 'bmp', 'tif', 'tiff', 'heic', 'heif', 'avif'];

    return kaheel_detected_weak($mime) && (str_starts_with($client, 'image/') || in_array($ext, $extensions, true));
}

function kaheel_document_allowed($file): bool
{
    if (!$file || !$file->isValid()) {
        return false;
    }
    [$mime, $client, $ext] = kaheel_upload_parts($file);
    if (kaheel_upload_blocked($mime, $client, $ext)) {
        return false;
    }

    return kaheel_file_is_image($mime, $client, $ext) || kaheel_file_is_pdf($mime, $client, $ext);
}

function kaheel_stored_mime($file): string
{
    [$mime, $client, $ext] = kaheel_upload_parts($file);
    if (kaheel_file_is_pdf($mime, $client, $ext)) {
        return 'application/pdf';
    }
    if (str_starts_with($mime, 'image/') && !str_contains($mime, 'svg')) {
        return $mime;
    }
    if (kaheel_detected_weak($mime) && str_starts_with($client, 'image/') && !str_contains($client, 'svg')) {
        return $client;
    }
    $map = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpe' => 'image/jpeg', 'jfif' => 'image/jpeg',
        'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif', 'bmp' => 'image/bmp',
        'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'heic' => 'image/heic', 'heif' => 'image/heif',
        'avif' => 'image/avif',
    ];

    return $map[$ext] ?? ($mime ?: 'application/octet-stream');
}

function kaheel_order_start_url(?string $pkg = null): string
{
    $pkg = strtoupper(trim((string) $pkg));
    $query = $pkg !== '' ? ['pkg' => $pkg] : [];
    if (kaheel_customer()) {
        return url(app()->getLocale().'/order'.($query ? '?'.http_build_query($query) : ''));
    }

    $query['redirect'] = '/order';

    return url(app()->getLocale().'/auth?'.http_build_query($query));
}

