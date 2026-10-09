<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class YakeenService
{
    protected string $baseUrl = 'https://yakeencore.api.elm.sa';
    protected string $username;
    protected string $password;
    protected string $appId;
    protected string $appKey;
    protected int $connectTimeoutSeconds;
    protected int $timeoutSeconds;
    protected int $retryTimes;
    protected int $retrySleepMs;
    protected string $operatorId;
    protected string $usageCode;

    public function __construct()
    {
        $this->username = config('services.yakeen.username');
        $this->password = config('services.yakeen.password');
        $this->appId    = config('services.yakeen.app_id');
        $this->appKey   = config('services.yakeen.app_key');
        $this->operatorId = (string) config('services.yakeen.operator_id', '1000011021');
        $this->usageCode  = (string) config('services.yakeen.usage_code', 'USC10004');

        $this->connectTimeoutSeconds = (int) (config('services.yakeen.connect_timeout', 5));
        $this->timeoutSeconds        = (int) (config('services.yakeen.timeout', 30));
        $this->retryTimes            = (int) (config('services.yakeen.retry_times', 2));
        $this->retrySleepMs          = (int) (config('services.yakeen.retry_sleep_ms', 300));
    }

    protected function http()
    {
        return Http::withOptions([
            'proxy' => 'http://8.213.38.124:3128',
        ])
        ->connectTimeout($this->connectTimeoutSeconds)
        ->timeout($this->timeoutSeconds)
        ->retry(
            $this->retryTimes,
            $this->retrySleepMs,
            function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException;
            }
        );
    }

    protected function getJwtToken(): string
    {
        if (Cache::has('yakeen_jwt_token')) {
            return Cache::get('yakeen_jwt_token');
        }

        $response = $this->http()->withHeaders([
            'app-id'  => $this->appId,
            'app-key' => $this->appKey,
        ])->post("{$this->baseUrl}/api/v3/yakeen/login", [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            Log::error('Yakeen login failed', ['body' => $response->body()]);
            throw new \Exception('Failed to get Yakeen JWT token: ' . $response->body());
        }

        $data  = $response->json();
        $token = $data['access_token'] ?? null;

        if (!$token) {
            throw new \Exception('Yakeen login response missing access_token');
        }

        Cache::put('yakeen_jwt_token', $token, now()->addHours(23));
        return $token;
    }

    /**
     * SaudiByNin  → nin
     * NonSaudiByIqama → iqama
     */
    private function serviceIdentifier(string $type): string
    {
        return match ($type) {
            'nin'   => 'e4dbc7f4-b941-46be-8e98-f52ae1d32bce',
            'iqama' => 'e514377a-05b7-4da8-a00b-3d491894934b',
            default => throw new \InvalidArgumentException('Invalid identifier type'),
        };
    }

    private static function formatDatePart(int $year, int $month, int $day, string $partFormat): string
    {
        return match ($partFormat) {
            'mm-yyyy'    => sprintf('%02d-%04d', $month, $year),
            'yyyy-mm'    => sprintf('%04d-%02d', $year, $month),
            'dd-mm-yyyy' => sprintf('%02d-%02d-%04d', $day, $month, $year),
            default      => sprintf('%04d-%02d-%02d', $year, $month, $day),
        };
    }

    /**
     * consent/create — dateOfBirth (cross-check هجري + ميلادي حسب TRIAL/ELM)
     *
     * @param  array{h_day:int,h_month:int,h_year:int,g_day:int,g_month:int,g_year:int,type:string,mode?:string}  $parts
     */
    public static function buildConsentDateOfBirth(array $parts): string
    {
        $type = $parts['type'] ?? 'nin';
        $mode = $parts['mode'] ?? ($type === 'iqama'
            ? (string) config('services.yakeen.crosscheck_mode_iqama', 'gregorian_yyyy_mm_dd')
            : (string) config('services.yakeen.crosscheck_mode_nin', 'dual_yyyy_mm_dd'));

        $h = self::formatDatePart(
            (int) $parts['h_year'],
            (int) $parts['h_month'],
            (int) $parts['h_day'],
            'yyyy-mm-dd'
        );
        $g = self::formatDatePart(
            (int) $parts['g_year'],
            (int) $parts['g_month'],
            (int) $parts['g_day'],
            'yyyy-mm-dd'
        );

        return match ($mode) {
            'dual_yyyy_mm_dd' => $h . ',' . $g,
            'dual_yyyy_mm' => self::formatDatePart((int) $parts['h_year'], (int) $parts['h_month'], 1, 'yyyy-mm')
                . ',' . self::formatDatePart((int) $parts['g_year'], (int) $parts['g_month'], 1, 'yyyy-mm'),
            'hijri_yyyy_mm_dd' => $h,
            'gregorian_yyyy_mm_dd' => $g,
            'hijri_yyyy_mm' => self::formatDatePart((int) $parts['h_year'], (int) $parts['h_month'], 1, 'yyyy-mm'),
            'hijri_mm_yyyy' => self::formatDatePart((int) $parts['h_year'], (int) $parts['h_month'], 1, 'mm-yyyy'),
            'gregorian_yyyy_mm' => self::formatDatePart((int) $parts['g_year'], (int) $parts['g_month'], 1, 'yyyy-mm'),
            'gregorian_mm_yyyy' => self::formatDatePart((int) $parts['g_year'], (int) $parts['g_month'], 1, 'mm-yyyy'),
            default => $h . ',' . $g,
        };
    }

    /**
     * Step 1 — consent: nin/iqama + dateOfBirth
     */
    public function sendConsentRequest(string $identifier, string $dateOfBirth, string $type = 'nin'): array
    {
        $token = $this->getJwtToken();

        $queryKey = $type === 'nin' ? 'nin' : 'iqama';
        $query = [
            $queryKey     => $identifier,
            'dateOfBirth' => $dateOfBirth,
        ];

        Log::info('Yakeen consent create', [
            $queryKey => $identifier,
            'dateOfBirth' => $dateOfBirth,
            'type' => $type,
        ]);

        $response = $this->http()->withHeaders([
            'Authorization'      => 'Bearer ' . $token,
            'service-identifier' => $this->serviceIdentifier($type),
            'usage-code'         => $this->usageCode,
            'operator-id'        => $this->operatorId,
            'app-id'             => $this->appId,
            'app-key'            => $this->appKey,
        ])->get("{$this->baseUrl}/api/v1/yakeen/consent/create", $query);

        if (! $response->successful()) {
            $body = $response->body();
            $errorCode = self::parseApiErrorCode($body);

            Log::error('Yakeen consent failed', [
                'query' => $query,
                'body' => $body,
                'status' => $response->status(),
                'error_code' => $errorCode,
            ]);

            // OTP مُرسل مسبقاً — يقين قد يُرجع transaction-id في الهيدر حتى مع 400
            if ($errorCode === 'CNST-001') {
                return [
                    'status' => 'otp_already_sent',
                    'error_code' => 'CNST-001',
                    'transaction_id' => self::extractTransactionId($response),
                ];
            }

            throw new \Exception(self::userFacingMessage($body));
        }

        $transactionId = self::extractTransactionId($response);
        if (! $transactionId) {
            throw new \Exception('transaction-id not found in response header');
        }

        return [
            'status'         => 'success',
            'transaction_id' => $transactionId,
        ];
    }

    /** @param \Illuminate\Http\Client\Response $response */
    public static function extractTransactionId($response): ?string
    {
        foreach (['transaction-id', 'Transaction-Id', 'transactionId', 'TransactionId'] as $name) {
            $value = $response->header($name);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        $body = $response->json();
        if (is_array($body)) {
            $fromBody = $body['transactionId'] ?? $body['transaction_id'] ?? null;
            if (is_string($fromBody) && trim($fromBody) !== '') {
                return trim($fromBody);
            }
        }

        return null;
    }

    public static function parseApiErrorCode(?string $body): ?string
    {
        if ($body === null || $body === '') {
            return null;
        }

        $json = $body;
        if (! str_starts_with(trim($body), '{')) {
            if (preg_match('/\{.*\}/s', $body, $m)) {
                $json = $m[0];
            }
        }

        $decoded = json_decode($json, true);

        return is_array($decoded)
            ? ($decoded['errorDetail']['errorCode'] ?? null)
            : null;
    }

    public static function userFacingMessage(?string $body, string $context = 'consent'): string
    {
        $code = self::parseApiErrorCode($body);

        if ($code === 'CNST-001') {
            return $context === 'verify'
                ? 'رمز التحقق غير صالح أو انتهت صلاحية الطلب. تأكد من الرمز المرسل لجوالك، أو انتظر 10 دقائق ثم ابدأ من جديد (الخطوة الأولى).'
                : 'تم إرسال رمز التحقق مسبقاً إلى جوالك. انتقل لإدخال الرمز ولا تضغط إرسال مرة أخرى.';
        }

        return match ($code) {
            'RQST-102' => 'صيغة تاريخ الميلاد غير صحيحة. المطلوب: سنة-شهر (yyyy-mm) كما في الهوية.',
            'RQST-015' => 'تاريخ الميلاد لا يطابق السجل. اختر هجري أو ميلادي كما على الهوية/الإقامة وأدخل الشهر والسنة الصحيحين.',
            'RQST-011' => 'إعدادات يقين غير صحيحة (operator-id).',
            default => $context === 'verify'
                ? 'فشل التحقق من الرمز. تأكد من OTP الصحيح أو اطلب رمزاً جديداً من الخطوة الأولى.'
                : 'فشل الطلب لدى يقين. تأكد من البيانات وحاول مرة أخرى.',
        };
    }

    /**
     * Step 2 — data inquiry: ترسل OTP + transaction-id → ترجع بيانات المواطن
     */
    public function getDataInquiry(string $transactionId, string $otpCode, string $type = 'nin'): array
    {
        $token = $this->getJwtToken();

        $response = $this->http()->withHeaders([
            'Authorization'      => 'Bearer ' . $token,
            'operation-ref'      => $transactionId,
            'service-identifier' => $this->serviceIdentifier($type),
            'usage-code'         => $this->usageCode,
            'operator-id'        => $this->operatorId,
            'app-id'             => $this->appId,
            'app-key'            => $this->appKey,
        ])->get("{$this->baseUrl}/api/v1/yakeen/consent/data", [
            'otpCode' => $otpCode,
        ]);

        if (! $response->successful()) {
            $body = $response->body();
            Log::error('Yakeen data inquiry failed', [
                'body' => $body,
                'status' => $response->status(),
                'error_code' => self::parseApiErrorCode($body),
            ]);
            throw new \Exception(self::userFacingMessage($body, 'verify'));
        }

        return [
            'status' => 'success',
            'data'   => $response->json(),
        ];
    }
}
