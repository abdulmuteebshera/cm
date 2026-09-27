<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoTracker;
use Illuminate\Http\Request;

class SeoBeaconController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->filled('action') && $request->getContent()) {
            $payload = json_decode($request->getContent(), true);
            if (is_array($payload)) {
                $request->merge($payload);
            }
        }

        try {
            SeoTracker::recordBeacon($request);
        } catch (\Throwable $e) {
            //
        }

        return response()->noContent();
    }
}
