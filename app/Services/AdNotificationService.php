<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * إشعارات الإعلانات: تُحفظ في notifications + notification_user (للقائمة والعداد داخل التطبيق) ثم تُرسل FCM.
 * لا يوجد لغة محفوظة للمستلم، لذلك النصوص بالعربي.
 */
class AdNotificationService
{
    protected static ?bool $hasExtraColumns = null;

    /**
     * إعلان جديد ← متابعو صاحب الإعلان فقط.
     */
    public static function newAd(Ad $ad, User $owner): void
    {
        $followerIds = $owner->followers()->pluck('users.id')->all();

        [$title, $body] = self::content('new_ad', $ad, $owner);

        self::deliver($followerIds, $title, $body, 'new_ad', $ad, $owner->id);
    }

    /**
     * نص الإشعار حسب النوع: [title, body].
     */
    public static function content(string $type, ?Ad $ad, ?User $actor = null, int $adsCount = 1): array
    {
        $adTitle = $ad ? self::adTitle($ad) : '';
        $name = $actor ? self::userName($actor) : '';

        return match ($type) {
            'new_ad' => ['إعلان جديد', 'تم إضافة إعلان جديد من قبل ' . $name . ' : ' . $adTitle],
            'comment_on_my_ad' => ['رد جديد', 'رد جديد على إعلانك : ' . $adTitle . ' بواسطة ' . $name],
            'comment_on_followed_ad' => ['تعليق جديد', 'يوجد تعليق جديد على الإعلان المتابع : ' . $adTitle . ' من العضو ' . $name],
            'refresh_reminder' => $adsCount > 1
                ? ['تحديث الإعلان', 'لديك ' . $adsCount . ' من الإعلانات بحاجة إلى تحديث، حدّثها الآن لتظهر في أعلى القائمة']
                : ['تحديث الإعلان', 'حان وقت تحديث إعلانك : ' . $adTitle . '، حدّثه الآن ليظهر في أعلى القائمة'],
            default => throw new \InvalidArgumentException("Unknown notification type: {$type}"),
        };
    }

    /**
     * data الخاصة بالـ push (بدون unread_count، يُضاف لكل مستخدم عند الإرسال).
     */
    public static function pushData(string $type, $notificationId, ?Ad $ad, array $userIds): array
    {
        $ownerId = count($userIds) === 1 ? (int) reset($userIds) : '';

        // إعلان محدد ← صفحة الإعلان، بدون إعلان (تذكير بعدة إعلانات) ← صفحة المستخدم نفسه
        $action = $ad
            ? FcmNotificationService::actionPayload('ad', $ad->id)
            : FcmNotificationService::actionPayload('user', $ownerId);

        $data = array_merge(['notification_id' => $notificationId], $action);

        // user_id فقط في طلب التحديث (صاحب الإعلان)
        if ($type === 'refresh_reminder') {
            $data['user_id'] = $ownerId;
        }

        return $data;
    }

    /**
     * تعليق جديد:
     * - صاحب الإعلان: "رد جديد على إعلانك : العنوان بواسطة فلان"
     * - من يتابع ردود الإعلان (علّق عليه سابقاً أو ضغط متابعة): "يوجد تعليق جديد على الإعلان المتابع : العنوان من العضو فلان"
     */
    public static function newComment(Ad $ad, User $commenter): void
    {
        $adOwnerId = (int) $ad->user_id;
        $commenterId = (int) $commenter->id;

        if ($adOwnerId !== $commenterId) {
            [$title, $body] = self::content('comment_on_my_ad', $ad, $commenter);
            self::deliver([$adOwnerId], $title, $body, 'comment_on_my_ad', $ad, $commenterId);
        }

        $previousCommenterIds = Comment::where('ad_id', $ad->id)
            ->distinct()
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $followUpRaw = (string) ($ad->getRawOriginal('follow_up') ?? '');
        $followUpIds = array_map('intval', array_filter(explode(',', trim($followUpRaw))));

        $followerIds = array_diff(
            array_unique(array_merge($previousCommenterIds, $followUpIds)),
            [$adOwnerId, $commenterId]
        );

        if (!empty($followerIds)) {
            [$title, $body] = self::content('comment_on_followed_ad', $ad, $commenter);
            self::deliver($followerIds, $title, $body, 'comment_on_followed_ad', $ad, $commenterId);
        }
    }

