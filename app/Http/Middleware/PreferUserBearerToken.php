<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanctum يقدّم جلسة الأدمن (guard admin) على Bearer token، فلو الأدمن مسجّل في لوحة التحكم
 * بنفس المتصفح تصير طلبات الموقع باسم الأدمن. هنا توكن المستخدم يأخذ الأولوية.
 */
class PreferUserBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token && Auth::guard('admin')->check()) {
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken?->tokenable;

            if ($user instanceof User && $this->isValid($accessToken)) {
                Auth::guard('sanctum')->setUser($user->withAccessToken($accessToken));
            }
        }

        return $next($request);
    }

    protected function isValid(PersonalAccessToken $accessToken): bool
    {
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return false;
        }

        $expiration = config('sanctum.expiration');

        return !$expiration || $accessToken->created_at->gt(now()->subMinutes($expiration));
    }
}
