<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class BricksCustomerClient
{
    public function login(string $login, string $password): array
    {
        try {
            $response = $this->http()->post($this->url('/v2/customer/session'), [
                'login' => $login,
                'password' => $password,
            ]);
        } catch (ConnectionException $e) {
            report($e);

            return ['ok' => false, 'down' => true];
        }

        $body = $response->json();
        $user = is_array($body) ? ($body['data']['user'] ?? null) : null;
        $token = is_array($body) ? ($body['data']['auth_token'] ?? null) : null;

        if (!$response->successful() || !is_array($user) || !is_string($token) || $token === '') {
            return ['ok' => false, 'down' => false];
        }

        return [
            'ok' => true,
            'token' => $token,
            'user' => $user,
            'units' => $this->units($token),
        ];
    }

    public function changePassword(string $login, string $password): array
    {
        $path = trim((string) config('services.bricks.password_path', ''));
        if ($path === '') {
            return ['ok' => false, 'missing' => true];
        }

        try {
            $response = $this->http()->post($this->url($path), [
                'login' => $login,
                'password' => $password,
                'password_confirmation' => $password,
            ]);
        } catch (ConnectionException $e) {
            report($e);

            return ['ok' => false, 'down' => true];
        }

        if ($response->status() === 404) {
            return ['ok' => false, 'missing' => true];
        }

        return ['ok' => $response->successful()];
    }

    public function units(string $token): array
    {
        try {
            $response = $this->http()
                ->withHeaders([
                    'auth-token' => $token,
                    'slug' => (string) config('services.bricks.slug'),
                ])
                ->get($this->url('/v2/customer/units'));
        } catch (ConnectionException $e) {
            report($e);

            return [];
        }

        $compounds = $response->json('data.compounds');
        if (!$response->successful() || !is_array($compounds)) {
            return [];
        }

        $stored = [];
        foreach ($compounds as $row) {
            if (!is_array($row)) {
                continue;
            }
            $compound = is_array($row['compound'] ?? null) ? $row['compound'] : [];
            $units = [];
            foreach ($row['units'] ?? [] as $unit) {
                if (!is_array($unit)) {
                    continue;
                }
                $units[] = [
                    'id' => $unit['id'] ?? null,
                    'unit_no' => (string) ($unit['unit_no'] ?? ''),
                    'bedrooms_number' => $unit['bedrooms_number'] ?? null,
                    'bathrooms_number' => $unit['bathrooms_number'] ?? null,
                    'status_text' => (string) ($unit['status_text'] ?? ''),
                    'squar_meters' => $unit['squar_meters'] ?? null,
                ];
            }
            $stored[] = [
                'id' => $compound['id'] ?? null,
                'display_name' => (string) ($compound['display_name'] ?? ''),
                'compound_number' => (string) ($compound['compound_number'] ?? ''),
                'short_name' => (string) ($compound['short_name'] ?? ''),
                'owned_units_count' => (int) ($compound['owned_units_count'] ?? count($units)),
                'units' => $units,
            ];
        }

        return $stored;
    }

    protected function http()
    {
        $options = ['timeout' => 20];
        if (defined('CURLSSLOPT_NATIVE_CA')) {
            $options['curl'] = [
                CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA,
            ];
        }

        return Http::withOptions($options)->acceptJson();
    }

    protected function url(string $path): string
    {
        return rtrim((string) config('services.bricks.url'), '/').$path;
    }
}
