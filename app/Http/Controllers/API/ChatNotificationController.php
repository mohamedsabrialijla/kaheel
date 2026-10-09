<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

/**
 * الشات نفسه على Firebase، هذا فقط لإرسال Push للمستلم بعد إرسال الرسالة.
 */
class ChatNotificationController extends Controller
{
    public function notifyNewMessage(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $sender = auth('sanctum')->user();

        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
            'message' => 'nullable|string|max:5000',
            'kind' => 'nullable|in:text,image,audio',
            'chat_id' => 'nullable|string|max:255',
        ], [
            'receiver_id.required' => $isAr ? 'المستلم مطلوب.' : 'Receiver is required.',
            'kind.in' => $isAr ? 'نوع الرسالة غير صحيح.' : 'Invalid message type.',
            'message.max' => $isAr ? 'الرسالة طويلة جداً.' : 'Message is too long.',
        ]);

        if (!$sender instanceof User) {
            Log::info('Chat push skipped: sender is not a user', ['auth_class' => $sender ? get_class($sender) : null]);
            return mainResponse(false, $isAr ? 'سجّل الدخول بحساب مستخدم.' : 'Login as a user.', ['sent' => false], 403, 'items', '');
        }

        $logContext = [
            'sender_id' => $sender->id,
            'receiver_id' => $request->input('receiver_id'),
            'kind' => $request->input('kind'),
            'chat_id' => $request->input('chat_id'),
        ];

        if ($validator->fails()) {
            Log::info('Chat push skipped: validation', $logContext + ['errors' => $validator->errors()->all()]);
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        // يقبل 57 أو "user_57" (نفس صيغة participants في Firebase)
        $rawReceiver = trim((string) $request->input('receiver_id'));
        if (Str::startsWith($rawReceiver, 'admin_')) {
            Log::info('Chat push skipped: admin receiver', $logContext);
            return mainResponse(true, $isAr ? 'لا يوجد إشعار للإدارة.' : 'No push for admin.', ['sent' => false], 200, 'items', '');
        }

        $receiverId = (int) preg_replace('/^user_/', '', $rawReceiver);

        if ($receiverId <= 0 || !User::whereKey($receiverId)->exists()) {
            Log::info('Chat push skipped: receiver not found', $logContext);
            return mainResponse(false, $isAr ? 'المستخدم غير موجود.' : 'User not found.', null, 404, 'items', '');
        }

        if ($receiverId === (int) $sender->id) {
            Log::info('Chat push skipped: receiver is sender', $logContext);
            return mainResponse(false, $isAr ? 'لا يمكنك إرسال إشعار لنفسك.' : 'You cannot notify yourself.', null, 422, 'items', '');
        }

        $kind = $request->input('kind') ?: 'text';
        $text = trim((string) $request->input('message', ''));

        if ($kind === 'text' && $text === '') {
            Log::info('Chat push skipped: empty text', $logContext);
            return mainResponse(false, $isAr ? 'نص الرسالة مطلوب.' : 'Message text is required.', null, 422, 'items', '');
        }

        $isBlocked = DB::table('blocks')
            ->where(function ($q) use ($sender, $receiverId) {
                $q->where('user_id', $sender->id)->where('blocked_user_id', $receiverId);
            })
            ->orWhere(function ($q) use ($sender, $receiverId) {
                $q->where('user_id', $receiverId)->where('blocked_user_id', $sender->id);
            })
            ->exists();

        if ($isBlocked) {
            Log::info('Chat push skipped: blocked', $logContext);
            return mainResponse(true, $isAr ? 'تم إرسال الإشعار بنجاح.' : 'Notification sent successfully.', ['sent' => false], 200, 'items', '');
        }

        [$title, $body, $data] = self::buildPush($sender, $kind, $text, (string) $request->input('chat_id', ''));

        // unread_count = إشعارات غير مقروءة (رسائل الشات على Firebase ولا تُحسب فيه)
        $result = FcmNotificationService::sendToUsersWithUnreadCount([$receiverId], $title, $body, $data);

        Log::info('Chat push result', $logContext + [
            'success' => $result['success'],
            'failed' => $result['failed'],
            'errors' => array_slice($result['errors'], 0, 3),
        ]);

        return mainResponse(true, $isAr ? 'تم إرسال الإشعار بنجاح.' : 'Notification sent successfully.', [
            'sent' => $result['success'] > 0,
            'devices' => $result['success'],
        ], 200, 'items', '');
    }

    /**
     * @return array{0:string,1:string,2:array} [title, body, data]
     */
    public static function buildPush(User $sender, string $kind, string $text, string $chatId): array
    {
        $senderName = trim((string) ($sender->name ?: $sender->username ?: ''));

        $body = match ($kind) {
            'image' => '📷 صورة',
            'audio' => '🎤 رسالة صوتية',
            default => Str::limit($text, 100, '...'),
        };

        return ['رسالة جديدة من ' . $senderName, $body, array_merge(
            FcmNotificationService::actionPayload('chat', $sender->id),
            ['chat_id' => $chatId]
        )];
    }
}