    /**
     * تذكير صاحب الإعلانات بتحديثها (من الأمر المجدول ads:refresh-reminders).
     * إعلان واحد ← يذكر العنوان ويفتح الإعلان، أكثر من إعلان ← إشعار واحد بالعدد.
     *
     * @param \Illuminate\Support\Collection<int, Ad> $ads
     */
    public static function refreshReminder(User $owner, $ads): bool
    {
        if ($ads->isEmpty()) {
            return false;
        }

        $ad = $ads->count() === 1 ? $ads->first() : null;
        [$title, $body] = self::content('refresh_reminder', $ad, null, $ads->count());

        return self::deliver([$owner->id], $title, $body, 'refresh_reminder', $ad, null);
    }

    protected static function deliver(array $userIds, string $title, string $body, string $type, ?Ad $ad, ?int $actorId): bool
    {
        try {
            $userIds = self::filterRecipients($userIds, $actorId);
            if (empty($userIds)) {
                return false;
            }

            $notification = new Notification();
            $notification->title = $title;
            $notification->body = $body;
            $notification->target_type = 'specific';
            $notification->users = json_encode(array_values($userIds));
            if (self::hasExtraColumns()) {
                $notification->type = $type;
                $notification->ad_id = $ad?->id;
                $notification->sender_id = $actorId;
            }
            $notification->save();

            $now = now();
            $rows = array_map(fn ($userId) => [
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'read' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ], $userIds);

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('notification_user')->insert($chunk);
            }

            FcmNotificationService::sendToUsersWithUnreadCount(
                $userIds,
                $title,
                $body,
                self::pushData($type, $notification->id, $ad, $userIds)
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('Ad notification failed', [
                'type' => $type,
                'ad_id' => $ad?->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * يستبعد: صاحب الفعل، المستخدمين غير الموجودين، وأي طرف بينه وبين صاحب الفعل حظر.
     */
    protected static function filterRecipients(array $userIds, ?int $actorId): array
    {
        $userIds = array_values(array_diff(
            array_unique(array_filter(array_map('intval', $userIds))),
            $actorId ? [$actorId] : []
        ));

        if (empty($userIds)) {
            return [];
        }

        if (!$actorId) {
            return User::whereIn('id', $userIds)->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $blockedIds = DB::table('blocks')
            ->where(function ($q) use ($actorId, $userIds) {
                $q->where('user_id', $actorId)->whereIn('blocked_user_id', $userIds);
            })
            ->orWhere(function ($q) use ($actorId, $userIds) {
                $q->where('blocked_user_id', $actorId)->whereIn('user_id', $userIds);
            })
            ->get(['user_id', 'blocked_user_id'])
            ->flatMap(fn ($row) => [(int) $row->user_id, (int) $row->blocked_user_id])
            ->all();

        $userIds = array_values(array_diff($userIds, $blockedIds));

        if (empty($userIds)) {
            return [];
        }

        return User::whereIn('id', $userIds)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    protected static function adTitle(Ad $ad): string
    {
        return $ad->translate('ar')?->title
            ?? $ad->translate('en')?->title
            ?? $ad->translate(app()->getLocale())?->title
            ?? '';
    }

    protected static function userName(User $user): string
    {
        return trim((string) ($user->name ?: $user->username ?: ''));
    }

    protected static function hasExtraColumns(): bool
    {
        if (self::$hasExtraColumns === null) {
            self::$hasExtraColumns = Schema::hasColumns('notifications', ['type', 'ad_id', 'sender_id']);
        }

        return self::$hasExtraColumns;
    }
}
