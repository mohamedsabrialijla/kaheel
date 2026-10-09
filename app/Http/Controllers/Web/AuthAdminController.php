<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Admin;

 

class AuthAdminController extends Controller
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
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }

        $email = $request->email;
        $credentials = request(['email', 'password']);

        if($token = auth('admin')->attempt($credentials))
        {
            $user = auth('admin')->user();
            $user['token'] = $user->createToken('auth_token')->plainTextToken;
            $message = 'Success Login';
            return mainResponse(true, $message , $user , 200 , 'items','');

            
        } else {
            $EmailData = User::query()->where(['email' => $email])->first();
            if ($EmailData != '') {
                $message = (app()->getLocale() == 'ar') ? 'يرجي التاكد من كلمة المرور المدخل' : 'The Password Wrong';
                return mainResponse(false, $message, null, 203, 'items','');
            } else {
                $message =  (app()->getLocale() == 'ar') ? 'يرجي التاكد من   البريد الالكتروني  المدخل' : 'The Email Not  Found';

                return mainResponse(false, $message, null, 203, 'items','');
            }
        }
    }


    public function register(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name_ar'=>'required|string|max:255',
            'name_en'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'mobile' => 'required|min:6|max:10|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
            'commercial_registration_number' => 'required|integer',
            'tax_number' => 'required|numeric|digits:15',
            'commercial_file' => 'required',
            'national_file' => 'required',
            'tax_file' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{
                $confirmation_code = 1111;

                $newUser = new User();
                $newUser->name = $request->first_name .' '.$request->last_name;
                $newUser->email = $request->email;
                $newUser->name_en = $request->name_en;
                $newUser->name_ar = $request->name_ar;
                $newUser->first_name = $request->first_name;
                $newUser->last_name = $request->last_name;
                $newUser->password = bcrypt($request->password);
                $newUser->pwd = $request->password;
                $newUser->mobile = $request->mobile;
                $newUser->verification = 0 ;
                $newUser->code = $confirmation_code;
                $newUser->commercial_registration_number = $request->commercial_registration_number;
                $newUser->tax_number = $request->tax_number;
                $newUser->address = $request->address;
                $newUser->status = 'active';
                $newUser->cafe = 1;


                if($request->file("commercial_file") && $request->file("commercial_file")!=NULL)
                {
                    if ($request->file("commercial_file")->isValid())
                    {
                        $destinationPath=public_path('uploads/users/commercial_file/');

                        $extension=strtolower($request->file("commercial_file")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("commercial_file")->move($destinationPath, $fileName);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                

                if(isset($fileName)){$newUser->commercial_file='uploads/users/commercial_file/'.$fileName;}


                 if($request->file("national_file") && $request->file("national_file")!=NULL)
                {
                    if ($request->file("national_file")->isValid())
                    {
                        $destinationPath=public_path('uploads/users/national_file/');

                        $extension=strtolower($request->file("national_file")->getClientOriginalExtension());
                        //dd($extension);
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName2=uniqid().'.'.$extension;
                            $request->file("national_file")->move($destinationPath, $fileName2);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName2)){ 
                    
                    $newUser->national_file='uploads/users/national_file/'.$fileName2;
                    $newUser->logo_file='uploads/users/national_file/'.$fileName2;
                    
                    
                }


                if($request->tax_file && $request->tax_file !=NULL)
                {
                    if ($request->file("tax_file")->isValid())
                    {
                        $destinationPath=public_path('uploads/users/tax_file/');

                        $extension=strtolower($request->file("tax_file")->getClientOriginalExtension());
                        //dd($extension);
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName3=uniqid().'.'.$extension;
                            $request->file("tax_file")->move($destinationPath, $fileName3);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName3)){$newUser->tax_file='uploads/users/tax_file/'.$fileName3;}


                $newUser->save();

                $email = $request->email;
                $credentials = request(['email', 'password']);
                return $this->login($request);
         }
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $message = 'return data success';
        return mainResponse(true, $message , auth('api')->user(), 200 , 'items','');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user->tokens()->delete()) {
                     Auth::guard('admin')->logout();

        $message = 'logged out successfully';
            return mainResponse(true, $message , null, 200 , 'items','');
        } else {
        $message = 'logged out successfully';
            return mainResponse(true, $message , null, 200 , 'items','');
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

   
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }



    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user()
        ]);
    }



}




   