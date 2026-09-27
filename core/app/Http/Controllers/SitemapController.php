<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoCatalog;
use App\Support\Seo\SeoSite;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = SeoCatalog::sitemapEntries();
        $lastmod = now()->toAtomString();
        $body = '<' . '?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $body .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $body .= '    <url>' . "\n";
            $body .= '        <loc>' . e($url['loc']) . '</loc>' . "\n";
            $body .= '        <changefreq>' . e($url['changefreq']) . '</changefreq>' . "\n";
            $body .= '        <priority>' . e($url['priority']) . '</priority>' . "\n";
            $body .= '        <lastmod>' . e($lastmod) . '</lastmod>' . "\n";
            $body .= '    </url>' . "\n";
        }

        $body .= '</urlset>';

        return response($body, 200)
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
