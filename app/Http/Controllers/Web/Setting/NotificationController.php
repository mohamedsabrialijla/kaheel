<?php

namespace App\Http\Controllers\Web\Setting;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Notification;
use App\Models\DeviceToken;
use App\Services\FcmNotificationService;


class NotificationController extends Controller
{
    



    public function __construct()
    {
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
    $targetType = $request->target_type === 'spesific' ? 'user' : $request->target_type;

    $userIds = collect($request->users ?? [])
        ->map(function ($user) {
            if (is_array($user)) {
                return $user['id'] ?? null;
            }
            if (is_object($user)) {
                return $user->id ?? null;
            }
            return $user;
        })
        ->filter()
        ->values()
        ->toArray();

    $rules = [
        'title' => 'required|string',
        'message' => 'required|string',
        'target_type' => 'required|in:all,user',
    ];

    $validationData = [
        'title' => $request->title,
        'message' => $request->message,
        'target_type' => $targetType,
    ];

    if ($targetType === 'user') {
        $validationData['users'] = $userIds;
        $rules['users'] = 'required|array|min:1';
        $rules['users.*'] = 'exists:users,id';
    }

    $validator = Validator::make($validationData, $rules);

    if ($validator->fails()) {
        return mainResponse(false, $validator->errors()->first(), null, 422, 'items', $validator);
    }

    if (!FcmNotificationService::credentialsAvailable()) {
        return mainResponse(false, 'ملف Firebase credentials غير موجود على السيرفر.', null, 500, 'items', '');
    }

    $tokens = $targetType === 'all'
        ? FcmNotificationService::allActiveTokens()
        : FcmNotificationService::tokensForUserIds($userIds);

    if (empty($tokens)) {
        return mainResponse(false, __('common.no_tokens_found') ?: 'لا يوجد device token مسجّل للمستخدمين المحددين.', null, 422, 'items', '');
    }

    $notifyUserIds = $targetType === 'all' ? User::pluck('id')->toArray() : $userIds;

    // الحفظ قبل الإرسال حتى يدخل هذا الإشعار ضمن unread_count في الـ payload
    $notification = new Notification();
    $notification->title = $request->title;
    $notification->body = $request->message;
    $notification->target_type = $targetType;
    $notification->users = $targetType === 'user' ? json_encode($userIds) : null;
    $notification->save();

    $now = now();
    foreach (array_chunk($notifyUserIds, 500) as $chunk) {
        DB::table('notification_user')->insert(array_map(fn ($userId) => [
            'user_id' => $userId,
            'notification_id' => $notification->id,
            'read' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ], $chunk));
    }

    $pushData = [
        'notification_id' => $notification->id,
        'action' => '',
        'action_id' => '',
        'action_url' => '',
    ];

    $sendResult = FcmNotificationService::sendToUsersWithUnreadCount(
        $notifyUserIds,
        $request->title,
        $request->message,
        $pushData
    );

    if ($targetType === 'all') {
        $guestTokens = DeviceToken::query()->whereNull('user_id')->where('is_active', true)->pluck('device_token')->all();
        if (!empty($guestTokens)) {
            $guestResult = FcmNotificationService::sendToTokens(
                $guestTokens,
                $request->title,
                $request->message,
                array_merge($pushData, ['unread_count' => 0])
            );
            $sendResult['success'] += $guestResult['success'];
            $sendResult['failed'] += $guestResult['failed'];
            $sendResult['errors'] = array_merge($sendResult['errors'], $guestResult['errors']);
        }
    }

    $payload = [
        'sent' => $sendResult['success'],
        'failed' => $sendResult['failed'],
        'total_tokens' => count($tokens),
        'errors' => array_slice($sendResult['errors'], 0, 3),
    ];

    if ($sendResult['success'] === 0) {
        $message = FcmNotificationService::getLastError()
            ?: ($sendResult['errors'][0] ?? 'فشل إرسال الإشعار عبر FCM.');
        return mainResponse(false, $message, $payload, 422, 'items', '');
    }

    $message = __('common.success') . " ({$sendResult['success']}/" . count($tokens) . ')';
    if ($sendResult['failed'] > 0) {
        $message .= ' — بعض الأجهزة فشلت.';
    }

    return mainResponse(true, $message, $payload, 200, 'items', '');
}





   

    public function getAll(Request $request)
    {




// $serviceAccount = json_decode(file_get_contents(storage_path('app/firebase-service-account.json')), true);

//  $jwt = \Firebase\JWT\JWT::encode([
//         "iss" => $serviceAccount['client_email'],
//         "scope" => "https://www.googleapis.com/auth/firebase.messaging",
//         "aud" => "https://oauth2.googleapis.com/token",
//         "iat" => time(),
//         "exp" => time() + 3600
//     ], $serviceAccount['private_key'], 'RS256');

//     $tokenResponse = Http::asForm()->withOptions([
//         'verify' => false,  // ⬅ هنا نعطل SSL
//     ])->post('https://oauth2.googleapis.com/token', [
//         'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
//         'assertion' => $jwt,
//     ]);

//     if (!$tokenResponse->successful()) {
//         return mainResponse(false, "Cannot get FCM access token", "", 500, "items", "");
//     }

//     $accessToken = $tokenResponse->json()['access_token'];

// $deviceToken = "d17jB9uZ-Eyxm_APgJMcZy:APA91bEq08xRnHaFNltfDk9yuxgMUG_S-Xk2CLQbDN_GgTPL3cigfj3bRC0NInVxTRElU1FyIrD6qjFT-Nn7ECNzdYy_Le8YZZKa25ThdY6n5bNBKSGsz44"; // توكن جهاز المستخدم

// $data = [
//     "message" => [
//         "token" => $deviceToken,
//         "notification" => [
//             "title" => "Test Title",
//             "body" => "Test message",
//         ],
//         "data" => [
//             "click_action" => "FLUTTER_NOTIFICATION_CLICK"
//         ]
//     ]
// ];

// $response = Http::withHeaders([
//     "Authorization" => "Bearer $accessToken",
//     "Content-Type" => "application/json",
// ])->withOptions([
//     "verify" => false, // ⬅ تعطيل SSL مؤقتًا
// ])->post("https://fcm.googleapis.com/v1/projects/{$serviceAccount['project_id']}/messages:send", $data);

// dd($response);

























         $id = auth('sanctum')->id();

            $items = Notification::query();

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



   
    public function delete($id)
    {
        if(Notification::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }

    

}