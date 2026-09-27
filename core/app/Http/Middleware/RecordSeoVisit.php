<?php

namespace App\Http\Middleware;

use App\Support\Seo\SeoTracker;
use Closure;
use Illuminate\Http\Request;

class RecordSeoVisit
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && SeoTracker::shouldTrack($request)) {
            try {
                $view = SeoTracker::recordVisit($request);
                if ($view) {
                    $request->attributes->set('seo_view_id', $view->id);
                    view()->share('seoViewId', $view->id);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $next($request);
    }
}
