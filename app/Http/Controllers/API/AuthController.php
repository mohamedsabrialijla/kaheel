<?php

namespace App\Http\Controllers\API;

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
        $lang = app()->getLocale();
        $isAr = $lang === 'ar';

        $messages = [
            'ar' => [
                'flag.in' => 'قيمة نوع تسجيل الدخول يجب أن تكون email أو mobile.',
                'password.required' => 'حقل كلمة المرور مطلوب.',
            ],
            'en' => [
                'flag.in' => 'The login type must be either email or mobile.',
                'password.required' => 'The password field is required.',
            ],
        ];
        $msg = $messages[$lang] ?? $messages['en'];

        $validator = Validator::make($request->all(), [
            'flag' => 'nullable|in:email,mobile',
            'password' => 'required|string',
        ], $msg);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        [$flag, $email, $mobile] = $this->resolveLoginIdentity($request);

        if ($flag === 'mobile') {
            if ($mobile === '') {
                $message = $isAr ? 'رقم الجوال مطلوب.' : 'The mobile number is required.';
                return mainResponse(false, $message, null, 422, 'items', '');
            }

            $user = User::where('mobile', $mobile)->first();
            if (!$user) {
                $message = $isAr ? 'رقم الجوال غير صحيح.' : 'The mobile number is incorrect.';
                return mainResponse(false, $message, null, 404, 'items', '');
            }
        } else {
            if ($email === '') {
                $message = $isAr ? 'البريد الإلكتروني مطلوب.' : 'The email address is required.';
                return mainResponse(false, $message, null, 422, 'items', '');
            }

            $user = User::where('email', $email)->first();
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
     * يحدد نوع الدخول والحقول حتى لو التطبيق أرسل flag/الحقول بشكل غير متسق.
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

        // رقم جوال مرسل داخل حقل الإيميل
        if ($mobile === '' && $email !== '' && $this->looksLikeMobile($email)) {
            $mobile = $email;
            $email = '';
        }

        $flag = strtolower(trim((string) $request->input('flag', '')));
        if (!in_array($flag, ['email', 'mobile'], true)) {
            $flag = $mobile !== '' ? 'mobile' : 'email';
        }

        // flag=email لكن المرسل جوال فقط
        if ($flag === 'email' && $email === '' && $mobile !== '') {
            $flag = 'mobile';
        }

        // flag=mobile لكن الرقم في email_or_username
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
        $lang = app()->getLocale();

        $messages = [
            'en' => [
                'name.required' => 'The name field is required.',
                'name.min' => 'The name must be at least 3 characters.',
                'name.max' => 'The name may not be greater than 50 characters.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been used.',
                'mobile.required' => 'The mobile number is required.',
                'mobile.unique' => 'The mobile number has already been used.',
                'mobile.min' => 'The mobile number must be at least 8 digits.',
                'mobile.max' => 'The mobile number may not exceed 15 digits.',
                'gender.required' => 'Gender is required.',
                'gender.in' => 'Gender must be male or female.',
                'country_id.required' => 'Country is required.',
                'country_id.exists' => 'The selected country is invalid.',
                'city_id.required' => 'City is required.',
                'city_id.exists' => 'The selected city is invalid.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 6 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
                'agree.accepted' => 'You must agree to the terms and conditions.',
            ],
            'ar' => [
                'name.required' => 'الاسم مطلوب.',
                'name.min' => 'الاسم يجب أن يحتوي على 3 أحرف على الأقل.',
                'name.max' => 'الاسم لا يجب أن يتجاوز 50 حرفاً.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.email' => 'يجب إدخال بريد إلكتروني صالح.',
                'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً.',
                'mobile.required' => 'رقم الجوال مطلوب.',
                'mobile.unique' => 'رقم الجوال مستخدم مسبقاً.',
                'mobile.min' => 'رقم الجوال يجب أن لا يقل عن 8 أرقام.',
                'mobile.max' => 'رقم الجوال يجب أن لا يزيد عن 15 رقم.',
                'gender.required' => 'الجنس مطلوب.',
                'gender.in' => 'يجب أن يكون الجنس ذكر أو أنثى.',
                'country_id.required' => 'الدولة مطلوبة.',
                'country_id.exists' => 'الدولة المحددة غير صحيحة.',
                'city_id.required' => 'المدينة مطلوبة.',
                'city_id.exists' => 'المدينة المحددة غير صحيحة.',
                'password.required' => 'كلمة المرور مطلوبة.',
                'password.min' => 'كلمة المرور يجب أن لا تقل عن 6 أحرف.',
                'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
                'agree.accepted' => 'يجب الموافقة على الشروط والأحكام.',
            ],
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|min:8|max:15|unique:users,mobile',
            'gender' => 'required|in:male,female',
            'country_id' => 'required|exists:items_categories,id',
            'city_id' => 'required|exists:items_categories,id',
            'password' => 'required|string|min:6|confirmed',
            'agree' => 'accepted',
        ], $messages[$lang]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

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
            $newUser->status = 'active';

            if ($newUser->save()) {
                $message = $lang == 'en'
                    ? 'Account created successfully! Please verify your account through the SMS sent to your phone.'
                    : 'تم إنشاء الحساب بنجاح! يرجى تفعيل حسابك من خلال الرسالة النصية المرسلة إلى هاتفك.';
            } else {
                $message = $lang == 'en'
                    ? 'Account not created. Please try again.'
                    : 'حدث خطأ، يمكنك المحاولة مرة أخرى.';
            }

            return mainResponse(true, $message, auth('sanctum')->user(), 200, 'items', '');

        } catch (\Exception $e) {
            return mainResponse(false, 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.', null, 500, 'items', '');
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
            'mobile' => 'required|string',
            'code'   => 'required|string',
        ]);

        try {
            $user = User::where('mobile', $request->mobile)
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
            'mobile' => 'required|string',
        ]);

        try {
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user) {
                return mainResponse(false, __('common.user_not_found'), null, 404, 'items', '');
            }

            $code = rand(1000, 9999);
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

            $code = rand(1000, 9999);
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


        
        $user = auth('sanctum')->user();
        $isAr = app()->getLocale() === 'ar';

        $messages = [
            'old_password.required' => __('validation.old_password.required'),
            'old_password.current_password' => __('validation.old_password.current_password'),
            'password.min' => __('validation.password.min'),
            'confirmPassword.same' => __('validation.confirmPassword.same'),
            'logo.image' => $isAr ? 'حقل الشعار يجب أن يكون صورة.' : 'The logo must be an image.',
            'logo.mimes' => $isAr ? 'صيغة الشعار يجب أن تكون jpeg أو jpg أو png.' : 'Logo must be jpeg, jpg, or png.',
            'logo.max' => $isAr ? 'حجم الشعار يجب ألا يتجاوز 2 ميجابايت.' : 'Logo may not be greater than 2MB.',
        ];

        $rules = [
            'name'       => 'sometimes|required|string|min:3|max:50',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'mobile'     => 'sometimes|required|string|min:8|max:15|unique:users,mobile,' . $user->id,
            'gender'     => 'sometimes|required|in:male,female',
            'country_id' => 'sometimes|required|exists:items_categories,id',
            'city_id'    => 'sometimes|required|exists:items_categories,id',
            'password'   => 'nullable|min:6',
            // 'confirmPassword' => 'same:password',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // إذا المستخدم حاب يغير الباسوورد، نخليه required يرسل old_password
        if ($request->filled('password')) {
            $rules['old_password'] = 'required';
        }


        if ($request->filled('password') && $request->password != $request->old_password) {
            
            $message = app()->getLocale() == 'en'
                ? 'password and confirm password not match.'
                : 'الباسورد وتأكيد الباسوورد غير متطابق .';

            return mainResponse(false, $message, null, 203, 'items', '');

        }



        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        // تحقق من الباسوورد القديم إذا كان فيه طلب تغيير باسوورد
        // if ($request->filled('password')) {
        //     if (!Hash::check($request->old_password, $user->password)) {
        //         $msg = app()->getLocale() == 'en'
        //             ? 'The old password is incorrect.'
        //             : 'كلمة المرور القديمة غير صحيحة.';
        //         return mainResponse(false, $msg, null, 203, 'items', '');
        //     }
        //     // إذا صح، نغيّر الباسوورد
        //     $user->password = bcrypt($request->password);
        // } 

        try {
            $user->name       = $request->has('name') ? $request->name : $user->name;
            $user->email      = $request->has('email') ? $request->email : $user->email;
            $user->mobile     = $request->has('mobile') ? $request->mobile : $user->mobile;
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

        $user = auth('sanctum')->user();

        if(!$user || $user == null){
            return false;
        }

        $user = User::with('following','followers')->where('id',$user->id)->first();
        $message = __('common.return');
        return mainResponse(true, $message , $user, 200 , 'items','');
    }

   
    public function logout(Request $request)
    {
        $lang = app()->getLocale();

        // بدون auth middleware: نحل التوكن يدوياً حتى لا يعلق التطبيق على 401
        $accessToken = null;
        $bearer = $request->bearerToken();
        if ($bearer) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
        }

        if ($accessToken) {
            $accessToken->delete();
        } else {
            $user = $request->user('sanctum') ?? auth('sanctum')->user();
            if ($user) {
                $current = $user->currentAccessToken();
                if ($current && method_exists($current, 'delete')) {
                    $current->delete();
                }
            }
        }

        $message = $lang === 'ar'
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




    public function login_facebook(Request $request){

          $token = $request->input('access_token');


        $response = Http::withoutVerifying()->get("https://graph.facebook.com/me?fields=id,name,email&access_token={$token}");

            if ($response->failed()) {
                return mainResponse(false, 'Invalid login' , null, 203 , 'items','');
            }

            $fbUser = $response->json();


            $user = User::firstOrCreate(
                ['facebook_id' => $fbUser['id']],
                [
                    'name' => $fbUser['name'],
                    'email' => $fbUser['email'] ?? "{$fbUser['id']}@facebook.com",
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            $user->token = $user->createToken('facebook-login')->plainTextToken;


            $message = __('auth.login_success'); 

        return mainResponse(true, $message, $user, 200, 'items', '');


    }




    public function login_google(Request $request){


         $idToken = $request->input('id_token');

            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

            if ($response->failed()) {
                return mainResponse(false, 'Invalid login' , null, 203 , 'items','');
            }

            $googleUser = $response->json();

            $user = \App\Models\User::firstOrCreate(
                ['google_id' => $googleUser['sub']],
                [
                    'name' => $googleUser['name'] ?? 'Unknown',
                    'email' => $googleUser['email'],
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            $user->token = $user->createToken('google-login')->plainTextToken;


        $message = __('auth.login_success'); 
        return mainResponse(true, $message, $user, 200, 'items', '');


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



    public function userStatus(Request $request)
    { 
        $user = User::findOrFail($request->id);

        $user->online = !$user->online;
        $user->save();

        $message = __('common.return');

        return mainResponse(true, $message, $user, 200, 'items', '');
    }






}

  
   


    







   
