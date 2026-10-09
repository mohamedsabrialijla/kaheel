<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Setting;
use App\Models\User;
use App\Services\AdNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * كل X يوم (settings.refresh_reminder_days) يرسل لصاحب كل إعلان فعّال غير محذوف
 * لم يُحدَّث منذ X يوم ولم يُذكَّر خلال آخر X يوم، إشعاراً يطلب تحديث الإعلان.
 */
class SendAdRefreshReminders extends Command
{
    protected $signature = 'ads:refresh-reminders
                            {--dry-run : عرض من سيصله الإشعار بدون إرسال}
                            {--user= : تقييد الإرسال لمستخدم واحد (للتجربة)}';

    protected $description = 'Send "please refresh your ad" notifications every N days (settings.refresh_reminder_days)';

    public function handle(): int
    {
        if (!Schema::hasColumn('settings', 'refresh_reminder_days') || !Schema::hasColumn('ads', 'refresh_reminded_at')) {
            $this->error('Run the migration first (refresh_reminder_days / refresh_reminded_at).');
            return self::FAILURE;
        }

        $days = (int) (Setting::query()->value('refresh_reminder_days') ?? 0);
        if ($days < 1) {
            $this->info('Refresh reminders are disabled (refresh_reminder_days = 0).');
            return self::SUCCESS;
        }

        // إعلان حُدّث يوم 25 والإعداد 5 أيام ← يُذكَّر يوم 30 مهما كانت ساعة التشغيل
        $cutoff = now()->startOfDay()->subDays($days - 1);
        $dryRun = (bool) $this->option('dry-run');

        $dueAds = Ad::query()
            ->with('translations')
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('updated_at')->orWhere('updated_at', '<', $cutoff))
            ->where(fn ($q) => $q->whereNull('refresh_reminded_at')->orWhere('refresh_reminded_at', '<', $cutoff))
            ->when($this->option('user'), fn ($q, $userId) => $q->where('user_id', (int) $userId))
            ->orderBy('user_id')
            ->orderByDesc('updated_at')
            ->get();

        if ($dueAds->isEmpty()) {
            $this->info("No ads need a reminder (every {$days} days).");
            return self::SUCCESS;
        }

        $owners = User::whereIn('id', $dueAds->pluck('user_id')->unique())->get()->keyBy('id');
        $sent = 0;

        foreach ($dueAds->groupBy('user_id') as $ownerId => $ads) {
            $owner = $owners->get($ownerId);
            if (!$owner) {
                continue;
            }

            if ($dryRun) {
                $this->line("[dry-run] user {$ownerId} ({$owner->name}): ads " . $ads->pluck('id')->implode(','));
                continue;
            }

            if (AdNotificationService::refreshReminder($owner, $ads)) {
                // Query Builder حتى لا يتغير updated_at (ترتيب الإعلان)
                DB::table('ads')->whereIn('id', $ads->pluck('id'))->update(['refresh_reminded_at' => now()]);
                $sent++;
            }
        }

        $this->info($dryRun
            ? "[dry-run] {$dueAds->count()} ads / {$dueAds->groupBy('user_id')->count()} owners would be reminded (every {$days} days)."
            : "Sent {$sent} reminders for {$dueAds->count()} ads (every {$days} days).");

        return self::SUCCESS;
    }
}
