<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// تذكير تحديث الإعلانات (عدد الأيام من الإعدادات: refresh_reminder_days)
Schedule::command('ads:refresh-reminders')
    ->dailyAt('10:00')
    ->timezone('Asia/Riyadh');
