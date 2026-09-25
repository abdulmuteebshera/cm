<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfEcUser
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('ec_user')->check()) {
            return redirect()->route('ec.user.dashboard');
        }

        return $next($request);
    }
}
