<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKaheelAdminActive
{
    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admin')->user();
        if ($admin && ($admin->status ?? 'active') === 'not_active') {
            $admin->tokens()->delete();
            auth('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => kaheel_msg('تم إيقاف هذا الحساب ولا يمكن استخدامه.', 'This account has been disabled.'),
            ], 401);
        }

        return $next($request);
    }
}
