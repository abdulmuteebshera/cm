<?php

namespace App\Http\Controllers\Crm\Auth;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected function resolvePortal(Request $request, ?string $portal = null): string
    {
        $portal = $portal
            ?: $request->route('portal')
            ?: ($request->route()->defaults['portal'] ?? null)
            ?: 'crm';

        return in_array($portal, ['crm', 'manager', 'agent', 'trader', 'finance'], true) ? $portal : 'crm';
    }

    public function showLoginForm(Request $request, string $portal = 'crm')
    {
        $portal = $this->resolvePortal($request, $portal);

        $titles = [
            'crm'     => 'CRM Super Admin Login',
            'manager' => 'Manager Portal Login',
            'agent'   => 'Investment Officer Login',
            'trader'  => 'Trading Desk Login',
            'finance' => 'Finance Portal Login',
        ];

        $pageTitle = $titles[$portal] ?? 'CRM Login';
        $routeName = match ($portal) {
            'manager' => 'crm.manager.login',
            'agent'   => 'crm.agent.login',
            'trader'  => 'crm.trader.login',
            'finance' => 'crm.finance.login',
            default   => 'crm.login',
        };

        return view('crm.auth.login', compact('pageTitle', 'portal', 'routeName'));
    }

    public function login(Request $request, string $portal = 'crm')
    {
        $portal = $this->resolvePortal($request, $portal);

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = (bool) $request->remember;

        if (!Auth::guard('crm')->attempt($credentials, $remember)) {
            $notify[] = ['error', 'Invalid email or password.'];
            return back()->withInput($request->only('email'))->withNotify($notify);
        }

        /** @var CrmStaff $staff */
        $staff = Auth::guard('crm')->user();

        if (!$staff->status) {
            Auth::guard('crm')->logout();
            $notify[] = ['error', 'Your account is disabled. Contact the CRM administrator.'];
            return back()->withNotify($notify);
        }

        if (!$staff->canAccessPortal($portal)) {
            Auth::guard('crm')->logout();
            $notify[] = ['error', 'This login portal is not assigned to your role. Use your designated portal URL.'];
            return back()->withNotify($notify);
        }

        $staff->last_login_at = now();
        $staff->save();

        $request->session()->regenerate();

        return to_route($staff->homeRoute());
    }

    public function logout(Request $request)
    {
        $staff = Auth::guard('crm')->user();
        $login = $staff ? $staff->loginRoute() : 'crm.login';

        Auth::guard('crm')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route($login);
    }
}
