<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MobileBridgeController extends Controller
{
    public function webSession(Request $request)
    {
        $redirect = $request->query('redirect', '/user/dashboard');
        if (!is_string($redirect) || !str_starts_with($redirect, '/')) {
            $redirect = '/user/dashboard';
        }

        $token = Str::random(64);
        Cache::put('mobile_web_auth:' . $token, auth()->id(), now()->addMinutes(2));

        $bridgePath = '/user/mobile-auth/' . $token . '?redirect=' . urlencode($redirect);

        return response()->json([
            'remark' => 'mobile_web_session',
            'status' => 'success',
            'message' => ['success' => ['Web session ready']],
            'data' => [
                'url' => url($bridgePath),
                'path' => $bridgePath,
            ],
        ]);
    }
}
