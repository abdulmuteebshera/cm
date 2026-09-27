<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Schema;

class EnsureEcTables
{
    public function handle($request, Closure $next)
    {
        if (Schema::hasTable('ec_admins')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Email campaign database not installed. On the server run: cd core && php artisan ec:setup-live --seed',
            ], 503);
        }

        return response()->view('emailcampaign.setup_required', [], 503);
    }
}
