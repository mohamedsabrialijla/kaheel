<?php

namespace App\Http\Controllers;

use App\Services\YakeenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * تشخيص اتصال يقين — يعمل فقط عند APP_DEBUG=true أو YAKEEN_DEBUG_ENABLED=true
 */
class YakeenDebugController extends Controller
{
    private const BASE_URL = 'https://yakeencore.api.elm.sa';

    private const TRIAL_SAUDI = [
        'type' => 'nin',
        'identifier' => '1174202257',
        'h_day' => 3, 'h_month' => 7, 'h_year' => 1379,
        'g_day' => 1, 'g_month' => 1, 'g_year' => 1960,
    ];

    private const TRIAL_IQAMA = [
        'type' => 'iqama',
        'identifier' => '2054312802',
        'h_day' => 29, 'h_month' => 11, 'h_year' => 1404,
        'g_day' => 26, 'g_month' => 8, 'g_year' => 1984,
    ];

    private const SERVICE_IDS = [
        'nin'   => 'e4dbc7f4-b941-46be-8e98-f52ae1d32bce',
        'iqama' => 'e514377a-05b7-4da8-a00b-3d491894934b',
    ];

    public function probe(Request $request): JsonResponse
    {
        if (! $this->debugEnabled()) {
            abort(404);
        }

        $type = $request->input('type', self::TRIAL_SAUDI['type']);
        if (! in_array($type, ['nin', 'iqama'], true)) {
            return response()->json(['ok' => false, 'error' => 'type must be nin or iqama'], 422);
        }

        $defaults = $type === 'nin' ? self::TRIAL_SAUDI : self::TRIAL_IQAMA;

        $identifier = (string) $request->input('identifier', $defaults['identifier']);
        $crossMode  = $request->input('crosscheck_mode');

        $creds = $this->credentials();

        $hDay   = (int) $request->input('h_day', $request->input('birth_day', $defaults['h_day']));
        $hMonth = (int) $request->input('h_month', $request->input('birth_month', $defaults['h_month']));
        $hYear  = (int) $request->input('h_year', $request->input('birth_year', $defaults['h_year']));
        $gDay   = (int) $request->input('g_day', $defaults['g_day']);
        $gMonth = (int) $request->input('g_month', $defaults['g_month']);
        $gYear  = (int) $request->input('g_year', $defaults['g_year']);

        // ما تكتبه في الرابط يذهب ليقين حرفياً — dateOfBirth أو date_format (alias)
        $dateOfBirthParamUsed = null;
        $dateOfBirthFromQuery = null;

        if ($request->has('dateOfBirth')) {
            $dateOfBirthFromQuery = trim((string) $request->input('dateOfBirth'));
            $dateOfBirthParamUsed = 'dateOfBirth';
        } elseif ($request->has('date_format')) {
            $dateOfBirthFromQuery = trim((string) $request->input('date_format'));
            $dateOfBirthParamUsed = 'date_format';
        }

        if ($dateOfBirthFromQuery !== null && $dateOfBirthFromQuery !== '') {
            $dateOfBirth = $dateOfBirthFromQuery;
        } elseif ($crossMode) {
            // البناء من الأجزاء فقط عند تمرير crosscheck_mode صراحةً
            $dateOfBirth = YakeenService::buildConsentDateOfBirth([
                'type' => $type,
                'mode' => $crossMode,
                'h_day' => $hDay,
                'h_month' => $hMonth,
                'h_year' => $hYear,
                'g_day' => $gDay,
                'g_month' => $gMonth,
                'g_year' => $gYear,
            ]);
        } else {
            // افتراضي TRIAL — ELM: yyyy-mm
            $dateOfBirth = YakeenService::buildConsentDateOfBirth([
                'type' => $type,
                'h_day' => $hDay,
                'h_month' => $hMonth,
                'h_year' => $hYear,
                'g_day' => $gDay,
                'g_month' => $gMonth,
                'g_year' => $gYear,
            ]);
        }
        $queryKey = $type === 'nin' ? 'nin' : 'iqama';

        $consentQuery = [
            $queryKey     => $identifier,
            'dateOfBirth' => $dateOfBirth,
        ];

        $headers = [
            'usage-code'  => (string) config('services.yakeen.usage_code', 'USC10004'),
            'operator-id' => (string) config('services.yakeen.operator_id', '1000011021'),
            'app-id'      => $creds['app_id'],
            'app-key'     => $creds['app_key'],
        ];

        $http = Http::withOptions([
            'proxy' => 'http://8.213.38.124:3128',
        ])->connectTimeout(10)->timeout(30);

        // ── 1) Login ──
        $loginResponse = $http->withHeaders([
            'app-id'  => $creds['app_id'],
            'app-key' => $creds['app_key'],
        ])->post(self::BASE_URL . '/api/v3/yakeen/login', [
            'username' => $creds['username'],
            'password' => $creds['password'],
        ]);

        $loginBody = $this->safeJson($loginResponse->body());
        $token     = is_array($loginBody) ? ($loginBody['access_token'] ?? null) : null;

        $result = [
            'ok' => false,
            'env' => [
                'app_debug' => (bool) config('app.debug'),
                'username'  => $creds['username'],
                'app_id'    => $creds['app_id'],
                'app_key_set' => $creds['app_key'] !== '',
            ],
            'input' => [
                'type' => $type,
                'identifier' => $identifier,
                'dateOfBirth_from_query' => $dateOfBirthFromQuery,
                'dateOfBirth_param_used' => $dateOfBirthParamUsed,
                'crosscheck_mode' => $crossMode,
                'hijri' => ['day' => $hDay, 'month' => $hMonth, 'year' => $hYear],
                'gregorian' => ['day' => $gDay, 'month' => $gMonth, 'year' => $gYear],
                'dateOfBirth_for_yakeen' => $dateOfBirth,
                'hint' => 'ELM: yyyy-mm — ?dateOfBirth=1379-07 (سعودي) أو 1984-08 (إقامة). افتراضي TRIAL: 1379-07',
            ],
            'login' => [
                'url' => self::BASE_URL . '/api/v3/yakeen/login',
                'status' => $loginResponse->status(),
                'successful' => $loginResponse->successful(),
                'body' => $loginBody,
                'has_token' => $token !== null,
            ],
            'consent' => null,
        ];

        if (! $loginResponse->successful() || ! $token) {
            return response()->json($result, 200);
        }

        // ── 2) Consent create ──
        $consentResponse = $http->withHeaders(array_merge($headers, [
            'Authorization'      => 'Bearer ' . $token,
            'service-identifier' => self::SERVICE_IDS[$type],
        ]))->get(self::BASE_URL . '/api/v1/yakeen/consent/create', $consentQuery);

        $result['consent'] = [
            'url' => self::BASE_URL . '/api/v1/yakeen/consent/create',
            'query' => $consentQuery,
            'headers_sent' => array_merge($headers, [
                'Authorization' => 'Bearer ***',
                'service-identifier' => self::SERVICE_IDS[$type],
            ]),
            'status' => $consentResponse->status(),
            'successful' => $consentResponse->successful(),
            'headers' => [
                'transaction-id' => $consentResponse->header('transaction-id'),
            ],
            'body' => $this->safeJson($consentResponse->body()),
        ];

        $result['ok'] = $consentResponse->successful();

        Log::info('Yakeen debug probe', [
            'type' => $type,
            'dateOfBirth' => $dateOfBirth,
            'login_status' => $loginResponse->status(),
            'consent_status' => $consentResponse->status(),
        ]);

        return response()->json($result, 200);
    }

    private function debugEnabled(): bool
    {
        return (bool) config('app.debug') || (bool) env('YAKEEN_DEBUG_ENABLED', false);
    }

    /** @return array{username: string, password: string, app_id: string, app_key: string} */
    private function credentials(): array
    {
        return [
            'username' => (string) (config('services.yakeen.username') ?: 'NYK_1327_CAQOMF_TRIAL'),
            'password' => (string) (config('services.yakeen.password') ?: 'wBF4zgIB7j6JOra2vV85'),
            'app_id'   => (string) (config('services.yakeen.app_id') ?: '5b02c61a'),
            'app_key'  => (string) (config('services.yakeen.app_key') ?: 'b1792cfc302b0446edcda7dba92732e8'),
        ];
    }

    private function safeJson(?string $body): mixed
    {
        if ($body === null || $body === '') {
            return null;
        }

        $decoded = json_decode($body, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $body;
    }

}
