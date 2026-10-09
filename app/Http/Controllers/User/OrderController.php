<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Language;
use App\Models\Ad;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Mpdf\Mpdf;


 

class OrderController extends Controller
{
  
     public function __construct() {
        // $this->middleware('auth', ['except' => ['login', 'register']]);

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    


    public function show($id)
    {

        $invoice = Payment::with('user', 'ad', 'payment_type')->findOrFail($id);

        $sellerName = $invoice->user->name ?? 'اسم البائع';
        $vatNumber = $invoice->user->vat_number ?? '123456789000003'; // ضع رقم الضريبة الصحيح من بياناتك
        $invoiceDate = Carbon::parse($invoice->created_at)->toIso8601String();
        $totalAmount = $invoice->amount ?? '0.00'; // مجموع الفاتورة مع الضريبة
        $vatAmount = number_format($invoice->amount * 0.15, 2, '.', '');

        $tlvs = [
            [1, $sellerName],
            [2, $vatNumber],
            [3, $invoiceDate],
            [4, $totalAmount],
            [5, $vatAmount],
        ];

        $qrString = '';
        foreach ($tlvs as $tlv) {
            $qrString .= chr($tlv[0]) . chr(strlen($tlv[1])) . $tlv[1];
        }

        $qrBase64 = base64_encode($qrString);
        $qrSvg = QrCode::format('svg')->size(200)->generate($qrBase64);
        // return $qrSvg;

        // $qrImage = base64_encode(QrCode::format('png')->size(150)->generate($qrBase64));
        return view('pdf.invoice', compact('invoice','qrSvg'));
    }



    public function invoices(Request $request)
    {
        // $user = auth('sanctum')->user();

        $items = Payment::with('ad','user');


        if(isset($request->track_id) && $request->track_id) {
            $items = $items->where('track_id',$request->track_id )->first();

        }else{

            $items = $items->orderByDesc('id')->get();

        }

        
        $message = __('common.return');
        return mainResponse(true, $message, $items, 200, 'items','');
    }



    

}