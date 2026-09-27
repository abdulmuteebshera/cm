<?php

namespace App\Http\Middleware;

use App\Support\Seo\SeoSite;
use Closure;
use Illuminate\Http\Request;

class CanonicalDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = strtolower((string) $request->getHost());

        if (SeoSite::shouldCanonicalize($host)) {
            $target = 'https://' . SeoSite::PRIMARY_HOST . $request->getRequestUri();
            return redirect()->to($target, 301);
        }

        return $next($request);
    }
}
