<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    protected static ?string $accessToken = null;
    protected static ?array $serviceAccount = null;
    protected static ?string $lastError = null;

    public static function getLastError(): ?string
    {
        return self::$lastError;
    }

    public static function credentialsAvailable(): bool
    {
        return file_exists(self::credentialsPath());
    }

    protected static function credentialsPath(): string
    {
        $configured = config('services.firebase.credentials');
        if (is_string($configured) && $configured !== '' && file_exists($configured)) {
            return $configured;
        }

        $legacy = storage_path('app/brandy-bb646-firebase-adminsdk-fbsvc-c95660894c.json');
        if (file_exists($legacy)) {
            return $legacy;
        }

        return is_string($configured) && $configured !== '' ? $configured : $legacy;
    }

    protected static function getAccessToken(): ?string
    {
        if (self::$accessToken !== null) {
            return self::$accessToken;
        }

        self::$lastError = null;
        $path = self::credentialsPath();

        if (!file_exists($path)) {
            self::$lastError = 'Firebase credentials file not found: ' . $path;
            Log::error(self::$lastError);
            return null;
        }

        self::$serviceAccount = json_decode(file_get_contents($path), true);
        if (!is_array(self::$serviceAccount)) {
            self::$lastError = 'Invalid Firebase credentials JSON.';
            Log::error(self::$lastError, ['path' => $path]);
            return null;
        }

        $jwt = \Firebase\JWT\JWT::encode([
            'iss' => self::$serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => time(),
            'exp' => time() + 3600,
        ], self::$serviceAccount['private_key'], 'RS256');

        $tokenResponse = Http::asForm()->withOptions(['verify' => false])
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (!$tokenResponse->successful()) {
            self::$lastError = 'Cannot get FCM access token: ' . $tokenResponse->body();
            Log::error(self::$lastError);
            return null;
        }

        self::$accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (!self::$accessToken) {
            self::$lastError = 'FCM access token missing in OAuth response.';
            Log::error(self::$lastError);
        }

        return self::$accessToken;
    }

    /**
     * @return array{success:int,failed:int,errors:array<int,string>}
     */
    public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter(array_map('strval', $tokens))));
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];

        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            $result['errors'][] = self::$lastError ?? 'FCM access token unavailable.';
            $result['failed'] = count($tokens);
            return $result;
        }

        if (empty($tokens)) {
            $result['errors'][] = 'No device tokens to send.';
            return $result;
        }

        foreach ($tokens as $token) {
            $payload = self::buildMessage($token, $title, $body, $data);

            if (config('services.firebase.log_payloads')) {
                Log::info('FCM payload', ['token_prefix' => substr($token, 0, 16) . '...', 'message' => $payload['message']]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->withOptions(['verify' => false])
                ->post(
                    'https://fcm.googleapis.com/v1/projects/' . self::$serviceAccount['project_id'] . '/messages:send',
                    $payload
                );

            if ($response->successful()) {
                $result['success']++;
                continue;
            }

            $result['failed']++;
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            $result['errors'][] = $errorMessage;

            Log::warning('FCM send failed', [
                'token_prefix' => substr($token, 0, 24) . '...',
                'status' => $response->status(),
                'error' => $errorBody,
            ]);

            if (self::isInvalidTokenError($errorMessage, is_array($errorBody) ? $errorBody : [])) {
                DeviceToken::where('device_token', $token)->update(['is_active' => false]);
            }
        }

        return $result;
    }

    /**
     * وجهة فتح الإشعار في التطبيق: action = ad | user | chat
     *
     * @return array{action:string,action_id:string,action_url:string}
     */
    public static function actionPayload(string $action, $actionId): array
    {
        $base = rtrim((string) config('services.firebase.action_base_url', 'https://www.brandy.sa'), '/');

        $url = match ($action) {
            'ad' => "{$base}/en/ad-details/{$actionId}",
            'user' => "{$base}/en/seller-page?user_id={$actionId}",
            'chat' => "{$base}/",
            default => '',
        };

        return [
            'action' => $action,
            'action_id' => (string) $actionId,
            'action_url' => $url,
        ];
    }

    /**
     * جسم رسالة FCM v1 كما يُرسل بالضبط (يُستخدم أيضاً في basic/testNotification).
     */
    public static function buildMessage(string $token, string $title, string $body, array $data = []): array
    {
        $aps = ['sound' => 'default'];
        if (isset($data['unread_count']) && is_numeric($data['unread_count'])) {
            $aps['badge'] = (int) $data['unread_count'];
        }

        return [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_merge(
                    ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    array_map(fn ($v) => (string) ($v ?? ''), $data)
                ),
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => $aps,
                    ],
                ],
            ],
        ];
    }

    /**
     * INVALID_ARGUMENT يرجع أيضاً لأخطاء الـ payload، فلا نعطّل التوكن إلا إذا الخطأ عن التوكن نفسه.
     */
    protected static function isInvalidTokenError(string $message, array $errorBody = []): bool
    {
        $errorCodes = collect($errorBody['error']['details'] ?? [])->pluck('errorCode')->filter()->all();
        if (in_array('UNREGISTERED', $errorCodes, true)) {
            return true;
        }

        $message = strtolower($message);
        return str_contains($message, 'unregistered')
            || str_contains($message, 'requested entity was not found')
            || str_contains($message, 'not a valid fcm registration token');
    }

    public static function tokensForUserIds(array $userIds): array
    {
        $userIds = array_values(array_unique(array_filter($userIds)));
        if (empty($userIds)) {
            return [];
        }

        return DeviceToken::query()
            ->whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->pluck('device_token')
            ->unique()
            ->values()
            ->toArray();
    }

    public static function allActiveTokens(): array
    {
        return DeviceToken::query()
            ->where('is_active', true)
            ->pluck('device_token')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * @return array{success:int,failed:int,errors:array<int,string>}
     */
    public static function sendToUserIds(array $userIds, string $title, string $body, array $data = []): array
    {
        return self::sendToTokens(self::tokensForUserIds($userIds), $title, $body, $data);
    }

    /**
     * عدد الإشعارات غير المقروءة لكل مستخدم من notification_user.
     *
     * @return array<int,int> user_id => count
     */
    public static function unreadCountsFor(array $userIds): array
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        if (empty($userIds)) {
            return [];
        }

        $counts = [];
        foreach (array_chunk($userIds, 1000) as $chunk) {
            $rows = DB::table('notification_user as nu')
                ->join('notifications as n', 'n.id', '=', 'nu.notification_id')
                ->whereNull('n.deleted_at')
                ->whereIn('nu.user_id', $chunk)
                ->where('nu.read', 0)
                ->groupBy('nu.user_id')
                ->selectRaw('nu.user_id, COUNT(DISTINCT nu.notification_id) as total')
                ->get();

            foreach ($rows as $row) {
                $counts[(int) $row->user_id] = (int) $row->total;
            }
        }

        return $counts;
    }

    /**
     * يضيف unread_count، ويبقى user_id (إن وُجد) بعده مباشرة.
     */
    public static function withUnreadCount(array $data, int $unreadCount): array
    {
        $userId = $data['user_id'] ?? null;
        unset($data['user_id']);
        $data['unread_count'] = $unreadCount;
        if ($userId !== null) {
            $data['user_id'] = $userId;
        }

        return $data;
    }

    /**
     * إرسال لكل مستخدم على حدة مع unread_count الخاص به (و badge على iOS).
     * يجب استدعاؤها بعد حفظ الإشعار في notification_user حتى يُحسب الإشعار الحالي.
     *
     * @return array{success:int,failed:int,errors:array<int,string>}
     */
    public static function sendToUsersWithUnreadCount(array $userIds, string $title, string $body, array $data = []): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];

        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
        if (empty($userIds)) {
            $result['errors'][] = 'No users to send.';
            return $result;
        }

        $tokensByUser = DeviceToken::query()
            ->whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->get(['user_id', 'device_token'])
            ->groupBy('user_id');

        if ($tokensByUser->isEmpty()) {
            $result['errors'][] = 'No device tokens to send.';
            return $result;
        }

        $unreadCounts = self::unreadCountsFor($tokensByUser->keys()->all());

        foreach ($tokensByUser as $userId => $rows) {
            $userResult = self::sendToTokens(
                $rows->pluck('device_token')->all(),
                $title,
                $body,
                self::withUnreadCount($data, $unreadCounts[(int) $userId] ?? 0)
            );

            $result['success'] += $userResult['success'];
            $result['failed'] += $userResult['failed'];
            $result['errors'] = array_merge($result['errors'], $userResult['errors']);
        }

        return $result;
    }

    public static function sendAdNotification(array $userIds, int $adId, int $adOwnerUserId, string $title, string $body, ?int $excludeUserId = null): int
    {
        $userIds = array_unique(array_filter($userIds));
        if ($excludeUserId !== null) {
            $userIds = array_values(array_diff($userIds, [$excludeUserId]));
        }
        if (empty($userIds)) {
            return 0;
        }

        return self::sendToUserIds($userIds, $title, $body, self::actionPayload('ad', $adId))['success'];
    }

    /**
     * @return array{success:int,failed:int,errors:array<int,string>}
     */
    public static function sendPanelNotification(array $userIds, string $title, string $body): array
    {
        return self::sendToUserIds($userIds, $title, $body, [
            'action' => '',
            'action_id' => '',
            'action_url' => '',
        ]);
    }
}
