<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Support\KaheelPermissions;
use Closure;
use Illuminate\Http\Request;

abstract class KaheelAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $method = $request->route()?->getActionMethod();
            $code = $method ? $this->kaheelPermissionFor($method, $request) : null;
            if ($code && ($deny = KaheelPermissions::denyUnless($code))) {
                return $deny;
            }

            return $next($request);
        });
    }

    abstract protected function kaheelPermissionFor(string $method, Request $request): ?string;
}
