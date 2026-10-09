<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use App\Mail\WelcomeUserMail;
use App\Mail\SendUserPasswordMail;

use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;


 

class AuthController extends Controller
{
  

    public function image_extensions(){
        return array('jpg','png','jpeg','gif','bmp','pdf');
    }

    public function pdf_extensions(){
        return array('pdf');
    }

    public function login(Request $request)
    {
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';

        $messages = [
            'flag.in' => $isAr ? 'طريقة الدخول غير صالحة' : 'Invalid login method',
            'password.required' => $isAr ? 'كلمة المرور مطلوبة' : 'Password is required',
        ];

        $validator = Validator::make($request->all(), [
            'flag' => 'nullable|in:email,mobile',
            'password' => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        [$flag, $email, $mobile] = $this->resolveLoginIdentity($request);

        if ($flag === 'mobile') {
            if ($mobile === '') {
                $message = $isAr ? 'رقم الجوال مطلوب.' : 'The mobile number is required.';
                return mainResponse(false, $message, null, 422, 'items', '');
            }

            $user = User::with('following', 'followers')->where('mobile', $mobile)->first();
            if (!$user) {
                $message = $isAr ? 'رقم الجوال غير صحيح.' : 'The mobile number is incorrect.';
                return mainResponse(false, $message, null, 404, 'items', '');
            }
        } else {
            if ($email === '') {
                $message = $isAr ? 'البريد الإلكتروني مطلوب.' : 'The email address is required.';
                return mainResponse(false, $message, null, 422, 'items', '');
            }

            $user = User::with('following', 'followers')->where('email', $email)->first();
            if (!$user) {
                $message = $isAr ? 'البريد الإلكتروني غير صحيح.' : 'The email address is incorrect.';
                return mainResponse(false, $message, null, 404, 'items', '');
            }
        }

        if (!Hash::check($request->password, $user->password)) {
            $message = $isAr ? 'كلمة المرور غير صحيحة.' : 'The password is incorrect.';
            return mainResponse(false, $message, null, 400, 'items', '');
        }

        $user->token = $user->createToken('auth_token')->plainTextToken;

        $message = $isAr ? 'تم تسجيل الدخول بنجاح.' : 'Login successful.';
        return mainResponse(true, $message, $user, 200, 'items', '');
    }

    /**
     * @return array{0:string,1:string,2:string} [flag, email, mobile]
     */
    protected function resolveLoginIdentity(Request $request): array
    {
        $email = trim((string) ($request->input('email_or_username')
            ?? $request->input('email')
            ?? ''));
        $mobile = trim((string) ($request->input('mobile')
            ?? $request->input('phone')
            ?? ''));

        if ($mobile === '' && $email !== '' && $this->looksLikeMobile($email)) {
            $mobile = $email;
            $email = '';
        }

        $flag = strtolower(trim((string) $request->input('flag', '')));
        if (!in_array($flag, ['email', 'mobile'], true)) {
            $flag = $mobile !== '' ? 'mobile' : 'email';
        }

        if ($flag === 'email' && $email === '' && $mobile !== '') {
            $flag = 'mobile';
        }

        if ($flag === 'mobile' && $mobile === '' && $email !== '') {
            $mobile = $email;
            $email = '';
        }

        return [$flag, $email, $mobile];
    }

    protected function looksLikeMobile(string $value): bool
    {
        if (str_contains($value, '@')) {
            return false;
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';
        return strlen($digits) >= 8 && strlen($digits) <= 15;
    }




   public function register(Request $request)
    {
        // dd($request->all());

        $messages_ar = [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نصًا.',
            'name.min' => 'الاسم يجب أن لا يقل عن :min أحرف.',
            'name.max' => 'الاسم يجب أن لا يزيد عن :max أحرف.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',

            'mobile.required' => 'رقم الجوال مطلوب.',
            'mobile.string' => 'رقم الجوال يجب أن يكون نصًا.',
            'mobile.min' => 'رقم الجوال يجب أن لا يقل عن :min أرقام.',
            'mobile.max' => 'رقم الجوال يجب أن لا يزيد عن :max أرقام.',
            'mobile.unique' => 'رقم الجوال مستخدم بالفعل.',

            'gender.required' => 'النوع مطلوب.',
            'gender.in' => 'النوع يجب أن يكون ذكر أو أنثى.',

            'country_id.required' => 'الدولة مطلوبة.',
            'country_id.exists' => 'الدولة المختارة غير صحيحة.',

            'city_id.required' => 'المدينة مطلوبة.',
            'city_id.exists' => 'المدينة المختارة غير صحيحة.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن :min أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',

            'terms.accepted' => 'يجب الموافقة على الشروط والأحكام.',
        ];

        $messages_en = [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.min' => 'Name must be at least :min characters.',
            'name.max' => 'Name may not be greater than :max characters.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already in use.',

            'mobile.required' => 'Mobile number is required.',
            'mobile.string' => 'Mobile must be a valid string.',
            'mobile.min' => 'Mobile must be at least :min digits.',
            'mobile.max' => 'Mobile must not exceed :max digits.',
            'mobile.unique' => 'This mobile number is already in use.',

            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be either male or female.',

            'country_id.required' => 'Country is required.',
            'country_id.exists' => 'Selected country is invalid.',

            'city_id.required' => 'City is required.',
            'city_id.exists' => 'Selected city is invalid.',

            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least :min characters.',
            'password.confirmed' => 'Password confirmation does not match.',

            'terms.accepted' => 'You must accept the terms and conditions.',
        ];

        // اختيار اللغة المناسبة
        $messages = app()->getLocale() === 'ar' ? $messages_ar : $messages_en;

        // التحقق
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|min:8|max:15|unique:users,mobile',
            'gender' => 'required|in:male,female',
            'country_id' => 'required|exists:items_categories,id',
            'city_id' => 'required|exists:items_categories,id',
            'password' => ['required', 'string', 'min:8'],
            'confirmPassword' => ['required', 'same:password'],
            'terms' => 'accepted'
        ], $messages);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        } else {
            try {
                $confirmation_code = 1111;

                $newUser = new User();
                $newUser->name = $request->name;
                $newUser->email = $request->email;
                $newUser->password = bcrypt($request->password);
                $newUser->pwd = $request->password;
                $newUser->mobile = $request->mobile;
                $newUser->country_id = $request->country_id;
                $newUser->city_id = $request->city_id;
                $newUser->verification = 0;
                $newUser->code = $confirmation_code;
                $newUser->gender = $request->gender;
                $newUser->status = 'not-active';

                

               
                if($newUser->save()){
                     if(app()->getLocale() == 'en'){
                        $message = 'Account created successfully! Please verify your account through the SMS sent to your phone.';
                    } else {
                        $message = 'تم إنشاء الحساب بنجاح! يرجى تفعيل حسابك من خلال الرسالة النصية المرسلة إلى هاتفك.';
                    }
                    // $this->sendSms($newUser);
                }else{

                     if(app()->getLocale() == 'en'){
                        $message = 'Account Not created Please try again.';
                    } else {
                        $message = 'حدث خطأ يمكنك اادة المحاولة مرة أخرى';
                    }

                }
                

               return mainResponse(true, $message, auth('api')->user(), 200, 'items', '');

                
                
            } catch (\Exception $e) {
                dd($e);
                return mainResponse(false, 'An error occurred while creating your account. Please try again later.', null, 500, 'items', '');
            }
        }
    }



