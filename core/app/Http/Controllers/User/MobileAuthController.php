<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MobileAuthController extends Controller
{
    public function authenticate(Request $request, string $token)
    {
        $userId = Cache::pull('mobile_web_auth:' . $token);

        if (!$userId) {
            abort(403, 'Invalid or expired mobile session link.');
        }

        Auth::loginUsingId($userId);
        $request->session()->regenerate();

        $redirect = $request->query('redirect', '/user/dashboard');
        if (!is_string($redirect) || !str_starts_with($redirect, '/user/')) {
            $redirect = '/user/dashboard';
        }

        return redirect($redirect);
    }
}
