<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CrmPermission
{
    public function handle($request, Closure $next, string $permission)
    {
        $staff = Auth::guard('crm')->user();

        if (!$staff || !$staff->hasPermission($permission)) {
            $notify[] = ['error', 'You do not have permission to access this module.'];
            return back()->withNotify($notify);
        }

        return $next($request);
    }
}
