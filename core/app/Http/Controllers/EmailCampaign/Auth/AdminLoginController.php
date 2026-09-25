<?php

namespace App\Http\Controllers\EmailCampaign\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        $pageTitle = 'Email Campaign — Admin';

        return view('emailcampaign.auth.admin_login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (!Auth::guard('ec_admin')->attempt($credentials, (bool) $request->remember)) {
            return back()->withInput($request->only('username'))->withNotify([['error', 'Invalid username or password.']]);
        }

        $admin = Auth::guard('ec_admin')->user();
        if (!$admin->status) {
            Auth::guard('ec_admin')->logout();

            return back()->withNotify([['error', 'Admin account is disabled.']]);
        }

        EcActivityLog::record('admin', $admin->id, 'admin.login', 'Admin signed in');

        return redirect()->route('ec.admin.dashboard');
    }

    public function logout(Request $request)
    {
        $adminId = Auth::guard('ec_admin')->id();
        Auth::guard('ec_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($adminId) {
            EcActivityLog::record('admin', $adminId, 'admin.logout', 'Admin signed out');
        }

        return redirect()->route('ec.admin.login')->withNotify([['success', 'Signed out.']]);
    }
}
