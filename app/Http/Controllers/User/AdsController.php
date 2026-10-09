<?php

namespace App\Http\Controllers\User;

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
use App\Models\Image;
use App\Services\AdNotificationService;
use App\Services\FcmNotificationService;
use App\Models\Rating;
use Illuminate\Support\Facades\Cache;
use App\Models\AdView;
use App\Models\Comment;
use App\Models\Language;
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
        $isAr = app()->getLocale() === 'ar';
        if ($isAr) {
            return [
                'required' => 'حقل :attribute مطلوب.',
                'integer' => 'حقل :attribute يجب أن يكون رقماً صحيحاً.',
                'string' => 'حقل :attribute يجب أن يكون نصاً.',
                'max' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',
                'image' => 'حقل :attribute يجب أن يكون صورة (jpg, jpeg, png).',
                'mimes' => 'حقل :attribute يجب أن يكون ملفاً من نوع: :values.',
                'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
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
        $isAr = app()->getLocale() === 'ar';
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

    protected function adCreatedMessage(): string
    {
        return app()->getLocale() === 'ar'
            ? 'تم إنشاء الإعلان بنجاح'
            : 'Ad created successfully';
    }

    protected function adUpdatedMessage(): string
    {
        return app()->getLocale() === 'ar'
            ? 'تم تعديل الإعلان بنجاح'
            : 'Ad updated successfully';
    }

    protected function invalidImageMessage(): string
    {
        return app()->getLocale() === 'ar'
            ? 'الملف المرفوع ليس صورة صالحة'
            : 'The uploaded file is not a valid image';
    }

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
        
        $user = auth()->user();


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
        $ad->user_id = auth()->id();
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
        // dd($request->all());

        $user = auth()->user();

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
            $message = (app()->getLocale() == 'ar') ? 'الإعلان غير موجود.' : 'Ad not found or not authorized.';

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



    public function deleteImg(Request $request)
    {
        $img_id = $request->img_id ;
        // dd($img_id);
        $img = Image::where('id', $img_id)->first();
        if(isset($img) && $img != '' ){
            $img->delete();
        }else{

            return mainResponse(true, __('common.Not_Found'), '', 200, 'items', '');
        }

        return mainResponse(true, __('common.delete'), '', 200, 'items', '');
    }



    public function deleteInvoice(Request $request)
    {
        $ad_id = $request->ad_id ;
        // dd($ad_id);
        $img = Ad::where('id', $ad_id)->first();
        if(isset($img) && $img != '' ){
            $img->invoice = null;
            $img->save();
        }else{

            return mainResponse(true, __('common.Not_Found'), $img, 200, 'items', '');
        }

        return mainResponse(true, __('common.delete'), '', 200, 'items', '');
    }
 

  

    public function getAllItems(Request $request)
    {

        $user = auth('sanctum')->user();
        // dd($user);

        $blockedUserIds = $user instanceof \App\Models\User
            ? $user->blockedUsers()->pluck('users.id')->map(fn ($id) => (int) $id)->values()->toArray()
            : [];

        if ($request->filled('ad_id')) {
            $ad = Ad::with('images', 'user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city')
                    ->where('status','active')
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

            // ✅ أضف my_ad بناءً على المستخدم
            $ad->my_ad = $user && $ad->user_id === $user->id;

            $message = __('common.return');
            return mainResponse(true, $message, $ad->toDetailArray(), 200, 'items', '');
        }

        // استعلام الإعلانات المتعددة
        $items = $this->paginateAdListing($request);

        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items', '');
    }



 
    public function getAllItemsMyAds()
    {
        $user = auth('sanctum')->user();
        $items = Ad::where('user_id',$user->id)->where('status','active')->with('user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city')->orderBy('updated_at','DESC')->orderByDesc('id')->get();
        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }



    public function getAllItemsSellerAds(Request $request)
    {
        $user_id = $request->user_id;
        $items = Ad::where('user_id',$user_id)->where('status','active')->with('user', 'category', 'brand', 'gender', 'subcategory', 'country', 'city')->orderBy('updated_at','DESC')->orderByDesc('id')->get();
        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }


    public function getAllItemsMyAdsWithTrash()
    {
        $user = auth()->user();
        // dd($user);
        $items = Ad::withTrashed()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
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





    public function refresh(Request $request)
    {

        $user = auth()->user();
        $id = $request->id ;
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

        $user = auth()->user();


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



    public function toggleFavorite(Request $request)
    {
        $user = auth()->user();

        $adId = $request->ad_id ;

        $ad = Ad::findOrFail($adId);

        if ($user->favorites()->where('ad_id', $adId)->exists()) {
            $user->favorites()->detach($adId);

            $message = (app()->getLocale() == 'ar') ? 'تم الازالة من المفضلة' : 'Removed from favorites.';

            return mainResponse(true, $message, null, 200, 'items', '');
        } else {
            $user->favorites()->attach($adId);
            $message = (app()->getLocale() == 'ar') ? 'تمت الاضافة للمفضلة' : 'Added to favorites.';
            return mainResponse(true, $message, null, 200, 'items', '');
        }
    }

    /**
     * إبلاغ عن إعلان
     */
    public function reportAd(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $user = auth()->user();

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
        $user = auth()->user();
        $favorites = $user->favorites()->with('category', 'brand', 'gender', 'subcategory', 'country', 'city', 'user')->get();

        return mainResponse(true, $message = __('common.return'), $favorites, 200, 'items', '');
    }



     public function sortAds(Request $request)
    {
        $results = $this->paginateAdListing($request);

        $message = (app()->getLocale() == 'ar') ? 'تم استرجاع البيانات بنجاح' : 'Return Info successfully.';

        return mainResponse(true, $message, $results, 200, 'items', '');
    }


  


    public function blockUser(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'blocked_user_id' => 'required|exists:users,id|different:'.auth()->id(),
        ]);

        $user = auth()->user();

        $blockedUserId = (int) $request->blocked_user_id;

        $alreadyBlocked = $user->blockedUsers()->where('users.id', $blockedUserId)->exists();

        if ($alreadyBlocked) {
            $user->blockedUsers()->detach($blockedUserId);
            $message = (app()->getLocale() == 'ar') ? 'تم الغاء الحظر بنجاح' : 'Unblocked successfully';

        } else {

            $user->blockedUsers()->syncWithoutDetaching([$blockedUserId]);
            $message = (app()->getLocale() == 'ar') ? 'تم الحظر بنجاح' : 'Blocked successfully';

        }

        $items = User::with('following','followers','blockedUsers')->findOrFail($user->id);



        return mainResponse(true, $message, $items, 200, 'items', '');
    }



    public function addComment(Request $request)
    {

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'body' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'ad_id' => $request->ad_id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $ad = Ad::find($request->ad_id);
        $commenter = auth()->user();

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
        $comments = Comment::where('user_id',auth()->id())->where('id', $comment_id)->first();
        if(isset($comments) && $comments != '' ){
            $comments->delete();
        }else{

            return mainResponse(true, __('common.Not_Found'), '', 200, 'items', '');
        }

        return mainResponse(true, __('common.delete'), '', 200, 'items', '');
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
        $user = auth()->user();

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

        $items = Ad::where('user_id', $id)->withTrashed()->orderByDesc('updated_at')->orderByDesc('id')->paginate(10);

        return mainResponse(true, __('common.return'), $items, 200, 'items', '');
    }


    public function toggleFollow(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $authUser = auth()->user();
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


        $items = User::with('following','followers','blockedUsers')->findOrFail($authUser->id);
        $message = (app()->getLocale() == 'ar') ? 'المستخدم {$status} بنجاح' : 'User {$status} successfully.';
        return mainResponse(true, "User {$status} successfully.", $items, 200, 'items', '');
    }



    public function myFollowing()
    {
        $users = auth()->user()->following()->get(); 

        return mainResponse(true, __('common.return'), $users, 200, 'items', '');
    }


    public function myFollowers()
    {
        $users = auth()->user()->followers()->get();
        return mainResponse(true, __('common.return'), $users, 200, 'items', '');
    }




    public function Calculate(Request $request){

        $setting = Setting::query()->first();

        $pay = $request->price * ($setting->vat/100) ;

        return mainResponse(true, __('common.return'), $pay, 200, 'items', '');

    }


    public function DataSerach(Request $request)
    {
        $query = $this->paginateAdListing($request);

        $message = (app()->getLocale() == 'ar') ? 'تم استرجاع البيانات بنجاح' : 'Return Info successfully.';
        return mainResponse(true, $message, $query, 200, 'items', '');
    }



    public function trust(Request $request)
    {

         
        $user = auth()->user();


        $validator = Validator::make($request->all(), [
            'id_no' => 'required',
            'birth_date' => 'required|integer',
            'mobile_trust' => 'required|string|max:255',
        ]);

       
        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }


        //here you are payment method 
        // after success go to 3alam api and go save data based in return response  

        $user->id_no = $request->id_no;
        $user->mobile_trust = $request->mobile_trust;
        $user->birth_date = $request->birth_date;
        $user->save();
 
        return mainResponse(true, __('common.create'), $ad , 200, 'items', '');
       
    }








    












  

}




   