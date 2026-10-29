<?php

namespace App\Services;

use App\Models\KaheelOrderMessage;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Http\HttpClientOptions;
use Throwable;

class KaheelOrderChat
{
    public function publish(KaheelOrderMessage $message): void
    {
        try {
            $database = $this->database();
            if (!$database) {
                return;
            }

            $locale = app()->getLocale();
            $url = null;
            if ($message->media_path) {
                $url = $message->sender_role === 'customer'
                    ? url($locale.'/orders/'.$message->order_id.'/messages/'.$message->id.'/media')
                    : url($locale.'/web/KaheelOrders/messageMedia/'.$message->id);
            }
            $database->getReference('orders/'.$message->order_id.'/messages/'.$message->id)->set([
                'id' => (int) $message->id,
                'order_id' => (int) $message->order_id,
                'role' => (string) $message->sender_role,
                'name' => (string) $message->sender_name,
                'kind' => $message->kind ?: 'text',
                'body' => (string) $message->body,
                'url' => $url,
                'duration' => $message->duration === null ? null : (int) $message->duration,
                'created_at' => kaheel_iso($message->created_at),
            ]);
        } catch (Throwable $e) {
            Log::warning('Kaheel order chat was saved locally but not synced.', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function database(): ?Database
    {
        $path = $this->credentialsPath();
        $url = rtrim((string) config('services.kaheel_firebase.database_url'), '/');
        $project = (string) config('services.kaheel_firebase.project_id');
        if ($path === '' || !is_file($path) || $url === '' || $project === '') {
            Log::warning('Kaheel order chat Firebase settings are incomplete.');

            return null;
        }

        $account = json_decode((string) file_get_contents($path), true);
        if (!is_array($account) || ($account['project_id'] ?? '') !== $project) {
            Log::warning('Kaheel order chat credentials do not match the Firebase project.');

            return null;
        }

        $options = HttpClientOptions::default();
        if (defined('CURLSSLOPT_NATIVE_CA')) {
            $options = $options->withGuzzleConfigOption('curl', [
                CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA,
            ]);
        }

        return (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri($url)
            ->withHttpClientOptions($options)
            ->createDatabase();
    }

    private function credentialsPath(): string
    {
        $configured = (string) config('services.kaheel_firebase.credentials');
        if ($configured !== '' && !preg_match('/^(?:[A-Za-z]:[\\\\\\/]|\/)/', $configured)) {
            return base_path($configured);
        }

        return $configured;
    }
}
