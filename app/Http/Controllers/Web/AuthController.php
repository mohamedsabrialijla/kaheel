<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use App\Mail\WelcomeUserMail;
use App\Mail\SendUserPasswordMail;

use Illuminate\Support\Facades\Mail;
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

        $validator = Validator::make($request->all(), [
            'flag'     => 'required|in:email,mobile',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $flag = $request->flag;
        $password = $request->password;

        if ($flag === 'email') {
            $validator = Validator::make($request->all(), [
                'email_or_username' => 'required|string',
            ]);

            if ($validator->fails()) {
                return mainResponse(false, '', null, 422, 'items', $validator);
            }

            $user = User::where('email', $request->email_or_username)->first();

        } elseif ($flag === 'mobile') {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|string',
            ]);

            if ($validator->fails()) {
                return mainResponse(false, '', null, 422, 'items', $validator);
            }

            $user = User::where('mobile', $request->mobile)->first();
        }

        if (!$user || crypt($password, $user->password) !== $user->password) {
            $message = (app()->getLocale() == 'ar') ? 'بيانات الدخول غير صحيحة' : 'Invalid login credentials';

            return mainResponse(false, $message, null, 400, 'items', '');
        }


        $user->token = $user->createToken('auth_token')->plainTextToken;

        $message = __('auth.login_success'); 

        return mainResponse(true, $message, $user, 200, 'items', '');
    }



   public function register(Request $request)
    {

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
                        $message = 'م إنشاء الحسا بنجاح! يرجى تفعي حسابك من خلال السالة النصية المرلة إلى هاتفك.';
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
            $message = "Brandy acivation code  :" . $item->code ;
        }else{
            $message = "كود التحقق من برادي هو  :" . $item->code;
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
        dd($request->all());
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
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'       => 'sometimes|required|string|min:3|max:50',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'mobile'     => 'sometimes|required|string|min:8|max:15|unique:users,mobile,' . $user->id,
            'gender'     => 'sometimes|required|in:male,female',
            'country_id' => 'sometimes|required|exists:items_categories,id',
            'city_id'    => 'sometimes|required|exists:items_categories,id',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        try {
            $user->name       = $request->has('name') ? $request->name : $user->name;
            $user->email      = $request->has('email') ? $request->email : $user->email;
            $user->mobile     = $request->has('mobile') ? $request->mobile : $user->mobile;
            $user->gender     = $request->has('gender') ? $request->gender : $user->gender;
            $user->country_id = $request->has('country_id') ? $request->country_id : $user->country_id;
            $user->city_id    = $request->has('city_id') ? $request->city_id : $user->city_id;

            $user->save();

            if (app()->getLocale() == 'en') {
                $message = 'Profile updated successfully.';
            } else {
                $message = 'تم تحيث الملف الشخصي بنجاح.';
            }

            return mainResponse(true, $message, $user, 200, 'items', '');

        } catch (\Exception $e) {
            dd($e);
            return mainResponse(false, 'An error occurred while updating your info. Please try again later.', null, 500, 'items', '');
        }
    }





    public function user(Request $request)
    {
        $message = __('common.return');
        return mainResponse(true, $message , auth()->user(), 200 , 'items','');
    }

   
    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user->tokens()->delete()) {
        $message = 'logged out successfully';
            return mainResponse(true, $message , null, 200 , 'items','');
        } else {
        $message = 'logged out successfully';
            return mainResponse(true, $message , null, 200 , 'items','');
        }
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
        $user = User::where('id',$request->user_id)->first();
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





}




   