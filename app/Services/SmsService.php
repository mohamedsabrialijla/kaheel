<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $mobile, string $message): bool
    {
        $url = (string) config('services.sms.url');
        $key = (string) config('services.sms.key');

        if ($url === '' || $key === '') {
            Log::warning('SMS provider is not configured', ['mobile' => $mobile]);

            return false;
        }

        $response = Http::asForm()
            ->timeout(15)
            ->post($url, [
                'to' => $mobile,
                'message' => $message,
                'sender' => config('services.sms.sender', 'Kaheel'),
                'api_key' => $key,
            ]);

        if ($response->failed()) {
            Log::error('SMS sending failed', [
                'mobile' => $mobile,
                'status' => $response->status(),
            ]);

            return false;
        }

        return true;
    }
}
