<?php

namespace App\Http\Middleware;

use App\Support\CrmAdminBridge;
use Closure;
use Illuminate\Http\Request;

class EnforceCrmAdminScope
{
    public function handle(Request $request, Closure $next)
    {
        $bridge = $request->session()->get('crm_admin_bridge');
        $routeName = optional($request->route())->getName();

        if ($routeName && !CrmAdminBridge::routeAllowed($bridge, $routeName)) {
            $notify[] = ['error', 'Your CRM role does not include this admin module.'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        return $next($request);
    }
}
