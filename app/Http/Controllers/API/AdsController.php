<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\CategoryGenderFilterTrait;
use App\Http\Controllers\Concerns\AdListingQueryTrait;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\ItemsCategories;
use App\Models\Setting;



use App\Mail\WelcomeUserMail;
use App\Mail\SendUserPasswordMail;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use App\Models\Ad;
use App\Models\AdReport;
use App\Models\Comment;
use App\Models\Image;
use App\Services\AdNotificationService;
use App\Services\FcmNotificationService;
use App\Models\Rating;
use Illuminate\Support\Facades\Cache;
use App\Models\AdView;
use App\Models\Language;
use App\Models\Payment;
use DB;

use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver; // لو بتستخدم GD





 

class AdsController extends Controller
{
    use CategoryGenderFilterTrait;
    use AdListingQueryTrait;
  
    public function __construct() {
        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    /**
     * رسائل التحقق المخصصة للإعلان (عربي/إنجليزي)
     */
    protected function getAdValidationMessages(): array
    {
        $isAr = $this->isArabicLocale();
        if ($isAr) {
            return [
                'required' => 'حقل :attribute مطلوب.',
                'integer' => 'حقل :attribute يجب أن يكون رقماً صحيحاً.',
                'string' => 'حقل :attribute يجب أن يكون نصاً.',
                'max' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',
                'image' => 'حقل :attribute يجب أن يكون صورة (jpg, jpeg, png).',
                'mimes' => 'حقل :attribute يجب أن يكون ملفاً من نوع: :values.',
                'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
                'in' => 'قيمة :attribute غير صحيحة.',
                'min' => [
                    'array' => 'يجب إرفاق صورة واحدة على الأقل للإعلان.',
                ],
            ];
        }
        return [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute must be an integer.',
            'string' => 'The :attribute must be a string.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'image' => 'The :attribute must be an image (jpg, jpeg, png).',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'array' => 'The :attribute must be an array.',
            'in' => 'The selected :attribute is invalid.',
            'min' => [
                'array' => 'At least one image is required for the ad.',
            ],
        ];
    }

    /**
     * أسماء الحقول للتحقق (عربي/إنجليزي)
     */
    protected function getAdValidationAttributes(): array
    {
        $isAr = $this->isArabicLocale();
        if ($isAr) {
            return [
                'title' => 'العنوان',
                'description' => 'الوصف',
                'category_id' => 'التصنيف',
                'country_id' => 'الدولة',
                'city_id' => 'المدينة',
                'brand_id' => 'العلامة التجارية',
                'product_type' => 'نوع المنتج',
                'mobile' => 'رقم الجوال',
                'price' => 'السعر',
                'images' => 'الصور',
                'images.*' => 'الصور',
                'gender_id' => 'الجنس',
                'subcategory_id' => 'النوع',
                'ad_id' => 'رقم الإعلان',
                'invoice' => 'الفاتورة',
            ];
        }
        return [
            'title' => 'title',
            'description' => 'description',
            'category_id' => 'category',
            'country_id' => 'country',
            'city_id' => 'city',
            'brand_id' => 'brand',
            'product_type' => 'product type',
            'mobile' => 'mobile',
            'price' => 'price',
            'images' => 'images',
            'images.*' => 'images',
            'gender_id' => 'gender',
            'subcategory_id' => 'type',
            'ad_id' => 'ad ID',
            'invoice' => 'invoice',
        ];
    }

    protected function isArabicLocale(): bool
    {
        $locale = request()->header('Accept-Language') ?? request()->input('lang') ?? app()->getLocale();
        return str_starts_with((string) $locale, 'ar');
    }

    protected function adCreatedMessage(): string
    {
        return $this->isArabicLocale()
            ? 'تم إنشاء الإعلان بنجاح'
            : 'Ad created successfully';
    }

    protected function adUpdatedMessage(): string
    {
        return $this->isArabicLocale()
            ? 'تم تعديل الإعلان بنجاح'
            : 'Ad updated successfully';
    }

    protected function invalidImageMessage(): string
    {
        return $this->isArabicLocale()
            ? 'الملف المرفوع ليس صورة صالحة'
            : 'The uploaded file is not a valid image';
    }

    /** الكل إجباري ما عدا الفاتورة والسعر والجوال */
    protected function adFormRules(bool $isUpdate = false): array
    {
        $rules = [
            'title' => 'required|string|max:60',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'product_type' => 'required|in:new,used',
            'mobile' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'invoice' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ];

        if ($isUpdate) {
            $rules['ad_id'] = 'required';
            $rules['images'] = 'nullable|array|min:1';
            $rules['images.*'] = 'nullable|image|mimes:jpeg,jpg,png|max:2048';
        } else {
            $rules['images'] = 'required|array|min:1';
            $rules['images.*'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
        }

        return $rules;
    }


    
    public function create(Request $request)
    {

        // dd($request->all());
        
        $user = auth('sanctum')->user();


        $rules = $this->adFormRules(false);
        $this->applyGenderBrandRules($request, $rules);
        $messages = $this->getAdValidationMessages();
        $attributes = $this->getAdValidationAttributes();
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = new Ad($request->except(['images', 'invoice', 'piece_id', 'metal_id', 'caliber_id', 'weight']));
         foreach ($this->locales as $locale) {
            $ad->translateOrNew($locale->lang)->title = $request->get('title');
            $ad->translateOrNew($locale->lang)->description = $request->get('description');
        }
        $ad->user_id = auth('sanctum')->id();
        $ad->status = 'active';
        $ad->save();

        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('uploads/invoice');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);

            $ad->invoice = 'uploads/invoice/' . $fileName;
            $ad->save();
        }


        if ($request->hasFile('images')) {
            $manager = new ImageManager(Driver::class);

            foreach ($request->file('images') as $imageFile) {
                if (!$imageFile->isValid() || !in_array($imageFile->extension(), ['jpg', 'jpeg', 'png'])) {
                    return mainResponse(false, $this->invalidImageMessage(), null, 422, 'items', '');
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

        AdNotificationService::newAd($ad, $user);

        // يرفع الإعلان لأعلى القائمة فوراً (نفس منطق التحديث / الترتيب بالأحدث)
        $ad->touch();

        $ad->load('images', 'user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city');

        return mainResponse(true, $this->adCreatedMessage(), $ad, 200, 'items', '');
    }


    public function update(Request $request)
    {
        $user = auth('sanctum')->user();

        $rules = $this->adFormRules(true);
        $this->applyGenderBrandRules($request, $rules);
        $messages = $this->getAdValidationMessages();
        $attributes = $this->getAdValidationAttributes();
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = Ad::where('id', $request->ad_id)->where('user_id', $user->id)->first();

        if (!$ad) {
            $message = $this->isArabicLocale() ? 'الإعلان غير موجود.' : 'Ad not found or not authorized.';

            return mainResponse(false, $message, null, 404, 'items', '');
        }

        $ad->fill($request->except(['images', 'invoice', 'piece_id', 'metal_id', 'caliber_id', 'weight']));
        // تأكيد مسح الجنس/النوع عند التعديل إذا أُرسلوا فارغين (تصنيف بدون رجالي/نسائي)
        $ad->gender_id = $request->input('gender_id');
        $ad->subcategory_id = $request->input('subcategory_id');
        if ($request->exists('brand_id')) {
            $ad->brand_id = $request->input('brand_id');
        }
        foreach ($this->locales as $locale) {
            $ad->translateOrNew($locale->lang)->title = $request->get('title');
            $ad->translateOrNew($locale->lang)->description = $request->get('description');
        }
        $ad->save();

        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('uploads/invoice');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $ad->invoice = 'uploads/invoice/' . $fileName;
            $ad->save();
        }


        if ($request->hasFile('images')) {
            $manager = new ImageManager(Driver::class);

            foreach ($request->file('images') as $imageFile) {
                if (!$imageFile->isValid() || !in_array($imageFile->extension(), ['jpg', 'jpeg', 'png'])) {
                    return mainResponse(false, $this->invalidImageMessage(), null, 422, 'items', '');
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


        $ad->load('images', 'user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city');

        return mainResponse(true, $this->adUpdatedMessage(), $ad , 200, 'items', '');
    }




    public function getAllItems(Request $request)
    {
        $user = auth('sanctum')->user();

        // dd($user->id);

        $blockedUserIds = $user
            ? ($user instanceof \App\Models\User ? $user->blockedUsers()->pluck('users.id')->map(fn ($id) => (int) $id)->values()->toArray() : [])
            : [];

        if ($request->filled('ad_id')) {
            $ad = Ad::with('images', 'user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city')->where('status','active')
                    ->whereNotIn('user_id', $blockedUserIds)
                    ->findOrFail($request->ad_id);

            $ip = $request->ip();

            $alreadyViewed = AdView::where('ad_id', $ad->id)
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->when(!$user, fn($q) => $q->where('ip', $ip))
                ->exists();

            if (!$alreadyViewed) {
                AdView::create([
                    'ad_id' => $ad->id,
                    'user_id' => $user?->id,
                    'ip' => $user ? null : $ip,
                ]);

                // Count views without touching updated_at (refresh alone bumps ranking)
                $viewsCount = AdView::where('ad_id', $ad->id)->count();
                $ad->timestamps = false;
                $ad->views = $viewsCount;
                $ad->save();
                $ad->timestamps = true;
            }

            $message = __('common.return');
            return mainResponse(true, $message, $ad->toDetailArray(), 200, 'items', '');
        }

        $items = $this->paginateAdListing($request);

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items', '');
    }





    public function getAllItemsMyAds()
    {
        $user = auth('sanctum')->user();
        $items = Ad::where('user_id',$user->id)->with('category', 'brand', 'gender', 'subcategory', 'country', 'city')->where('status','active')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }




     public function refresh(Request $request)
    {

        $user = auth('sanctum')->user();
        $id = $request->ad_id ;
        $isAr = app()->getLocale() === 'ar';

        $ad = Ad::where('id', $id)->where('user_id', $user->id)->first();

        if (!$ad) {
            $message = $isAr ? 'الإعلان غير موجود.' : 'Ad not found or not authorized.';
            return mainResponse(false, $message, null, 404, 'items', '');
        }

        if ($ad->updated_at && $ad->updated_at->diffInHours(now()) < 24) {
            $message = $isAr
                ? 'يمكنك تحديث هذا الإعلان بعد مرور 24 ساعة منذ التحديث الأخير'
                : 'You can refresh this ad after 24 hours since the last update';
            return mainResponse(false, $message, null, 403, 'items', '');
        }

        $ad->touch();

        $message = $isAr
            ? 'تم تحديث الإعلان بنجاح'
            : 'Ad refreshed successfully';

        return mainResponse(true, $message, $ad, 200, 'items', '');
    } 

    public function filter(Request $request)
    {
        $query = $this->paginateAdListing($request);

        $message = (app()->getLocale() == 'ar') ? 'تم استرجاع البيانات بنجاح' : 'Return Info successfully.';

        return mainResponse(true, $message, $query, 200, 'items', '');
    }

 

    public function rateSeller(Request $request)
    {
        // dd($request->all());

        $user = auth('sanctum')->user();


        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'seller_id' => 'required',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }


        $rating = Rating::updateOrCreate(
            ['user_id' => $user->id, 'seller_id' => $request->seller_id],
            ['rating' => $request->rating]
        );


        $message = (app()->getLocale() == 'ar') ? 'تم التقييم بنجاح' : 'Rating saved.';

        $seller = User::where('id',$request->seller_id)->first();

        return mainResponse(true, $message, $seller, 200, 'items', '');
    }



    // public function rateAd(Request $request)
    // {
    //     $user = auth('sanctum')->user();


    //     $validator = Validator::make($request->all(), [
    //         'rating' => 'required|integer|min:1|max:5',
    //         'ad_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return mainResponse(false, '', null, 422, 'items', $validator);
    //     }


    //     $ad_id = $request->ad_id ;

    //     $ad = Ad::where('id',$ad_id)->first();

    //     $rating = Rating::updateOrCreate(
    //         ['user_id' => $user->id, 'ad_id' => $ad->id],
    //         ['rating' => $request->rating]
    //     );


    //     $message = (app()->getLocale() == 'ar') ? 'تم التقييم بنجاح' : 'Rating saved.';


    //     return mainResponse(true, $message, $rating, 200, 'items', '');
    // }



   public function toggleFavorite(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return mainResponse(false, 'Unauthenticated.', null, 401, 'items', '');
        }

        $adId = $request->ad_id;
        $ad = Ad::findOrFail($adId);

        $isFav = false;
        $message = '';

        if ($user->favorites()->where('ad_id', $adId)->exists()) {
            $user->favorites()->detach($adId);
            $message = (app()->getLocale() == 'ar') ? 'تم الازالة من المفضلة' : 'Removed from favorites.';
            $isFav = false;
        } else {
            $user->favorites()->attach($adId);
            $message = (app()->getLocale() == 'ar') ? 'تمت الاضافة للمفضلة' : 'Added to favorites.';
            $isFav = true;
        }

        return mainResponse(true, $message, ['is_fav' => $isFav], 200, 'items', '');
    }

    /**
     * إبلاغ عن إعلان
     * POST: ad_id, message
     */
    public function reportAd(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $user = auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|exists:ads,id',
            'message' => 'required|string|min:3|max:2000',
        ], [
            'ad_id.required' => $isAr ? 'رقم الإعلان مطلوب.' : 'Ad ID is required.',
            'ad_id.exists' => $isAr ? 'الإعلان غير موجود.' : 'Ad not found.',
            'message.required' => $isAr ? 'نص البلاغ مطلوب.' : 'Report message is required.',
            'message.min' => $isAr ? 'نص البلاغ قصير جداً.' : 'Report message is too short.',
            'message.max' => $isAr ? 'نص البلاغ طويل جداً.' : 'Report message is too long.',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $ad = Ad::where('id', $request->ad_id)->where('status', 'active')->first();
        if (!$ad) {
            $message = $isAr ? 'الإعلان غير موجود.' : 'Ad not found.';
            return mainResponse(false, $message, null, 404, 'items', '');
        }

        if ((int) $ad->user_id === (int) $user->id) {
            $message = $isAr ? 'لا يمكنك الإبلاغ عن إعلانك.' : 'You cannot report your own ad.';
            return mainResponse(false, $message, null, 422, 'items', '');
        }

        $report = AdReport::updateOrCreate(
            [
                'user_id' => $user->id,
                'ad_id' => $ad->id,
            ],
            [
                'message' => trim((string) $request->message),
            ]
        );

        $message = $isAr ? 'تم إرسال البلاغ بنجاح.' : 'Report submitted successfully.';

        return mainResponse(true, $message, $report, 200, 'items', '');
    }



    public function favorites()
    {
        $user = auth('sanctum')->user();
        $favorites = $user->favorites()->with('category', 'brand', 'gender', 'subcategory', 'city', 'country', 'user')->get();

        return mainResponse(true, $message = __('common.return'), $favorites, 200, 'items', '');
    }



    public function sortAds(Request $request)
    {
        // يقبل نفس باراميترات الفلتر + sort_by حتى ما يلغي الترتيب نتائج الفلتر
        $results = $this->paginateAdListing($request);

        $message = (app()->getLocale() == 'ar') ? 'تم استرجاع البيانات بنجاح' : 'Return Info successfully.';

        return mainResponse(true, $message, $results, 200, 'items', '');
    }

    /**
     * خيارات الترتيب للتطبيق (مثل DataFilter).
     */
    public function DataSortAds(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';

        $data = [
            [
                'value' => 'latest',
                'label' => $isAr ? 'الأحدث' : 'Latest',
            ],
            [
                'value' => 'most_viewed',
                'label' => $isAr ? 'الأكثر مشاهدة' : 'Most Viewed',
            ],
            [
                'value' => 'more_rate',
                'label' => $isAr ? 'الأعلى تقييماً' : 'Highest Rating',
            ],
            [
                'value' => 'highest_price',
                'label' => $isAr ? 'الأعلى قيمة' : 'Highest Value',
            ],
            [
                'value' => 'less_price',
                'label' => $isAr ? 'الأقل قيمة' : 'Lowest Value',
            ],
        ];

        $message = __('common.return');

        return mainResponse(true, $message, $data, 200, 'items', '');
    }


  


    public function blockUser(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';

        $request->validate([
            'blocked_user_id' => 'required|exists:users,id|different:'.auth('sanctum')->id(),
        ], [
            'blocked_user_id.required' => $isAr ? 'معرّف المستخدم مطلوب.' : 'User ID is required.',
            'blocked_user_id.exists' => $isAr ? 'المستخدم غير موجود.' : 'User not found.',
            'blocked_user_id.different' => $isAr ? 'لا يمكنك حظر نفسك.' : 'You cannot block yourself.',
        ]);

        $user = auth('sanctum')->user();
        $blockedUserId = (int) $request->blocked_user_id;

        $alreadyBlocked = $user->blockedUsers()->where('users.id', $blockedUserId)->exists();

        if ($alreadyBlocked) {
            $user->blockedUsers()->detach($blockedUserId);
            $message = $isAr ? 'تم الغاء الحظر بنجاح' : 'Unblocked successfully';
            $blocked = false;
        } else {
            $user->blockedUsers()->syncWithoutDetaching([$blockedUserId]);
            $message = $isAr ? 'تم الحظر بنجاح' : 'Blocked successfully';
            $blocked = true;
        }

        return mainResponse(true, $message, [
            'blocked' => $blocked,
            'blocked_user_id' => $blockedUserId,
        ], 200, 'items', '');
    }

    /**
     * حالة الحظر لمجموعة مستخدمين بالنسبة للمستخدم الحالي + علاقات الحظر بينهم
     * user_ids: [1,2,3] أو "1,2,3"
     */
    public function checkBlocks(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $me = auth('sanctum')->user();

        $rawIds = $request->input('user_ids', []);
        if (is_string($rawIds)) {
            $rawIds = explode(',', $rawIds);
        }

        $userIds = collect((array) $rawIds)
            ->map(fn ($id) => (int) trim((string) $id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($userIds->isEmpty()) {
            $message = $isAr ? 'قائمة المستخدمين مطلوبة.' : 'User IDs are required.';
            return mainResponse(false, $message, null, 422, 'items', '');
        }

        if ($userIds->count() > 200) {
            $message = $isAr ? 'الحد الأقصى 200 مستخدم في الطلب.' : 'Maximum 200 users per request.';
            return mainResponse(false, $message, null, 422, 'items', '');
        }

        $involvedIds = $userIds->concat([$me->id])->unique()->values()->all();

        $blockRows = DB::table('blocks')
            ->whereIn('user_id', $involvedIds)
            ->whereIn('blocked_user_id', $involvedIds)
            ->get(['user_id', 'blocked_user_id']);

        $blockedByMe = $blockRows->where('user_id', $me->id)->pluck('blocked_user_id')->map(fn ($id) => (int) $id)->all();
        $blockedMe = $blockRows->where('blocked_user_id', $me->id)->pluck('user_id')->map(fn ($id) => (int) $id)->all();

        $onlineById = User::whereIn('id', $userIds)->pluck('online', 'id')
            ->mapWithKeys(fn ($online, $id) => [(int) $id => (bool) $online])
            ->all();

        $users = $userIds
            ->reject(fn ($id) => $id === (int) $me->id)
            ->map(function ($id) use ($blockedByMe, $blockedMe, $onlineById) {
                $iBlocked = in_array($id, $blockedByMe, true);
                $theyBlocked = in_array($id, $blockedMe, true);

                return [
                    'user_id' => $id,
                    'exists' => array_key_exists($id, $onlineById),
                    // متل الواتس اب: حالة الاتصال لا تظهر إذا في حظر بين الطرفين
                    'online' => !$iBlocked && !$theyBlocked && ($onlineById[$id] ?? false),
                    'is_blocked' => $iBlocked || $theyBlocked,
                    'blocked_by_me' => $iBlocked,
                    'blocked_me' => $theyBlocked,
                    'blocked_by' => $iBlocked && $theyBlocked ? 'both' : ($iBlocked ? 'me' : ($theyBlocked ? 'them' : null)),
                ];
            })
            ->values();

        $relations = $blockRows->map(fn ($row) => [
            'blocker_id' => (int) $row->user_id,
            'blocked_id' => (int) $row->blocked_user_id,
        ])->values();

        return mainResponse(true, __('common.return'), [
            'users' => $users,
            'relations' => $relations,
        ], 200, 'items', '');
    }



    public function addComment(Request $request)
    {
        // dd($request->all());
         
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'ad_id' => $request->ad_id,
            'user_id' => auth('sanctum')->id(),
            'body' => $request->body,
        ]);

        $ad = Ad::find($request->ad_id);
        $commenter = auth('sanctum')->user();

        if ($ad && $commenter) {
            AdNotificationService::newComment($ad, $commenter);
        }

        return mainResponse(true, __('common.create'), $comment->load('user'), 200, 'items', '');
    }



    public function getComments(Request $request)
    {
        $ad_id = $request->ad_id ;
        $comments = Comment::with('user')->where('ad_id', $ad_id)->get();

        return mainResponse(true, __('common.return'), $comments, 200, 'items', '');
    }



    public function deleteComment(Request $request)
    {
        $comment_id = $request->comment_id ;
        $comments = Comment::where('user_id',auth('sanctum')->id())->where('id', $comment_id)->first();
        if(isset($comments) && $comments != '' ){
            $comments->delete();
        }else{

            return mainResponse(true, __('common.Not_Found'), null, 200, 'items', '');
        }

        return mainResponse(true, __('common.delete'), null, 200, 'items', '');
    }



    public function addReportComment(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'report' => 'required|in:not serious about buying,inappropriate response,undervaluation of the commodity',
        ]);

        $comment = Comment::findOrFail($request->comment_id);
        $comment->report = $request->report;
        $comment->save();


        return mainResponse(true, __('common.create'), $comment->load('user'), 200, 'items', '');
    }



    public function deleteAd(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $user = auth('sanctum')->user();

        $adId = $request->input('ad_id') ?? $request->input('id');
        if (!$adId) {
            $message = $isAr ? 'رقم الإعلان مطلوب.' : 'Ad ID is required.';
            return mainResponse(false, $message, null, 422, 'items', '');
        }

        $ad = Ad::where('user_id', $user->id)->where('id', $adId)->first();
        if (!$ad) {
            $message = $isAr ? 'الإعلان غير موجود.' : 'Ad not found or not authorized.';
            return mainResponse(false, $message, null, 404, 'items', '');
        }

        $ad->delete();

        $message = $isAr ? 'تم حذف الإعلان بنجاح.' : 'Ad deleted successfully.';
        return mainResponse(true, $message, null, 200, 'items', '');
    }



    public function getAlldeletedAd(Request $request)
    {
        $id = auth::id();

        $items = Ad::withTrashed()
            ->where('user_id', $id)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }


    public function toggleFollow(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $authUser = auth('sanctum')->user();
        $targetUserId = $request->user_id;

        if ($authUser->id == $targetUserId) {
            return mainResponse(false, 'You cannot follow yourself.', null, 422, 'items', '');
        }

        $isFollowing = $authUser->following()->where('followed_id', $targetUserId)->exists();

        if ($isFollowing) {
            $authUser->following()->detach($targetUserId);
            $status = 'unfollowed';
        } else {
            $authUser->following()->attach($targetUserId);
            $status = 'followed';
        }

        $message = (app()->getLocale() == 'ar') ? 'المستخدم {$status} بنجاح' : 'User {$status} successfully.';


        return mainResponse(true, "User {$status} successfully.", null, 200, 'items', '');
    }



    public function myFollowing()
    {
        $users = auth('sanctum')->user()->following()->get(); 

        return mainResponse(true, __('common.return'), $users, 200, 'items', '');
    }


    public function myFollowers()
    {
        $users = auth('sanctum')->user()->followers()->get();
        return mainResponse(true, __('common.return'), $users, 200, 'items', '');
    }





    public function DataFilter(Request $request)
{
    $categories = ItemsCategories::with('brands')  
    ->where('status', 'active')
    ->where('type_id', 2)
    ->get()
    ->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'brands' => $item->brands, // إذا بدك ترجع البراند مع كل عنصر
        ];
    });



    $countries = ItemsCategories::with('cities')  
    ->where('status', 'active')
    ->where('type_id', 28)
    ->get()
    ->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'cities' => $item->cities, // إذا بدك ترجع البراند مع كل عنصر
        ];
    });


    
    $data = [
        'categories' => $categories,
        'countries' => $countries,
    ];

    $message = __('common.return');
    return mainResponse(true, $message, $data, 200, 'data', '');
}




 
    


    public function DataSerach(Request $request)
    {
        $query = $this->paginateAdListing($request);

        $message = (app()->getLocale() == 'ar') ? 'تم استرجاع البيانات بنجاح' : 'Return Info successfully.';
        return mainResponse(true, $message, $query, 200, 'items', '');
    }



     


    public function getAllItemsMyInvoice(Request $request)
    {
        $id = auth('sanctum')->id();

        $items = Payment::with('ad','user')->where('user_id',$id);


        if(isset($request->track_id) && $request->track_id) {
            $items = $items->where('track_id',$request->track_id )->first();

        }else{

            $items = $items->orderByDesc('id')->get();

        }




        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }




    



    public function CalculateComissionPayment(Request $request)
    {
        $price = $request->price;
        $setting = Setting::query()->first();
        $vat = $setting->vat;
        
        $value = $price * $vat / 100 ;

        return response()->json([
            'status'=> true,
            'message'=> __('common.return'),
            'items'=> $value,
            'code'=> 200,
        ]);

        // return mainResponse(true, __('common.return'), [$value], 200, 'items', '');
    }




    public function getAdSimilar(Request $request)
    {
        $category_id = Ad::where('id',$request->ad_id)->pluck('category_id')->first();
        $items = Ad::where('category_id', $category_id)->where('id', '!=', $request->ad_id)
            ->where('status', 'active')
            ->with(['user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city'])
            ->latest() 
            ->take(4)   
            ->get();
        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }






 

    public function FollowUp(Request $request)
    {
        $user = auth('sanctum')->user();

        $ad = Ad::where('id', $request->ad_id)->first();
        if (!$ad) {
            return mainResponse(false, __('common.not_found'), null, 404, 'items', '');
        }
        $newUserId = $user->id;

        $followUpRaw = $ad->getRawOriginal('follow_up') ?? $ad->attributes['follow_up'] ?? '';
        $userIds = $followUpRaw ? explode(',', trim($followUpRaw)) : [];

        if (!in_array((string) $newUserId, array_map('strval', $userIds))) {
            $userIds[] = $newUserId;
        }

        // بدون تحديث updated_at حتى لا يرتفع الإعلان للأعلى كأن صاحبه حدّثه
        $ad->timestamps = false;
        $ad->follow_up = implode(',', $userIds);
        $ad->save();

        $message = __('common.return');
        return mainResponse(true, $message, '', 200, 'items', '');
    }




  

}




   