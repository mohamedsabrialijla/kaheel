<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\AdNotificationService;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * تجربة الإشعارات: يرجع نفس رسالة FCM التي تُرسل فعلياً لكل نوع،
 * ومع send=1 يرسلها لأجهزة صاحب الـ token فقط. لا يحفظ شيئاً في جدول الإشعارات.
 */
class NotificationTestController extends Controller
{
    protected const TYPES = ['new_ad', 'comment_on_my_ad', 'comment_on_followed_ad', 'refresh_reminder', 'chat', 'panel'];

    public function test(Request $request)
    {
        $me = auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:' . implode(',', self::TYPES),
            'send' => 'nullable|in:0,1,true,false',
            'ad_id' => 'nullable|integer',
            'ads_count' => 'nullable|integer|min:1|max:100',
            'kind' => 'nullable|in:text,image,audio',
            'message' => 'nullable|string|max:5000',
            'title' => 'nullable|string|max:255',
        ], [
            'type.required' => 'نوع الإشعار مطلوب: ' . implode(' / ', self::TYPES),
            'type.in' => 'نوع الإشعار غير صحيح: ' . implode(' / ', self::TYPES),
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $type = $request->input('type');
        $otherUser = User::where('id', '!=', $me->id)->orderBy('id')->first() ?? $me;
        $currentUnread = FcmNotificationService::unreadCountsFor([$me->id])[$me->id] ?? 0;

        if ($type === 'chat') {
            [$title, $body, $data] = ChatNotificationController::buildPush(
                $otherUser,
                $request->input('kind') ?: 'text',
                (string) ($request->input('message') ?: 'هلا وغلا، الإعلان لسا متوفر؟'),
                'test_chat_id'
            );
            $data['unread_count'] = $currentUnread;
        } elseif ($type === 'panel') {
            $title = (string) ($request->input('title') ?: 'إشعار من الإدارة');
            $body = (string) ($request->input('message') ?: 'هذا إشعار تجريبي من لوحة التحكم');
            $data = [
                'notification_id' => 'test',
                'action' => '',
                'action_id' => '',
                'action_url' => '',
                'unread_count' => $currentUnread + 1,
            ];
        } else {
            $ad = $this->sampleAd($request, $type, $me);
            if (!$ad) {
                return mainResponse(false, 'لا يوجد إعلان للتجربة، أرسل ad_id.', null, 404, 'items', '');
            }

            $adsCount = $type === 'refresh_reminder' ? (int) ($request->input('ads_count') ?: 1) : 1;
            $adForPush = $adsCount > 1 ? null : $ad;
            $actor = match ($type) {
                'new_ad' => $ad->user ?? $otherUser,
                'refresh_reminder' => null,
                default => $otherUser,
            };

            [$title, $body] = AdNotificationService::content($type, $adForPush, $actor, $adsCount);
            $data = AdNotificationService::pushData($type, 'test', $adForPush, [$me->id]);
            // الإشعار الحقيقي يُحفظ قبل الإرسال فيُحسب ضمن غير المقروء
            $data = FcmNotificationService::withUnreadCount($data, $currentUnread + 1);
        }

        $deviceRows = DeviceToken::where('user_id', $me->id)->get(['device_token', 'is_active']);
        $myTokens = $deviceRows->pluck('device_token')->all();
        $shouldSend = in_array((string) $request->input('send'), ['1', 'true'], true);

        $result = ['success' => 0, 'failed' => 0, 'errors' => []];
        if ($shouldSend) {
            if (empty($myTokens)) {
                $result['errors'][] = "لا يوجد device token للمستخدم رقم {$me->id}، سجّل الدخول من التطبيق بنفس الحساب أولاً.";
            }
            // نجرب كل توكنات المستخدم حتى المعطّلة، والتوكن اللي ينجح نرجّعه فعّال
            foreach ($myTokens as $token) {
                $one = FcmNotificationService::sendToTokens([$token], $title, $body, $data);
                $result['success'] += $one['success'];
                $result['failed'] += $one['failed'];
                $result['errors'] = array_merge($result['errors'], $one['errors']);
                if ($one['success'] > 0) {
                    DeviceToken::where('device_token', $token)->update(['is_active' => true]);
                }
            }
        }

        return mainResponse(true, $shouldSend
            ? "تم إرسال الإشعار التجريبي ({$result['success']}/" . count($myTokens) . ')'
            : 'معاينة فقط، أضف send=1 للإرسال لجهازك', [
            'title' => $title,
            'body' => $body,
            // اسم المفتاح ليس "data" لأن mainResponse يفك أي مصفوفة فيها data
            'push_data' => FcmNotificationService::buildMessage('', $title, $body, $data)['message']['data'],
            'fcm_message' => FcmNotificationService::buildMessage('<device_token>', $title, $body, $data),
            'user_id' => $me->id,
            'my_devices' => $deviceRows->count(),
            'active_devices' => $deviceRows->where('is_active', true)->count(),
            'sent' => $shouldSend,
            'send_result' => $shouldSend ? [
                'success' => $result['success'],
                'failed' => $result['failed'],
                'errors' => array_slice($result['errors'], 0, 3),
            ] : null,
        ], 200, 'items', '');
    }

    protected function sampleAd(Request $request, string $type, User $me): ?Ad
    {
        if ($request->filled('ad_id')) {
            return Ad::with('translations', 'user')->find($request->ad_id);
        }

        $mine = in_array($type, ['comment_on_my_ad', 'refresh_reminder'], true);

        return Ad::with('translations', 'user')
            ->where('status', 'active')
            ->when($mine, fn ($q) => $q->where('user_id', $me->id), fn ($q) => $q->where('user_id', '!=', $me->id))
            ->orderByDesc('updated_at')
            ->first()
            ?? Ad::with('translations', 'user')->where('status', 'active')->orderByDesc('updated_at')->first();
    }
}
