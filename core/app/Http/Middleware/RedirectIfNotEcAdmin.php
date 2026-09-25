<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotEcAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('ec_admin')->check()) {
            return redirect()->route('ec.admin.login');
        }

        $admin = Auth::guard('ec_admin')->user();
        if (!$admin->status) {
            Auth::guard('ec_admin')->logout();

            return redirect()->route('ec.admin.login')->withNotify([['error', 'Admin account is disabled.']]);
        }

        return $next($request);
    }
}
