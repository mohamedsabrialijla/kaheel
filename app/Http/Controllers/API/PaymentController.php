<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Cache;

use App\Models\Language;
use App\Models\Payment;
use DB;




 

class PaymentController extends Controller
{
  
    public function __construct() {
        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }


     
    public function generateUrwayPaymentLink(Request $request)
    {
        $user = auth('sanctum')->user();
        $trackId  = uniqid();
        $amount   = number_format((float) $request->amount, 2, '.', '');
        $currency = 'SAR';

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $payment = Payment::create([
            'track_id'      => $trackId,
            'ad_id'         => $request->ad_id,
            'amount'        => $amount,
            'currency'      => $currency,
            'status'        => 'pending',
            'payment_from'  => $request->payment_from ?? 'app',
            'payment_type'  => 'commission',
            'user_id'       => $user->id,
        ]);

        $paymentUrl = rtrim($request->getSchemeAndHttpHost(), '/')
            . '/neoleap/pay?payment_id=' . urlencode($payment->track_id);

        return response()->json([
            'status'       => true,
            'payment_url'  => $paymentUrl,
            'payment_id'   => $payment->track_id,
            'message'      => 'success generate',
        ]);
    }




    public function handleCallback(Request $request)
    {
        // dd('sss');
        $paymentResult = $request->all();

        // dd($paymentResult);
        \Log::info('URWAY CALLBACK RECEIVED', $paymentResult);

        $payment = Payment::where('track_id', $paymentResult['TrackId'] ?? null)->first();


        if ($payment) {
            $status = ($paymentResult['Result'] ?? '') === 'Successful' ? 'paid' : 'unpaid';

           

            $payment->update([
                'status'       => $status,
                'amount'       => $paymentResult['amount'] ?? $payment->amount,
                'raw_response' => $paymentResult,
                'payment_id'   => $paymentResult['PaymentId'] ?? $payment->payment_id,
                'payment_type' => $paymentResult['cardBrand'] ?? 'not_found',
            ]);


            if ($payment->type === 'trust' && $status === 'paid') {
                    $user = User::where('id',$payment->user_id)->first();

                    $user->trusted = 1;
                    $user->save();
                }

            // 👇 هنا التفرقة بين الويب والتطبيق
                // هذا استدعاء من تطبيق (API)
                return response()->json([
                    'status' => $status === 'paid',
                    'message' => $status === 'paid' ? 'Payment successful' : 'Payment failed',
                    'payment' => $payment,
                ]);
                     
        }

        // لم يتم العثور على عملية دفع
        return response()->json([
                    'status' =>false,
                    'message' => 'Payment failed',
                    'payment' => $payment,
                ]);
    }

    public function handleError(Request $request)
    {
        return redirect()->to('/payment/failed?track_id=' . $payment->track_id);
    }



    public function getAllItems(Request $request)
    {
        $track_id = $request->trackId;         


        $payment = Payment::with('ad','payment_type')->where('track_id', $track_id)->first();

        return mainResponse(true, __('common.create'), $payment, 200, 'items', '');
    }





    public function paymentStatus(Request $request)
    {
        $trackId = $request->track_id ?? $request->trackId;
        $paymentId = $request->payment_id;

        if ($trackId) {
            $resulte = Payment::where('track_id', $trackId)->first();
        } elseif ($paymentId) {
            $resulte = Payment::where('payment_id', $paymentId)
                ->orWhere('track_id', $paymentId)
                ->first();
        } else {
            return mainResponse(false, 'track_id or payment_id is required', null, 400, 'items', '');
        }

        return mainResponse(true, __('common.create'), $resulte, 200, 'items', '');
    }


    public function generateUrwayPaymentLinkTrust(Request $request)
    {
        $rules = [
            'id_no'        => 'required|string|max:20',
            'birth_date'   => 'required|date',
            'mobile_trust' => 'required|string|max:15',
        ];
        $messages = [
            'ar' => [
                'id_no.required'        => 'الرقم الوطني مطلوب',
                'birth_date.required'   => 'تاريخ الميلاد مطلوب',
                'mobile_trust.required' => 'رقم الجوال مطلوب',
            ],
            'en' => [
                'id_no.required'        => 'ID number is required',
                'birth_date.required'   => 'Birth date is required',
                'mobile_trust.required' => 'Mobile number is required',
            ]
        ];
        $locale = app()->getLocale();
        $validator = Validator::make($request->all(), $rules, $messages[$locale] ?? []);
        if ($validator->fails()) {
            return mainResponse(false, '', null, 203, 'items', $validator);
        }

        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $trackId = uniqid();
        $amount   = number_format((float) $request->amount, 2, '.', '');
        $currency = 'SAR';

        $user->id_no = $request->id_no;
        $user->birth_date = $request->birth_date;
        $user->mobile_trust = $request->mobile_trust;
        $user->save();

        $payment = Payment::create([
            'track_id'      => $trackId,
            'ad_id'         => $request->ad_id,
            'amount'        => $amount,
            'currency'      => $currency,
            'status'        => 'pending',
            'payment_from'  => 'app',
            'payment_type'  => 'Card',
            'type'          => 'trust',
            'user_id'       => $user->id,
        ]);

        $paymentUrl = rtrim($request->getSchemeAndHttpHost(), '/')
            . '/neoleap/pay?payment_id=' . urlencode($payment->track_id);

        return response()->json([
            'status'       => true,
            'payment_url'  => $paymentUrl,
            'payment_id'   => $payment->track_id,
            'message'      => 'success generate',
        ]);
    }





 




  

}




   