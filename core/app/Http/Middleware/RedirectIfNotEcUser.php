<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotEcUser
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('ec_user')->check()) {
            return redirect()->route('ec.user.login');
        }

        $user = Auth::guard('ec_user')->user();
        if (!$user->status) {
            Auth::guard('ec_user')->logout();

            return redirect()->route('ec.user.login')->withNotify([['error', 'Your account is disabled.']]);
        }

        return $next($request);
    }
}
