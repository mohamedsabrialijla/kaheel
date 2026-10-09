<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\CategoryGenderFilterTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\ItemsCategories;
use App\Models\ItemsTypes;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Notification;




use App\Mail\WelcomeUserMail;
use App\Mail\SendUserPasswordMail;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use DB;


 

class BasicController extends Controller
{
    use CategoryGenderFilterTrait;

  

    
    public function getCountries(Request $request)
    {

        $items = ItemsCategories::where('status','active');

        if(isset($request->country_id) && $request->country_id != ''){

            $items = $items->where('type_id',29)->where('country',$request->country_id)->get();

            $items = $items->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->name,
                    'logo' => $item->logo,
                ];
            });

        }else{

            $items = $items->where('type_id',28)->get();


            $items = $items->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->name,
                    'code' => $item->code ?? '',
                    'slug' => $item->slug ?? '',
                    'logo' => $item->logo ?? '',
                ];
            });

        }


       

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items','');
    }




    public function getSetting(Request $request)
    {
        $items = Setting::query()->first();
        
        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items','');
    }




    public function contact(Request $request)
    {

        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = new Contact;
        $ad->user_id = auth()->id();
        $ad->name = $user->name;
        $ad->email = $user->email;
        $ad->mobile = $user->mobile;
      
        $ad->message = $request->message;
        $ad->save();

       
        return mainResponse(true, __('common.create'), $ad , 200, 'items', '');
       
    }



   


    public function report(Request $request)
    {

        // dd($request->all());
        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'user_id_report' => 'required',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = new Contact;
        $ad->user_id = auth()->id();
        $ad->name = $user->name;
        $ad->email = $user->email;
        $ad->mobile = $user->mobile;
      
        $ad->message = $request->message;
        $ad->user_id_report = $request->user_id_report;  // report form user id 
        $ad->save();

       
        return mainResponse(true, __('common.create'), $ad , 200, 'items', '');
       
    }




    public function getBanner(Request $request)
    {
        $items = ItemsCategories::where('status','active')->where('type_id',31)->orderBy('order')->get();


            $items = $items->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->name,
                    'logo' => $item->logo,
                    
                ];
            });

        


        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items','');
    }







    public function getReasons(Request $request)
    {

        $items = ItemsCategories::where('status','active')->where('type_id',32)->get();

            $items = $items->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->name,
                ];
            });

        


       

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items','');
    }




   public function getNotifications(Request $request)
{
    $user = auth()->user(); 
    $now = Carbon::now();

    $items = Notification::with('currentUser')
        ->join('notification_user as nu', 'nu.notification_id', '=', 'notifications.id')
        ->where('nu.user_id', $user->id)
        ->where('notifications.created_at', '>=', $user->created_at) // 🔹 فقط من يوم إنشاء الحساب
        ->select('notifications.*', 'nu.read as read')
        ->orderBy('notifications.created_at', 'desc')
        ->orderBy('notifications.id', 'desc')
        ->get()
        ->unique('id')
        ->values()
        ->map(function($notif) {
            $notif->read = (int) $notif->read;
            return $notif;
        })
        ->groupBy(function ($item) use ($now, $user) {
            $createdAt = Carbon::parse($item->created_at);

            if ($createdAt->greaterThanOrEqualTo($now->copy()->subDay())) {
                return __('common.now');
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subWeek())) {
                return __('common.week');
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subMonth())) {
                return __('common.month');
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subMonths(6))) {
                return __('common.six_months');
            } else {
                return __('common.older');
            }
        });

    $message = __('common.return');
    return mainResponse(true, $message, $items, 200, 'items', '');
}





    public function readNotification(Request $request)
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        DB::table('notification_user')
            ->where('user_id', $user->id)
            ->when($request->filled('notification_id'), fn ($q) => $q->where('notification_id', $request->notification_id))
            ->update(['read' => true, 'updated_at' => now()]);


        $message = __('common.return');
        return mainResponse(true, $message, ['unread_count' => $this->unreadNotificationsCountFor($user->id)], 200, 'items', '');
        
    }

    public function unreadNotificationsCount(Request $request)
    {
        $user = auth('sanctum')->user() ?? auth()->user();

        return mainResponse(true, __('common.return'), [
            'unread_count' => $this->unreadNotificationsCountFor($user->id),
        ], 200, 'items', '');
    }

    protected function unreadNotificationsCountFor(int $userId): int
    {
        return DB::table('notification_user as nu')
            ->join('notifications as n', 'n.id', '=', 'nu.notification_id')
            ->whereNull('n.deleted_at')
            ->where('nu.user_id', $userId)
            ->where('nu.read', 0)
            ->distinct()
            ->count('nu.notification_id');
    }

    /**
     * حذف إشعار/إشعارات للمستخدم الحالي من جدول الربط.
     * - notification_id: حذف إشعار واحد
     * - notification_ids[]: حذف عدة إشعارات
     * - بدون باراميتر: حذف كل إشعارات المستخدم
     */
    public function deleteNotification(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return mainResponse(false, 'Unauthenticated.', null, 401, 'items', '');
        }

        if ($request->filled('notification_id')) {
            $deleted = DB::table('notification_user')
                ->where('user_id', $user->id)
                ->where('notification_id', $request->notification_id)
                ->delete();
        } elseif ($request->filled('notification_ids') && is_array($request->notification_ids)) {
            $deleted = DB::table('notification_user')
                ->where('user_id', $user->id)
                ->whereIn('notification_id', $request->notification_ids)
                ->delete();
        } else {
            $deleted = DB::table('notification_user')
                ->where('user_id', $user->id)
                ->delete();
        }

        $message = app()->getLocale() === 'ar'
            ? 'تم حذف الإشعار بنجاح'
            : 'Notification deleted successfully';

        return mainResponse(true, $message, ['deleted' => (int) $deleted], 200, 'items', '');
    }




    public function create2(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = new Contact;
        $ad->user_id = auth()->id();
        $ad->name = $request->name;
        $ad->email = $request->email;
        $ad->mobile = $request->mobile;
        // $ad->subject = $request->subject;
      
        $ad->message = $request->message;
        $ad->save();

       
        return mainResponse(true, __('common.create'), $ad , 200, 'items', '');
       
    }


  

}




   