<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\CategoryGenderFilterTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\ItemsCategories;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Page;
use App\Models\Notification;
use App\Services\FcmNotificationService;



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

        $about = Page::whereTranslationLike('slug', 'about')->first();
        $terms = Page::whereTranslationLike('slug', 'conditions')->first(); 
        $payments = ItemsCategories::where('type_id', '1')->get();
        $items->about = $about;
        $items->terms = $terms;
        $items->payments = $payments;

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items', '');
    }






    public function getReasons(Request $request)
    {

        $items = ItemsCategories::where('status','active');


            $items = $items->where('type_id',32)->get();

            $items = $items->map(function ($item) {
                // جلب الترجمة العربية والإنجليزية
                $name_ar = optional($item->translations->firstWhere('locale', 'ar'))->name;
                $name_en = optional($item->translations->firstWhere('locale', 'en'))->name;

                return [
                    'id'      => $item->id,
                    'name'    => $item->name,    // الاسم الافتراضي
                    'name_ar' => $name_ar ?? $item->name,  // fallback إذا ما فيه ترجمة
                    'name_en' => $name_en ?? $item->name,
                ];
            });



       

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

        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'userid_report' => 'required',
            // 'reasons_ids' => 'required',
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
        $ad->user_id_report = $request->userid_report;  // report form user id 
        $ad->reasons_ids = $request->reasons_ids;  
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




    public function getPages(Request $request)
    {
        $items = Page::query();

        if ($request->slug && $request->slug != '') {
            $items->whereHas('translations', function ($query) use ($request) {
                $query->where('slug', 'like', '%' . $request->slug . '%');
            });
        }

        $items = $items->get();

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items', '');
    }




    public function getNotifications(Request $request)
    {
        $user = auth('sanctum')->user(); 
        $now = Carbon::now();

        // إشعارات المستخدم الحالي فقط (المحذوفة من notification_user لا تظهر)
        $items = Notification::with('currentUser')
        ->join('notification_user as nu', 'nu.notification_id', '=', 'notifications.id')
        ->where('nu.user_id', $user->id)
        ->select('notifications.*', 'nu.read as read')
        ->orderBy('notifications.created_at', 'desc')
        ->orderBy('notifications.id', 'desc')
        ->get()
        ->unique('id')
        ->values()
        ->map(function($notif) use ($user) {
            $notif->read = (int) $notif->read;

            $action = match (true) {
                !empty($notif->ad_id) => FcmNotificationService::actionPayload('ad', $notif->ad_id),
                $notif->type === 'refresh_reminder' => FcmNotificationService::actionPayload('user', $user->id),
                default => ['action' => '', 'action_id' => '', 'action_url' => ''],
            };
            foreach ($action as $key => $value) {
                $notif->setAttribute($key, $value);
            }
            $notif->makeHidden(['type', 'target_type']);

            return $notif;
        })
        ->groupBy(function ($item) use ($now) {
            $createdAt = Carbon::parse($item->created_at);

            if ($createdAt->greaterThanOrEqualTo($now->copy()->subDay())) {
                return 'now';
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subWeek())) {
                return 'one_week';
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subMonth())) {
                return 'one_month';
            } elseif ($createdAt->greaterThanOrEqualTo($now->copy()->subMonths(6))) {
                return 'six_months';
            } else {
                return 'older';
            }
        });


        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items', '');
    }




    public function readNotification(Request $request)
    {
        $user = auth('sanctum')->user();

        // notification_id: قراءة إشعار واحد، بدونه: قراءة الكل
        DB::table('notification_user')
            ->where('user_id', $user->id)
            ->when($request->filled('notification_id'), fn ($q) => $q->where('notification_id', $request->notification_id))
            ->update(['read' => true, 'updated_at' => now()]);


        $message = __('common.return');
        return mainResponse(true, $message, ['unread_count' => $this->unreadNotificationsCountFor($user->id)], 200, 'items', '');
        
    }

    public function unreadNotificationsCount(Request $request)
    {
        $user = auth('sanctum')->user();

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



  

}




   