    public function sendSms($item)
    {
        if(app()->getLocale()=='en'){
            $message = "Brandy acivation code  " . $item->code ;
        }else{
            $message = "كود التحقق من برادي هو " . $item->code;
        }

        try {
            $client = new \GuzzleHttp\Client();

            $response = $client->post('https://your-sms-provider.com/api/send', [
                'form_params' => [
                    'to' => $request->mobile,
                    'message' => $message,
                    'sender' => 'YourAppName',
                    'api_key' => 'YOUR_API_KEY',
                ]
            ]);

            $body = json_decode((string) $response->getBody(), true);

        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
        }
    }



    public function verify(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'code'   => 'required',
        ]);


        $validator = Validator::make($request->all(), [
             'mobile' => 'required',
            'code'   => 'required',
        ]);


        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        try {
            $user = User::with('following','followers')->where('mobile', $request->mobile)
                        ->where('code', $request->code)
                        ->first();


            $m1 = __('common.unvalid_verfied');
            $m2 = __('common.verfied');
            $m3 = __('common.unverfied');

            if (!$user) {
                return mainResponse(false, $m1, null,422 , 'items', '');
            }

            $user->verification = 1; 
            $user->code = null; 
            $user->save();

            $user->token = $user->createToken('auth_token')->plainTextToken;

            return mainResponse(true, $m2, $user, 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, $m3, null, 500, 'items', '');
        }
    }




    public function resendCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
        ]);

        try {
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user) {
                return mainResponse(false, __('common.user_not_found'), null, 404, 'items', '');
            }

            // $code = rand(1000, 9999);
            $code = 1111;
            $user->code = $code;
            $user->save();

            // $this->sendSMS($user);

            return mainResponse(true, __('common.code_resent_successfully'), null, 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, __('common.something_wrong'), null, 500, 'items', '');
        }
    }


    

    public function forget(Request $request)
    {
        

        $request->validate([
            'mobile' => 'required|string',
        ]);

        try {
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user) {
                return mainResponse(false, __('common.user_not_found'), null, 404, 'items', '');
            }

            // $code = rand(1000, 9999);
            $code = 1111;
            $user->code = $code;
            $user->save();

            // $this->sendSMS($user);

            return mainResponse(true, __('common.code_resent_successfully'), null, 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, __('common.something_wrong'), null, 500, 'items', '');
        }
    }





   public function setupNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'   => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);


         if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }else{




        try {
            $user = User::where('mobile', $request->mobile)
                        ->where('verification', 1) // التأكد أنه تحقق مسبقا
                        ->first();

            if (!$user) {
                return mainResponse(false, __('common.unverfied'), null, 404, 'items', '');
            }

            $user->password = bcrypt($request->password);
            $user->pwd = $request->password;
            $user->save();

            $user->token = $user->createToken('auth_token')->plainTextToken;

            return mainResponse(true, __('common.update'), $user , 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, __('common.something_wrong'), null, 500, 'items', '');
        }


        }
    }
 

 

    public function editInfo(Request $request)
    {
        // dd($request->all());
        
        $user = auth()->user();

        $messages = [
            'old_password.required' => __('validation.old_password.required'),
            'old_password.current_password' => __('validation.old_password.current_password'),
            'password.min' => __('validation.password.min'),
            'confirmPassword.same' => __('validation.confirmPassword.same'),
            // باقي الرسائل زي ما كانت...
        ];

        $rules = [
            'name'       => 'sometimes|required|string|min:3|max:50',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'mobile2'     => 'sometimes|required|string|min:8|max:15|unique:users,mobile,' . $user->id,
            'gender'     => 'sometimes|required|in:male,female',
            'country_id' => 'sometimes|required|exists:items_categories,id',
            'city_id'    => 'sometimes|required|exists:items_categories,id',
            'password'   => 'nullable|min:6',
            // 'confirmPassword' => 'same:password',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];

        // إذا المستخدم حاب يغير الباسوورد، نخليه required يرسل old_password
        if ($request->filled('password')) {
            $rules['oldPassword'] = 'required';
        }



        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        // تحقق من الباسوورد القديم إذا كان فيه طلب تغيير باسوورد
        // if ($request->filled('password')) {
        //     if (!Hash::check($request->oldPassword, $user->password)) {
        //         $msg = app()->getLocale() == 'en'
        //             ? 'The old password is incorrect.'
        //             : 'كلمة المرور القديمة غير صحيحة.';
        //         return mainResponse(false, $msg, null, 203, 'items', '');
        //     }
        //     // إذا صح، نغيّر الباسوورد
        //     $user->password = bcrypt($request->password);
        //     $user->pwd = $request->password;
        // }

        try {
            $user->name       = $request->has('name') ? $request->name : $user->name;
            $user->email      = $request->has('email') ? $request->email : $user->email;
            $user->mobile     = $request->has('mobile2') ? $request->mobile2 : $user->mobile;
            $user->gender     = $request->has('gender') ? $request->gender : $user->gender;
            $user->country_id = $request->has('country_id') ? $request->country_id : $user->country_id;
            $user->city_id    = $request->has('city_id') ? $request->city_id : $user->city_id;
            $user->password    = $request->has('password') ? bcrypt($request->password) : $user->password;
            $user->pwd    = $request->has('password') ? $request->password : $user->pwd;

            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                if ($logoFile && $logoFile->isValid()) {
                    $destinationPath = public_path('uploads/user');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }

                    $oldLogo = $user->getRawOriginal('logo');
                    if ($oldLogo) {
                        $oldPath = public_path(ltrim($oldLogo, '/'));
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $extension = strtolower($logoFile->getClientOriginalExtension() ?: 'jpg');
                    $fileName = time() . '_' . uniqid() . '.' . $extension;
                    $logoFile->move($destinationPath, $fileName);
                    $user->logo = 'uploads/user/' . $fileName;
                }
            }

            $user->save();

            $message = app()->getLocale() == 'en'
                ? 'Profile updated successfully.'
                : 'تم تحديث الملف الشخصي بنجاح.';

            $u = User::where('id',$user->id)->first();

            return mainResponse(true, $message, $u, 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, 'An error occurred while updating your info. Please try again later.', null, 500, 'items', '');
        }
    }





    public function user(Request $request)
    {
        $id = auth()->id();
        $user = User::with('following','followers')->where('id',$id)->first();
        $message = __('common.return');
        return mainResponse(true, $message , $items, 200 , 'items','');
    }

   
    public function logout(Request $request)
    {
        $locale = app()->getLocale();
        $user = $request->user() ?? auth('sanctum')->user();

        if ($user) {
            $current = $user->currentAccessToken();
            if ($current) {
                $current->delete();
            } else {
                $user->tokens()->delete();
            }
        }

        $message = $locale === 'ar'
            ? 'تم تسجيل الخروج بنجاح.'
            : 'Logged out successfully.';

        return mainResponse(true, $message, null, 200, 'items', '');
    }




    public function updatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed', // حتاج new_password_confirmation
        ]);


        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        $user = auth()->user();


        $user->password = bcrypt($request->password);
        $user->pwd = $request->new_password;
        $user->save();

            $message = __('common.update');
            return mainResponse(true, $message , null, 200 , 'items','');
    }




   


    public function userInformations(Request $request)
    {
        $user = User::with('following','followers')->where('id',$request->user_id)->first();
        $message = __('common.return');
        return mainResponse(true, $message , $user , 200 , 'items','');
    }




    public function storeDeviceToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:android,ios,web',
            // 'app_version' => 'nullable|string'
        ]);

        $user = auth()->user();

        DeviceToken::updateOrCreate(
            ['device_token' => $request->device_token],
            [
                'user_id' => $user->id ?? null,
                'device_type' => $request->device_type,
                // 'app_version' => $request->app_version,
                'ip' => $request->ip(),
                'is_active' => true
            ]
        );

        return mainResponse(true, __('common.create'), null, 200, 'items', '');
    }


    public function getAuth(){
        $user = auth()->user();
        $items = User::with('following','followers','blockedUsers')->where('id',$user->id)->first();
        $message ="ٌreturn Item successfully";
        return mainResponse(true, $message , $items, 200, 'items','');

    }

    /**
     * طلب تحقق نفاذ عبر Semati API (نفس منطق package saudi-nafath-integration).
     * POST مع id_number → يستدعي semati.sa ويرجع transId و random لعرضه على المستخدم.
     */
    public function nafathRequest(Request $request)
    {
        $maxBirthYear = (int) date('Y') - 10;
        $minDate = now()->subYears(120)->format('Y-m-d');
        $maxDate = now()->subYears(10)->format('Y-m-d');

        $validator = Validator::make($request->all(), [
            'id_number' => 'required|string|min:10|max:20',
            'birth_date' => 'required_without_all:birth_year,birth_month|date|after_or_equal:' . $minDate . '|before_or_equal:' . $maxDate,
            'birth_year' => 'required_without:birth_date|integer|between:1940,' . $maxBirthYear,
            'birth_month' => 'required_without:birth_date|integer|between:1,12',
            'birth_day' => 'nullable|integer|between:1,31',
        ], [
            'id_number.required' => __('رقم الهوية مطلوب'),
            'id_number.min' => __('رقم الهوية 10 أرقام على الأقل'),
            'birth_date.required_without_all' => __('يرجى اختيار تاريخ الميلاد كاملاً'),
            'birth_year.required_without' => __('سنة الميلاد مطلوبة'),
            'birth_month.required_without' => __('شهر الميلاد مطلوب'),
        ]);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), null, 422, 'items', $validator);
        }

        [$birthYear, $birthMonth, $birthDay, $birthDateIso] = $this->resolveBirthPartsFromRequest($request);

        $apiKey = config('services.nafath.api_key');
        $callbackUrl = config('services.nafath.callback_url');
        $authorizeUrl = config('services.nafath.authorize_url');
        if (!empty($authorizeUrl)) {
            $authorizeUrl = rtrim($authorizeUrl, '/') . '/';
        } else {
            $urlBase = preg_replace('#^https?://#i', '', trim(config('services.nafath.url_base', 'test')));
            // إذا كان url_base يحتوي المسار (مثل .../nafath/.../authorize) استخدمه كما هو بدون إلحاق المسار مرة أخرى
            if (str_contains($urlBase, 'nafath') && str_contains($urlBase, 'authorize')) {
                $authorizeUrl = 'https://' . trim($urlBase, '/') . '/';
            } else {
                $authorizeUrl = 'https://' . trim($urlBase, '/') . '/nafath/api/v1/client/authorize/';
            }
        }

        if (empty($apiKey) || empty($callbackUrl)) {
            $msg = 'إعداد نفاذ غير مكتمل. ضع في .env: NAFATH_API_KEY و NAFATH_CALLBACK_URL (مثال: https://www.brandy.sa/nafath/post-callback)';
            return mainResponse(false, $msg, null, 400, 'items', '');
        }

        $appId = config('services.nafath.app_id', '');
        $headers = [
            'Authorization' => 'apikey ' . $apiKey,
            'Content-Type' => 'application/json',
        ];
        if (!empty($appId)) {
            $headers['X-App-Id'] = $appId;
            $headers['App-Id'] = $appId;
        }

        $body = [
            'id' => $request->id_number,
            'action' => 'SpRequest',
            'service' => 'Login',
            'callbackUrl' => $callbackUrl,
        ];
        if (!empty($appId)) {
            $body['appId'] = $appId;
        }

        try {
            $response = Http::withHeaders($headers)->post($authorizeUrl, $body);

            $statusCode = $response->status();
            $body = $response->json();

            if ($statusCode !== 200) {
                Log::error('Nafath API Error', ['response' => $body]);
                return mainResponse(false, $body['error'] ?? 'Nafath request failed', null, $statusCode ?: 500, 'items', '');
            }

            $transId = $body['transId'] ?? null;
            $random = $body['random'] ?? null;
            if (!$transId || $random === null) {
                return mainResponse(false, 'Invalid response from Nafath', null, 500, 'items', '');
            }

            Cache::put('nafath_trans_' . $transId, [
                'user_id' => auth()->id(),
                'id_number' => $request->id_number,
                'birth_year' => $birthYear,
                'birth_month' => $birthMonth,
                'birth_day' => $birthDay,
                'birth_date' => $birthDateIso,
                'random' => $random,
                'status' => 'pending',
            ], 600);

            return mainResponse(true, 'Nafath request sent', [
                'trans_id' => $transId,
                'random' => $random,
            ], 200, 'items', '');
        } catch (\Exception $e) {
            Log::error('Nafath Exception', ['message' => $e->getMessage()]);
            return mainResponse(false, 'Nafath error: ' . $e->getMessage(), null, 500, 'items', '');
        }
    }

    /**
     * فحص حالة طلب نفاذ (لـ polling من الواجهة).
     * GET auth/nafath/check/{transId}
     */
    public function nafathCheck(Request $request, $transId)
    {
        $key = 'nafath_trans_' . $transId;
        $data = Cache::get($key);
        if (!$data) {
            return mainResponse(false, 'Transaction not found or expired', null, 404, 'items', '');
        }
        if ($data['user_id'] != auth()->id()) {
            return mainResponse(false, 'Unauthorized', null, 403, 'items', '');
        }
        return mainResponse(true, 'OK', [
            'status' => $data['status'],
            'random' => $data['random'] ?? null,
        ], 200, 'items', '');
    }

    /**
     * إرجاع حالة توثيق نفاذ للمستخدم الحالي (للاستدعاء من Vue بعد العودة أو عند فتح الصفحة).
     * GET /user/auth/nafath/status
     * Response: { code: 200, status: true, items: { nafath_verified: bool, national_id: string|null } }
     */
    public function nafathStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return mainResponse(false, 'Unauthenticated', null, 401, 'items', '');
        }
        return mainResponse(true, 'OK', [
            'nafath_verified' => (bool) ($user->nafath_verified ?? false),
            'national_id' => $user->national_id ?? null,
            'birth_year' => $user->birth_year ?? null,
            'birth_month' => $user->birth_month ?? null,
            'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
        ], 200, 'items', '');
    }

    /**
     * نفاذ (Semati) يستدعي هذا الرابط بـ POST ويرسل JWT في response.
     * فك JWT ← transId, status ← تحديث المستخدم وحالة الـ cache للـ polling.
     */
    public function nafathPostCallback(Request $request)
    {
        try {
            $jwt = $request->input('response');
            if (!$jwt) {
                Log::warning('Nafath callback: missing response');
                return response('Bad Request', 400);
            }
            $payload = $this->nafathDecodeJwt($jwt);
            if (!$payload) {
                return response('Invalid token', 400);
            }
            $transId = $payload->transId ?? null;
            $status = $payload->status ?? null;
            $nationalId = $payload->nationalId ?? $payload->national_id ?? null;

            if (!$transId) {
                return response('Invalid token', 400);
            }

            $key = 'nafath_trans_' . $transId;
            $data = Cache::get($key);
            if (!$data) {
                Log::warning('Nafath callback: no record for transId', ['transId' => $transId]);
                return response('No login found', 404);
            }

            $newStatus = ($status === 'COMPLETED' || $status === 'completed') ? 'completed' : 'rejected';
            Cache::put($key, array_merge($data, ['status' => $newStatus]), 600);

            $user = User::find($data['user_id']);
            if ($user) {
                $user->nafath_verified = ($newStatus === 'completed');
                if ($nationalId) {
                    $user->national_id = $nationalId;
                } elseif (!empty($data['id_number'])) {
                    $user->national_id = $data['id_number'];
                }
                $birthYear = ! empty($data['birth_year']) ? (int) $data['birth_year'] : null;
                $birthMonth = ! empty($data['birth_month']) ? (int) $data['birth_month'] : null;
                $birthIso = $data['birth_date'] ?? null;
                if (! $birthIso && $birthYear && $birthMonth) {
                    $d = (int) ($data['birth_day'] ?? 1);
                    $birthIso = Carbon::createFromDate(
                        $birthYear,
                        $birthMonth,
                        min($d, Carbon::create($birthYear, $birthMonth)->daysInMonth)
                    )->format('Y-m-d');
                }
                $this->applyBirthPartsToUser($user, [$birthYear, $birthMonth, null, $birthIso]);
                $user->save();
            }

            return response('TRS', 200);
        } catch (\Exception $e) {
            Log::error('Nafath callback exception', ['message' => $e->getMessage()]);
            return response('TRS', 200);
        }
    }

    private function nafathDecodeJwt($token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) < 2) return null;
            $payload = $parts[1];
            $payload = strtr($payload, '-_', '+/');
            return json_decode(base64_decode($payload));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Nafath redirects the user here after authentication (لو استخدموا redirect بدل POST).
     * Return HTML that notifies opener and closes popup.
     */
    public function nafathCallbackPage(Request $request)
    {
        $params = $request->query();
        $locale = app()->getLocale();
        $returnUrl = rtrim(config('app.url'), '/') . '/' . $locale . '/add-ad';
        return response()->view('nafath.callback', [
            'params' => $params,
            'status' => $request->input('status', 'success'),
            'national_id' => $request->input('national_id', $request->input('nationalId', '')),
            'return_url' => $returnUrl,
        ], 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * Save Nafath result for the authenticated user (national_id, verified).
     */
    public function nafathSave(Request $request)
    {
        $maxBirthYear = (int) date('Y');
        $minDate = now()->subYears(120)->format('Y-m-d');
        $maxDate = now()->format('Y-m-d');
        $request->validate([
            'national_id' => 'required|string|max:20',
            'verified' => 'boolean',
            'birth_date' => 'nullable|date|after_or_equal:' . $minDate . '|before_or_equal:' . $maxDate,
            'birth_year' => 'nullable|integer|between:1940,' . $maxBirthYear,
            'birth_month' => 'nullable|integer|between:1,12',
            'birth_day' => 'nullable|integer|between:1,31',
        ]);
        $user = $request->user();
        if (!$user) {
            return mainResponse(false, 'Unauthenticated', null, 401, 'items', '');
        }
        $user->national_id = $request->national_id;
        $user->nafath_verified = (bool) $request->get('verified', true);
        $this->applyBirthPartsToUser($user, $this->resolveBirthPartsFromRequest($request));
        $user->save();
        return mainResponse(true, 'تم حفظ بيانات التحقق', [
            'id' => $user->id,
            'national_id' => $user->national_id,
            'nafath_verified' => $user->nafath_verified,
            'birth_year' => $user->birth_year,
            'birth_month' => $user->birth_month,
            'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
        ], 200, 'items', '');
    }

    /**
     * @return array{0: int|null, 1: int|null, 2: int|null, 3: string|null} [year, month, day, iso date]
     */
    protected function resolveBirthPartsFromRequest(Request $request): array
    {
        if ($request->filled('birth_date')) {
            $birth = Carbon::parse($request->birth_date)->startOfDay();

            return [
                (int) $birth->year,
                (int) $birth->month,
                (int) $birth->day,
                $birth->format('Y-m-d'),
            ];
        }

        $year = $request->filled('birth_year') ? (int) $request->birth_year : null;
        $month = $request->filled('birth_month') ? (int) $request->birth_month : null;
        $day = $request->filled('birth_day') ? (int) $request->birth_day : 1;
        $iso = null;
        if ($year && $month) {
            $dt = Carbon::createFromDate($year, $month, min($day, Carbon::create($year, $month)->daysInMonth));
            $iso = $dt->format('Y-m-d');
        }

        return [$year, $month, $day, $iso];
    }

    /** @param array{0: ?int, 1: ?int, 2: ?int, 3: ?string} $parts */
    protected function applyBirthPartsToUser(User $user, array $parts): void
    {
        [$year, $month, , $iso] = array_pad($parts, 4, null);
        if ($year) {
            $user->birth_year = $year;
        }
        if ($month) {
            $user->birth_month = $month;
        }
        if ($iso) {
            $user->birth_date = $iso;
        }
    }

    /* ──────────────────────────────────────────────────────────────
     *  Yakeen (يقين) — Step 1: consent  /  Step 2: OTP verify
     * ────────────────────────────────────────────────────────────── */

    /**
     * Step 1 — إرسال الهوية + تاريخ الميلاد ليقين → الحصول على transaction-id
     * POST auth/yakeen/consent
     */
    public function yakeenConsent(Request $request)
    {
        $idNumber = (string) $request->input('id_number', '');
        $isSaudi  = str_starts_with($idNumber, '1');
        $birthCalendar = strtolower((string) $request->input(
            'birth_calendar',
            $isSaudi ? 'hijri' : 'gregorian'
        ));
        if (! in_array($birthCalendar, ['hijri', 'gregorian'], true)) {
            $birthCalendar = $isSaudi ? 'hijri' : 'gregorian';
        }

        $maxBirthYear = (int) now()->subYears(10)->format('Y');

        $rules = [
            'id_number' => 'required|string|min:10|max:10',
            'birth_calendar' => 'nullable|in:hijri,gregorian',
        ];
        if ($birthCalendar === 'hijri') {
            $rules['birth_month_h'] = 'required|integer|between:1,12';
            $rules['birth_year_h']  = 'required|integer|between:1350,1450';
            $rules['birth_day_h']   = 'nullable|integer|between:1,31';
        } else {
            $rules['birth_month'] = 'required|integer|between:1,12';
            $rules['birth_year']  = 'required|integer|between:1940,' . $maxBirthYear;
            $rules['birth_day']   = 'nullable|integer|between:1,31';
        }

        $validator = Validator::make($request->all(), $rules, [
            'id_number.required'   => 'رقم الهوية مطلوب',
            'id_number.min'        => 'رقم الهوية يجب أن يكون 10 أرقام',
            'birth_calendar.in'    => 'نوع التاريخ يجب أن يكون هجري أو ميلادي',
            'birth_month.required' => 'شهر الميلاد الميلادي مطلوب',
            'birth_year.required'  => 'سنة الميلاد الميلادي مطلوبة',
            'birth_month_h.required' => 'شهر الميلاد الهجري مطلوب',
            'birth_year_h.required'  => 'سنة الميلاد الهجري مطلوبة',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), null, 422, 'items', $validator);
        }

        $user = $request->user();
        if (!$user) {
            return mainResponse(false, 'Unauthenticated', null, 401, 'items', '');
        }

        $type = $isSaudi ? 'nin' : 'iqama';

        try {
            if ($birthCalendar === 'hijri') {
                $birthMonth = (int) $request->birth_month_h;
                $birthYear  = (int) $request->birth_year_h;
                $birthDay   = (int) ($request->birth_day_h ?: 1);
                $birthIso   = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
                $dateOfBirth = \App\Services\YakeenService::buildConsentDateOfBirth([
                    'type' => $type,
                    'mode' => 'hijri_yyyy_mm',
                    'h_day' => $birthDay,
                    'h_month' => $birthMonth,
                    'h_year' => $birthYear,
                    'g_day' => 1,
                    'g_month' => 1,
                    'g_year' => 1960,
                ]);
            } else {
                $birthMonth = (int) $request->birth_month;
                $birthYear  = (int) $request->birth_year;
                $birthDay   = (int) ($request->birth_day ?: 1);
                $birthIso   = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
                $dateOfBirth = \App\Services\YakeenService::buildConsentDateOfBirth([
                    'type' => $type,
                    'mode' => 'gregorian_yyyy_mm',
                    'h_day' => 1,
                    'h_month' => 1,
                    'h_year' => 1400,
                    'g_day' => $birthDay,
                    'g_month' => $birthMonth,
                    'g_year' => $birthYear,
                ]);
            }

            $yakeen = app(\App\Services\YakeenService::class);
            $result = $yakeen->sendConsentRequest($idNumber, $dateOfBirth, $type);

            if (($result['status'] ?? '') === 'otp_already_sent') {
                $transactionId = trim((string) (
                    $result['transaction_id']
                    ?? $user->yakeen_transaction_id
                    ?? ''
                ));

                if ($transactionId !== '') {
                    $user->yakeen_transaction_id = $transactionId;
                    $user->national_id = $idNumber;
                    $this->applyBirthPartsToUser($user, [
                        $birthYear,
                        $birthMonth,
                        $birthDay,
                        $birthIso,
                    ]);
                    $user->save();

                    return mainResponse(true, 'رمز التحقق مُرسل مسبقاً — أدخل الرمز المرسل إلى جوالك', [
                        'transaction_id' => $transactionId,
                        'otp_already_sent' => true,
                    ], 200, 'items', '');
                }

                return mainResponse(
                    false,
                    'تم طلب رمز التحقق مسبقاً لهذه الهوية. تحقق من رسائل جوالك. إن لم يصلك رمز، انتظر 10 دقائق ثم أعد المحاولة.',
                    ['error_code' => 'CNST-001'],
                    429,
                    'items',
                    ''
                );
            }

            $transactionId = trim((string) ($result['transaction_id'] ?? ''));
            if ($transactionId === '') {
                return mainResponse(false, 'لم يُرجع يقين معرّف المعاملة. أعد المحاولة.', null, 502, 'items', '');
            }

            $user->yakeen_transaction_id = $transactionId;
            $user->national_id          = $idNumber;
            $this->applyBirthPartsToUser($user, [
                $birthYear,
                $birthMonth,
                $birthDay,
                $birthIso,
            ]);

            if (! $user->save()) {
                Log::error('Yakeen consent: failed to save user transaction_id', ['user_id' => $user->id]);
            }

            $user->refresh();
            $persisted = ! empty($user->yakeen_transaction_id);
            if (! $persisted) {
                Log::warning('Yakeen consent: transaction_id not in DB — verify will use request body', [
                    'user_id' => $user->id,
                    'transaction_id' => $transactionId,
                ]);
            }

            return mainResponse(true, 'تم إرسال رمز التحقق إلى جوالك — أدخل OTP', [
                'transaction_id' => $transactionId,
                'transaction_persisted' => $persisted,
            ], 200, 'items', '');
        } catch (\Exception $e) {
            Log::error('Yakeen consent error', [
                'message' => $e->getMessage(),
                'nin_or_iqama' => $idNumber,
                'type' => $type,
                'birth_calendar' => $birthCalendar,
                'dateOfBirth_sent' => isset($dateOfBirth) ? $dateOfBirth : null,
                'input' => $birthCalendar === 'hijri'
                    ? ['birth_month_h' => $request->birth_month_h, 'birth_year_h' => $request->birth_year_h]
                    : ['birth_month' => $request->birth_month, 'birth_year' => $request->birth_year],
            ]);
            $httpCode = str_contains($e->getMessage(), 'مسبقاً') ? 429 : 500;

            return mainResponse(false, $e->getMessage(), null, $httpCode, 'items', '');
        }
    }

    /**
     * Step 2 — إرسال OTP + transaction-id ليقين → استلام بيانات المواطن وتوثيق الحساب
     * POST auth/yakeen/verify
     */
    public function yakeenVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|min:4|max:10',
            'transaction_id' => 'nullable|string|max:191',
        ], [
            'otp_code.required' => 'رمز التحقق مطلوب',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), null, 422, 'items', $validator);
        }

        $user = $request->user();
        if (!$user) {
            return mainResponse(false, 'Unauthenticated', null, 401, 'items', '');
        }

        $transactionId = trim((string) ($request->input('transaction_id') ?: $user->yakeen_transaction_id));
        if ($transactionId === '') {
            return mainResponse(false, 'انتهت صلاحية طلب التحقق. أعد إدخال الهوية واطلب رمزاً جديداً.', null, 400, 'items', '');
        }

        if ($user->yakeen_transaction_id !== $transactionId) {
            $user->yakeen_transaction_id = $transactionId;
            $user->save();
        }

        try {
            $idNumber = $user->national_id ?? '';
            $type = str_starts_with((string) $idNumber, '1') ? 'nin' : 'iqama';

            $yakeen = app(\App\Services\YakeenService::class);
            $result = $yakeen->getDataInquiry($transactionId, $request->otp_code, $type);

            $yakeenData = $result['data'] ?? [];

            // توثيق الحساب
            $user->nafath_verified = true;
            $user->yakeen_transaction_id = null; // استُخدم — مسح
            $user->save();

            return mainResponse(true, 'تم التحقق من الهوية بنجاح', [
                'verified'    => true,
                'yakeen_data' => $yakeenData,
            ], 200, 'items', '');
        } catch (\Exception $e) {
            Log::error('Yakeen verify error', ['message' => $e->getMessage()]);
            $httpCode = str_contains($e->getMessage(), 'مسبقاً') ? 429 : 422;

            return mainResponse(false, $e->getMessage(), null, $httpCode, 'items', '');
        }
    }

    //  public function login_facebook(Request $request){

    //       $token = $request->input('access_token');


    //     $response = Http::withoutVerifying()->get("https://graph.facebook.com/me?fields=id,name,email&access_token={$token}");

    //         if ($response->failed()) {
    //             return mainResponse(false, 'Invalid login' , null, 203 , 'items','');
    //         }

    //         $fbUser = $response->json();


    //         $user = User::firstOrCreate(
    //             ['facebook_id' => $fbUser['id']],
    //             [
    //                 'name' => $fbUser['name'],
    //                 'email' => $fbUser['email'] ?? "{$fbUser['id']}@facebook.com",
    //                 'password' => bcrypt(str()->random(16)),
    //             ]
    //         );

    //         $user->token = $user->createToken('facebook-login')->plainTextToken;


    //         $message = __('auth.login_success'); 

    //     return mainResponse(true, $message, $user, 200, 'items', '');


    // }




    // public function login_google(Request $request){


    //      $idToken = $request->input('id_token');

    //         $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
    //             'id_token' => $idToken,
    //         ]);

    //         if ($response->failed()) {
    //             return mainResponse(false, 'Invalid login' , null, 203 , 'items','');
    //         }

    //         $googleUser = $response->json();

    //         $user = \App\Models\User::firstOrCreate(
    //             ['google_id' => $googleUser['sub']],
    //             [
    //                 'name' => $googleUser['name'] ?? 'Unknown',
    //                 'email' => $googleUser['email'],
    //                 'password' => bcrypt(str()->random(16)),
    //             ]
    //         );

    //         $user->token = $user->createToken('google-login')->plainTextToken;


    //     $message = __('auth.login_success'); 
    //     return mainResponse(true, $message, $user, 200, 'items', '');


    // }






}

  
   


    







   
