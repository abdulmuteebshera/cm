<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotCrmStaff
{
    public function handle($request, Closure $next, $portal = null)
    {
        if (!Auth::guard('crm')->check()) {
            $login = match ($portal) {
                'manager' => 'crm.manager.login',
                'agent'   => 'crm.agent.login',
                'trader'  => 'crm.trader.login',
                'finance' => 'crm.finance.login',
                default   => 'crm.login',
            };

            return to_route($login);
        }

        $staff = Auth::guard('crm')->user();

        if (!$staff->status) {
            Auth::guard('crm')->logout();
            $notify[] = ['error', 'Your CRM account is disabled.'];
            return to_route('crm.login')->withNotify($notify);
        }

        if ($portal && !$staff->canAccessPortal($portal)) {
            $notify[] = ['error', 'You do not have access to this portal.'];
            return to_route($staff->homeRoute())->withNotify($notify);
        }

        return $next($request);
    }
}
