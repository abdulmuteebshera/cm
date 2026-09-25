<?php

namespace App\Http\Controllers\EmailCampaign\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function showLoginForm()
    {
        $pageTitle = 'Email Campaign — Sign in';

        return view('emailcampaign.auth.user_login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('ec_user')->attempt($request->only('username', 'password'), (bool) $request->remember)) {
            return back()->withInput($request->only('username'))->withNotify([['error', 'Invalid username or password.']]);
        }

        $user = Auth::guard('ec_user')->user();
        if (!$user->status) {
            Auth::guard('ec_user')->logout();

            return back()->withNotify([['error', 'Your account is disabled.']]);
        }

        $user->last_login_at = now();
        $user->save();

        EcActivityLog::record('user', $user->id, 'user.login', 'User signed in');

        return redirect()->route('ec.user.dashboard');
    }

    public function logout(Request $request)
    {
        $userId = Auth::guard('ec_user')->id();
        Auth::guard('ec_user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            EcActivityLog::record('user', $userId, 'user.logout', 'User signed out');
        }

        return redirect()->route('ec.user.login')->withNotify([['success', 'Signed out.']]);
    }
}
