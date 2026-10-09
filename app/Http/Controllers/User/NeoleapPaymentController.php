<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NeoleapPaymentController extends Controller
{
    private const AES_IV = 'PGKEYENCDECIVSPC';

    /** @var object */
    private $config_values;

    public function __construct()
    {
        $mode = config('neoleap.mode', 'live');
        $values = config('neoleap.' . $mode, config('neoleap.live'));
        $this->config_values = (object) [
            'tranportal_Id'       => $values['tranportal_id'] ?? '',
            'tranportal_password' => $values['tranportal_password'] ?? '',
            'resource_Key'        => $values['resource_key'] ?? '',
        ];
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), ['payment_id' => 'required|string']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $payment = Payment::where('track_id', $request->payment_id)->first();
        if (!$payment) {
            \Log::warning('Neoleap pay – payment not found', [
                'payment_id' => $request->payment_id,
                'host'       => $request->getHost(),
            ]);
            return response()->json(['message' => 'Payment not found or already paid'], 404);
        }

        if ($payment->status !== 'pending') {
            \Log::info('Neoleap pay – payment already processed', [
                'payment_id' => $request->payment_id,
                'status'     => $payment->status,
            ]);

            $paidStatuses = ['paid', 'successful', 'success'];
            $isPaid = in_array($payment->status, $paidStatuses, true);

            if (($payment->payment_from ?? '') === 'app') {
                return response()->json([
                    'status'  => $isPaid,
                    'message' => $isPaid ? 'Payment already completed' : 'Payment already processed',
                    'payment' => $payment,
                ], $isPaid ? 200 : 400);
            }

            $locale = app()->getLocale() ?: 'ar';
            if ($isPaid) {
                return redirect()->to($this->paymentResultUrl($locale, 'success', $payment->track_id));
            }
            return redirect()->to($this->paymentResultUrl($locale, 'failed', $payment->track_id));
        }

        $config = $this->config_values;
        if (!$config || empty($config->tranportal_Id)) {
            return response()->json(['error' => 'Neoleap payment configuration not found'], 500);
        }

        $endpoint = 'https://digitalpayments.neoleap.com.sa/pg/payment/hosted.htm';
        $amount = number_format((float) $payment->amount, 2, '.', '');

        $trackId = 'neo-' . $payment->track_id . '-' . time() . '-' . rand(1000, 9999);

        $fullResponseUrl = $this->buildNeoleapCallbackUrl($request, $payment->track_id);
        $fullErrorUrl = $fullResponseUrl;
    
    // بناء callback URL للـ trandata
    // حسب المثال الناجح: "www.failure.com" و "www.success.com" (host فقط)
    // Neoleap يحتاج إلى host فقط في trandata (كما في المثال الناجح تماماً)
    // payment_id سيتم استخراجه من trackId في callback
    // 
    // ⚠️ مهم: إذا لم يصل callback، تحقق من:
    // 1. Domain Whitelist: إضافة domain (kudhary.com) في إعادات Neoleap
    // 2. IP Whitelist: إضافة IP السرفر في إعدادات Neoleap
    // 3. Callback Route: لتأكد من أن route يعمل على /payment/neoleap/callback
    $host = $request->getHost();
    
    // استخدام host فقط في trandata (كما في المثال النجح تماماً)
    $callbackUrlForTrandata = $host;
    
    \Log::info('Neoleap Callback URLs', [
        'fullResponseUrl' => $fullResponseUrl,
        'fullErrorUrl' => $fullErrorUrl,
        'callbackUrlForTrandata' => $callbackUrlForTrandata,
        'host' => $host,
        'note' => 'Using host only in trandata (as in successful example)'
    ]);

    // بناء نص trandata الخام (دويًا لتجنب ترمي الرموز الخاصة)
       // ...
    $trandataArray = [
        "id" => $config->tranportal_Id,
        "password" => $config->tranportal_password,
        "action" => "1",
        "currencyCode" => "682",
        "errorURL" => $fullResponseUrl,
        "responseURL" => $fullResponseUrl,
        "trackId" => $trackId,
        "amt" => $amount
    ];

    // =================================================================
    // ✅ بناء trandata ـ JSON array string كما ف الصورة
    // =================================================================
    // البيانات يجب أن تكون JSON array: [{"id":"...","password":"...",...}]
    $plainTrandata = json_encode([$trandataArray], JSON_UNESCAPED_SLASHES);
    // =================================================================

     // ✅ اتدعاء دالة التشفر الصحيحة حسب الدوكيمنت
     $encryptedTrandata = $this->encryptAES($plainTrandata, $config->resource_Key);
     
     // للاختبار فقط في وضع التطوير: التحقق من التشفير
     if (config('app.debug')) {
         $testPlain = '[{"id":"F9y478D7cdaZQZv","password":"!P8@6Ke0m!0idPV","action":"1","currencyCode":"682","errorURL":"www.failure.com","responseURL":"www.success.com","trackId":"ssssjhvsg4d7777","amt":"1000.00"}]';
         $testEncrypted = $this->encryptAES($testPlain, $config->resource_Key);
         $expectedEncrypted = "43B2A8B461C9185B4E76A8A1D58651FCCA3B7B5E12B8ED17CE0E16C0FB83AA1E6E2C86E103C25EF4B102E4E04A8FC66172D7D41E1EF2A8EEA6FBC5A981C66994DF0A3234FF7D2778FFAF40FEA113C5642B634D808ED6787EFF6BB333086CD9F46E74AB874019C269626F145AB9806B7113471E2601F3F6E7CA60FC326241B5DD617886F55AD77B30B644E0DF1881B0B3B50B50FBB94BF3189BD50E0E1F6AED7771ECB71511CB36DC915EFF8DF3493CAF29330A574B5949E02279E6EBBB696E4C0FE4056D65F24849C58E2979472C0F6A";
         
         \Log::info('Encryption Test (Debug Mode Only)', [
             'test_encrypted' => $testEncrypted,
             'expected_encrypted' => $expectedEncrypted,
             'match' => $testEncrypted === $expectedEncrypted,
             'test_length' => strlen($testEncrypted),
             'expected_length' => strlen($expectedEncrypted)
         ]);
         
         if ($testEncrypted !== $expectedEncrypted) {
             \Log::warning('ENCRYPTION MISMATCH - The resource_Key may be incorrect!', [
                 'current_key' => $config->resource_Key,
                 'key_length' => strlen($config->resource_Key)
             ]);
         }
     }

    // بناء الـ Payload النهائي
    $payload = [[
        "id" => $config->tranportal_Id,
        "trandata" => $encryptedTrandata,
        "responseURL" => $fullResponseUrl,
        "errorURL" => $fullErrorUrl,
    ]];
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);

    \Log::info('Neoleap Final Request (Using New Encryptor)', [
        'plainTrandata' => $plainTrandata,
        'finalPayload' => $payloadJson,
    ]);

    try {
        $guzzleOptions = [
            'verify'          => false,
            'connect_timeout' => (int) config('neoleap.connect_timeout', 15),
            'timeout'         => (int) config('neoleap.timeout', 60),
        ];
        $proxy = strtolower(trim((string) config('neoleap.proxy', 'http://8.213.38.124:3128')));
        $useProxy = $proxy !== '' && ! in_array($proxy, ['none', 'false', '0', 'off'], true);
        if ($useProxy) {
            $guzzleOptions['proxy'] = $proxy;
        }

        \Log::info('Neoleap HTTP client options', [
            'endpoint' => $endpoint,
            'proxy_set' => $useProxy,
            'proxy_host' => $useProxy ? (parse_url($proxy, PHP_URL_HOST) ?: $proxy) : null,
            'connect_timeout' => $guzzleOptions['connect_timeout'],
            'timeout' => $guzzleOptions['timeout'],
            'code_version' => 'neoleap-proxy-v2',
        ]);

        $client = new Client($guzzleOptions);
        $headers = ['Content-Type' => 'application/json'];
        $response = $client->post($endpoint, ['headers' => $headers, 'body' => $payloadJson]);
        $body = (string) $response->getBody();
        \Log::info('Neoleap Response', ['status' => $response->getStatusCode(), 'body' => $body]);

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON from Neoleap. Body: " . $body);
        }

        $first = $data[0] ?? [];
        if (!empty($first['error']) || (isset($first['status']) && (string) $first['status'] === '2')) {
            $errorText = $first['errorText'] ?? $first['error'] ?? 'Neoleap returned an error';
            \Log::warning('Neoleap API Error', ['response' => $first]);
            return response()->json([
                'error'   => 'Payment gateway error',
                'message' => $errorText,
            ], 400);
        }

        if (!isset($first['result'])) {
            throw new Exception("Invalid response: missing 'result' key. Body: " . $body);
        }

        // استخراج الرقم والرابط من result
        // result format: "600202533756433352:https://securepayments.alrajhibank.com.sa/pg/paymentpage.htm"
        $resultParts = explode(":", $first['result'], 2);
        $paymentId = $resultParts[0] ?? null; // الرقم: 600202533756433352
        $paymentPageUrl = $resultParts[1] ?? null; // لرابط: https://securepayments.alrajhibank.com.sa/pg/paymentpage.htm
        
        if (!$paymentPageUrl || !$paymentId) {
            throw new Exception("Payment URL or PaymentID not found in the result.");
        }

        // إضافة PaymentID كـ query parameter
        $separator = (strpos($paymentPageUrl, '?') !== false) ? '&' : '?';
        $finalUrl = $paymentPageUrl . $separator . 'PaymentID=' . $paymentId;

        \Log::info('Neoleap Final Redirect URL', [
            'payment_id' => $paymentId,
            'original_url' => $paymentPageUrl,
            'final_url' => $finalUrl
        ]);

        return redirect()->away($finalUrl);

    } catch (Exception $e) {
        \Log::error('Neoleap Error: ' . $e->getMessage());
        return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
    }
}


    /**
     * دال التشفير الصحيحة حسب دوكيمنت Neoleap
     * 
     * @param string $str النص المراد تشفيره
     * @param string $key المفتاح السري
     * @return string النص المشفر
     */
    private function encryptAES(string $str, string $key): string
    {
        // 1. PKCS5 padding
        $str = $this->pkcs5_pad($str);
        
        // التحق من أن النص بعد PKCS5 padding يكون مضاعف 16
        $strLength = strlen($str);
        if ($strLength % 16 !== 0) {
            // إذ لم يكن مضاعف 16، أضف padding إضافي
            $pad = 16 - ($strLength % 16);
            $str = $str . str_repeat(chr($pad), $pad);
        }
        
        // 2. إعداد المفتاح - AES-256-CBC (32 بايت = 256 bit)
        $originalKey = $key;
        $key = trim($key);
        
        // تجربة: استخدام المفتاح مباشرة بدون أي تعديل
        // المفتاح 53215311988853215311988853215311 (32 حرف نص عادي)
        // استخدمه مباشرة كما هو
        if (strlen($key) < 32) {
            $key = str_pad($key, 32, "\0");
        } elseif (strlen($key) > 32) {
            $key = substr($key, 0, 32);
        }
        
        \Log::info('Key preparation (AES-256-CBC)', [
            'original_key' => $originalKey,
            'original_key_length' => strlen($originalKey),
            'final_key' => $key,
            'final_key_length' => strlen($key)
        ]);
        
        // 3. إعدا IV
        $iv = "PGKEYENCDECIVSPC";
        
        // Log للتحقق من البيانات قبل التشفير
        \Log::info('EncryptAES - Before Encryption (AES-256-CBC)', [
            'plain_text' => $str,
            'plain_text_length' => strlen($str),
            'key_length' => strlen($key),
            'iv' => $iv
        ]);
        
        // 4. التشفير - AES-256-CBC كا في الدوكيمنت
        // الطريقة: PKCS5Padding + AES-256-CBC
        // ستخدام OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING لمنع padding التلقائي
        // لأننا أضفنا PKCS5 padding يدوياً
        $encrypted = openssl_encrypt(
            $str, 
            "aes-256-cbc",  // AES-256-CBC
            $key, 
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,  // منع padding التقائي
            $iv
        );
        
        if ($encrypted === false) {
            throw new \RuntimeException("Neoleap Encryption Failed: " . openssl_error_string());
        }
        
        // التقق من أن النتيجة عد التشفير تكون بنفس طول النص المدل
        // إذا كان هناك padding إضافي، أزل آخر 16 بايت
        $expectedLength = strlen($str);
        if (strlen($encrypted) > $expectedLength) {
            \Log::warning('EncryptAES - Removing extra padding', [
                'encrypted_length' => strlen($encrypted),
                'expected_length' => $expectedLength,
                'difference' => strlen($encrypted) - $expectedLength
            ]);
            $encrypted = substr($encrypted, 0, $expectedLength);
        }
        
        \Log::info('EncryptAES - After openssl_encrypt (RAW)', [
            'encrypted_binary_length' => strlen($encrypted),
            'original_text_length' => $expectedLength
        ]);
        
        // 5. تحويل مباشر إلى hex (uppercase)
        $encrypted = strtoupper(bin2hex($encrypted));
        
        \Log::info('EncryptAES - After byteArray2Hex', [
            'hex_length' => strlen($encrypted),
            'hex_preview' => substr($encrypted, 0, 50) . '...'
        ]);
        
        // 8. بدون urlencode - JSON يقوم بترمزه تلقائياً
        // الدوكيمنت يقول urlencode، لكن في JSON قد لا نحتاجه
        // $encrypted = urlencode($encrypted);
        
        // Log لتحقق من النتيجة
        \Log::info('EncryptAES - After Encryption', [
            'encrypted_length' => strlen($encrypted),
            'encrypted_preview' => substr($encrypted, 0, 50) . '...',
            'encrypted_full' => $encrypted
        ]);
        
        return $encrypted;
    }
    
    /**
     * فك تشفير AES-256-CBC
     * 
     * @param string $encryptedHex الن المشفر (hex string)
     * @param string $key المفاح السري
     * @return string النص المفكوك
     */
    private function decryptAES(string $encryptedHex, string $key): string
    {
        // 1. تحويل hex إلى binary
        $encrypted = hex2bin(strtolower($encryptedHex));
        
        if ($encrypted === false) {
            throw new \RuntimeException("Invalid hex string");
        }
        
        // 2. إعداد المفتاح
        $key = trim($key);
        if (strlen($key) < 32) {
            $key = str_pad($key, 32, "\0");
        } elseif (strlen($key) > 32) {
            $key = substr($key, 0, 32);
        }
        
        // 3. إعداد IV
        $iv = "PGKEYENCDECIVSPC";
        
        // 4. فك التشفير
        $decrypted = openssl_decrypt(
            $encrypted,
            "aes-256-cbc",
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        
        if ($decrypted === false) {
            throw new \RuntimeException("Neoleap Decryption Failed: " . openssl_error_string());
        }
        
        // 5. إزالة PKCS5 padding
        $decrypted = $this->pkcs5_unpad($decrypted);
        
        return $decrypted;
    }
    
    /**
     * PKCS5 Padding
     */
    private function pkcs5_pad(string $text): string
    {
        $block_size = 16;
        $pad = $block_size - (strlen($text) % $block_size);
        return $text . str_repeat(chr($pad), $pad);
    }
    
    /**
     * إزالة PKCS5 Padding
     */
    private function pkcs5_unpad(string $text): string
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > 16 || $pad < 1) {
            return $text; // لا يوجد padding صحيح
        }
        return substr($text, 0, -$pad);
    }
    
    /**
     * تحيل byte array إلى hex string
     */
    private function byteArray2Hex(array $byteArray): string
    {
        $hex = '';
        foreach ($byteArray as $byte) {
            $hex .= sprintf('%02X', $byte);
        }
        return $hex;
    }



 

    public function callback(Request $request)
    {
        $response = $request->all();
        
        // Log للتحقق من البينات المستقبلة
        \Log::info('Neoleap Callback - Received Data', [
            'all_data' => $response,
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);
        
        $payment_id = $request->get('payment_id');
        if (!$payment_id) {
            $trackIdRaw = $request->get('trackId') ?? $request->input('trackId');
            if ($trackIdRaw && strpos($trackIdRaw, 'neo-') === 0) {
                $parts = explode('-', $trackIdRaw);
                $payment_id = $parts[1] ?? null;
            } else {
                $payment_id = $trackIdRaw;
            }
        }

        if (!$payment_id) {
            \Log::error('Neoleap Callback - payment_id not found');
            return $this->payment_response(null, 'fail');
        }

        $payment_data = Payment::where('track_id', $payment_id)->first();

        if (!$payment_data) {
            \Log::error('Neoleap Callback - Payment not found', ['payment_id' => $payment_id]);
            return $this->payment_response(null, 'fail');
        }

        // البياات المستقبلة من Neoleap
        $paymentid = $request->get('paymentid') ?? $request->input('paymentid'); // lowercase
        $trandata = $request->get('trandata') ?? $request->input('trandata'); // مشفر
        $result = $request->get('result') ?? $request->input('result');
        $responseCode = $request->get('responseCode') ?? $request->input('responseCode');
        $trackId = $request->get('trackId') ?? $request->input('trackId');

        // فك تشفير trandata للتحق من البيانات (اخياري)
        $decryptedTrandata = null;
        if ($trandata) {
            try {
                $config = $this->config_values;
                if ($config && $config->resource_Key) {
                    $decryptedTrandata = $this->decryptAES($trandata, $config->resource_Key);
                    \Log::info('Neoleap Callback - Decrypted trandata', [
                        'decrypted' => $decryptedTrandata
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Neoleap Callback - Failed to decrypt trandata', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // التحقق من نجاح الدع
        // حسب البينات المستقبلة، إا كان paymentid موجود و trandata موجود، فالع ناجح
        // يمك أيضاً التحقق من responseCode أو result إذا ان متوفراً
        $isSuccess = false;
        
        if ($paymentid && $trandata) {
            // إذا كا paymentid و trandata موجودين، فالدع ناجح
            $isSuccess = true;
        } elseif ($responseCode == '000' || $result == 'CAPTURED' || $result == 'SUCCESS') {
            $isSuccess = true;
        }

        if ($isSuccess) {
            Payment::where('track_id', $payment_id)->update([
                'status'        => 'paid',
                'payment_id'    => $paymentid ?? $payment_data->payment_id,
                'payment_type'  => 'neoleap',
                'raw_response'   => array_merge($payment_data->raw_response ?? [], $response),
            ]);

            $data = Payment::where('track_id', $payment_id)->first();

            if ($data && $data->type === 'trust') {
                $user = \App\Models\User::find($data->user_id);
                if ($user) {
                    $user->trusted = 1;
                    $user->save();
                }
            }

            \Log::info('Neoleap Callback - Payment Success', [
                'payment_id' => $payment_id,
                'paymentid' => $paymentid
            ]);

            return $this->payment_response($data, 'success');
        }

        \Log::warning('Neoleap Callback - Payment Failed', [
            'payment_id' => $payment_id,
            'paymentid' => $paymentid,
            'result' => $result,
            'responseCode' => $responseCode
        ]);

        return $this->payment_response($payment_data, 'fail');
    }

    /**
     * بناء رابط callback من نفس host الطلب (مثل إنشاء رابط الدفع).
     */
    private function buildNeoleapCallbackUrl(Request $request, string $trackId): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/')
            . '/neoleap/callback?payment_id=' . urlencode($trackId);
    }

    /**
     * إرجاع الاستجابة حسب مصدر الدفع (تطبيق = JSON، ويب = redirect).
     */
    private function payment_response($payment, string $status): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $isApp = $payment && ($payment->payment_from ?? '') === 'app';
        $locale = app()->getLocale() ?: (string) config('app.locale', 'ar');

        if ($status === 'success') {
            if ($isApp) {
                return response()->json([
                    'status' => true,
                    'message' => 'Payment successful',
                    'payment' => $payment,
                ]);
            }
            return redirect()->to($this->paymentResultUrl($locale, 'success', $payment->track_id ?? ''));
        }

        if ($isApp) {
            return response()->json([
                'status' => false,
                'message' => 'Payment failed',
                'payment' => $payment,
            ]);
        }
        $track_id = $payment ? $payment->track_id : '';
        return redirect()->to($this->paymentResultUrl($locale, 'failed', $track_id));
    }

    private function paymentResultUrl(string $locale, string $page, string $trackId): string
    {
        $loc = $locale !== '' ? $locale : (string) config('app.locale', 'ar');

        return '/' . $loc . '/payment/' . $page . '?track_id=' . urlencode($trackId);
    }

    /**
     * شفير trandata => hex (uppercase) ثم urlencode
     * سخدم AES-256-CBC مع PKCS#5 padding و IV ثابت
     *
     * @param string $plain
     * @param string $key  (resource key)
     * @return string
     */
   
private function encryptArb(string $plain, string $resourceKey): string
{
    // 1) استخدم أول 16 بايت من resourceKey (AES-128-CBC)
    $key = substr(trim($resourceKey), 0, 16);
    $iv = "PGKEYENCDECIVSPC";
    
    // Log للتحقق من المفتاح المستخدم
    \Log::info('Encryption Details', [
        'resourceKey_length' => strlen($resourceKey),
        'resourceKey_preview' => substr($resourceKey, 0, 20) . '...',
        'key_used_length' => strlen($key),
        'key_used_preview' => substr($key, 0, 16),
        'plain_length' => strlen($plain),
        'iv' => $iv
    ]);

    // 2) PKCS#5 padding (block size 16)
    $block = 16;
    $pad = $block - (strlen($plain) % $block);
    $plainPadded = $plain . str_repeat(chr($pad), $pad);

    // 3) تشفير AES-128-CBC (raw binary)
    $cipherRaw = openssl_encrypt(
        $plainPadded,
        "AES-128-CBC",
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($cipherRaw === false) {
        throw new \RuntimeException("OpenSSL encryption failed: " . openssl_error_string());
    }

    // 4) HEX uppercase (بدون urlencode - حسب المثال الناجح)
    $hex = strtoupper(bin2hex($cipherRaw));
    
    \Log::info('Encryption Result', [
        'encrypted_length' => strlen($hex),
        'encrypted_preview' => substr($hex, 0, 50) . '...'
    ]);
    
    return $hex;
}

private function encryptArb256(string $plain, string $resourceKey): string
{
    // 1) استخدم أول 32 بايت من resourceKey (AES-256-CBC)
    $key = substr(trim($resourceKey), 0, 32);
    // ذا كان المفتاح أقل من 32 حرف، أضف padding
    if (strlen($key) < 32) {
        $key = str_pad($key, 32, "\0");
    }
    $iv = "PGKEYENCDECIVSPC";

    // 2) PKCS#5 padding (block size 16)
    $block = 16;
    $pad = $block - (strlen($plain) % $block);
    $plainPadded = $plain . str_repeat(chr($pad), $pad);

    // 3) تشفير AES-256-CBC (raw binary)
    $cipherRaw = openssl_encrypt(
        $plainPadded,
        "AES-256-CBC",
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($cipherRaw === false) {
        throw new \RuntimeException("OpenSSL encryption failed: " . openssl_error_string());
    }

    // 4) HEX uppercase (بدون urlencode - حسب المثال الناجح)
    $hex = strtoupper(bin2hex($cipherRaw));
    return $hex;
}




}
