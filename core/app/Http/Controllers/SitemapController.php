<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoCatalog;
use App\Support\Seo\SeoSite;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = SeoCatalog::sitemapEntries();

        return response()
            ->view('seo.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $base = SeoSite::publicBase();
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /user',
            'Disallow: /user/',
            'Disallow: /crm',
            'Disallow: /crm/',
            'Disallow: /ticket',
            'Disallow: /ticket/',
            'Disallow: /emailcampaign',
            'Disallow: /emailcampaign/',
            'Disallow: /core/',
            '',
            'Sitemap: ' . $base . '/sitemap.xml',
        ];

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
