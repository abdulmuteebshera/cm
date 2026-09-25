<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfEcAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('ec_admin')->check()) {
            return redirect()->route('ec.admin.dashboard');
        }

        return $next($request);
    }
}
