<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfCrmStaff
{
    public function handle($request, Closure $next, $portal = null)
    {
        if (Auth::guard('crm')->check()) {
            $staff = Auth::guard('crm')->user();

            if ($portal && !$staff->canAccessPortal($portal) && !$staff->isSuper()) {
                return to_route($staff->homeRoute());
            }

            return to_route($staff->homeRoute());
        }

        return $next($request);
    }
}
