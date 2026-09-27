<?php

namespace App\Support\Seo;

use App\Models\SeoEvent;
use App\Models\SeoPageView;
use App\Models\SeoVisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeoTracker
{
    public const COOKIE = '_cm_vid';
    public const SESSION_KEY = 'seo_sid';

    public static function ready(): bool
    {
        try {
            return Schema::hasTable('seo_visitor_sessions')
                && Schema::hasTable('seo_page_views')
                && Schema::hasTable('seo_events');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function shouldTrack(Request $request): bool
    {
        if (!self::ready()) {
            return false;
        }

        if ($request->isMethod('POST') && !$request->is('seo/beacon')) {
            return false;
        }

        if ($request->ajax() && !$request->is('seo/beacon')) {
            return false;
        }

        $path = ltrim($request->path(), '/');
        $skipPrefixes = [
            'admin', 'crm', 'emailcampaign', 'user', 'ticket', 'cron',
            'assets', 'core', 'vendor', 'storage', 'livewire',
        ];
        foreach ($skipPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        if (in_array($path, ['sitemap.xml', 'robots.txt', 'seo/beacon', 'favicon.ico'], true)) {
            return false;
        }

        if (preg_match('/\.(js|css|png|jpe?g|gif|svg|webp|woff2?|ttf|eot|map|ico)$/i', $path)) {
            return false;
        }

        if (SeoGeo::isBot($request->userAgent())) {
            return false;
        }

        return true;
    }

    public static function recordVisit(Request $request): ?SeoPageView
    {
        if (!self::shouldTrack($request)) {
            return null;
        }

        $visitorKey = self::visitorId($request);
        $session    = self::currentSession($request, $visitorKey);
        $path       = self::path($request);

        $view = SeoPageView::create([
            'seo_visitor_session_id' => $session->id,
            'visitor_key'            => $visitorKey,
            'url'                    => Str::limit($request->fullUrl(), 500, ''),
            'path'                   => Str::limit($path, 190, ''),
            'page_title'             => Str::limit((string) $request->attributes->get('pageTitle', ''), 190, ''),
            'referrer'               => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'query_string'           => Str::limit((string) $request->getQueryString(), 190, ''),
            'entered_at'             => now(),
            'duration_seconds'       => 0,
            'scroll_depth'           => 0,
        ]);

        $updates = [
            'last_seen_at' => now(),
            'page_count'   => (int) $session->page_count + 1,
            'is_bounce'    => ((int) $session->page_count + 1) <= 1,
        ];
        if (Schema::hasColumn('seo_visitor_sessions', 'exit_path')) {
            $updates['exit_path'] = Str::limit($path, 190, '');
        }
        $session->forceFill($updates)->save();

        return $view;
    }

    public static function recordBeacon(Request $request): void
    {
        if (!self::ready()) {
            return;
        }

        $action = $request->input('action');
        $viewId = (int) $request->input('view_id');
        $view   = $viewId ? SeoPageView::find($viewId) : null;
        $session = $view?->session;

        if (!$session) {
            $sid = $request->session()->get(self::SESSION_KEY);
            $session = $sid ? SeoVisitorSession::find($sid) : null;
        }

        if (!$session) {
            return;
        }

        $duration = max(0, min(86400, (int) $request->input('duration', 0)));
        $scroll   = max(0, min(100, (int) $request->input('scroll_depth', 0)));

        if ($view && in_array($action, ['heartbeat', 'leave', 'engage'], true)) {
            if ($duration > (int) $view->duration_seconds) {
                $view->duration_seconds = $duration;
            }
            if ($scroll > (int) $view->scroll_depth) {
                $view->scroll_depth = $scroll;
            }
            if ($action === 'leave') {
                $view->left_at = now();
            }
            $view->save();
        }

        $session->last_seen_at = now();
        if ($duration && $view) {
            $engaged = (int) SeoPageView::where('seo_visitor_session_id', $session->id)->sum('duration_seconds');
            if (Schema::hasColumn('seo_visitor_sessions', 'engaged_seconds')) {
                $session->engaged_seconds = $engaged;
            }
            $first = $session->started_at ?: $session->created_at;
            $session->duration_seconds = max(0, now()->diffInSeconds($first));
            $session->is_bounce = $session->page_count <= 1;
        }
        $session->save();

        if ($action === 'click') {
            $payload = [
                'seo_visitor_session_id' => $session->id,
                'seo_page_view_id'       => $view?->id,
                'visitor_key'            => $session->visitor_key,
                'event_type'             => 'click',
                'label'                  => Str::limit((string) $request->input('label'), 190, ''),
                'href'                   => Str::limit((string) $request->input('href'), 500, ''),
                'path'                   => Str::limit((string) ($view->path ?? $request->input('path')), 190, ''),
            ];
            if (Schema::hasColumn('seo_events', 'meta')) {
                $payload['meta'] = [
                    'tag'  => $request->input('tag'),
                    'x'    => (int) $request->input('x'),
                    'y'    => (int) $request->input('y'),
                    'text' => Str::limit((string) $request->input('text'), 190, ''),
                ];
            }
            if (Schema::hasColumn('seo_events', 'occurred_at')) {
                $payload['occurred_at'] = now();
            }
            SeoEvent::create($payload);
            if (Schema::hasColumn('seo_visitor_sessions', 'clicks')) {
                $session->increment('clicks');
            }
        }
    }

    protected static function visitorId(Request $request): string
    {
        $existing = $request->cookie(self::COOKIE);
        if ($existing && preg_match('/^[a-f0-9]{16,64}$/', $existing)) {
            return $existing;
        }

        $id = bin2hex(random_bytes(16));
        Cookie::queue(cookie(self::COOKIE, $id, 60 * 24 * 400, '/', null, $request->secure(), false, false, 'Lax'));
        return $id;
    }

    protected static function currentSession(Request $request, string $visitorKey): SeoVisitorSession
    {
        $sid = $request->session()->get(self::SESSION_KEY);
        $session = $sid ? SeoVisitorSession::find($sid) : null;

        $idleLimit = now()->subMinutes(30);
        if ($session && $session->visitor_key === $visitorKey && $session->last_seen_at && $session->last_seen_at->gte($idleLimit)) {
            return $session;
        }

        $ip = getRealIP();
        $geo = SeoGeo::lookup($ip);
        $device = SeoGeo::device((string) $request->userAgent());
        $utm = [
            'utm_source'   => $request->query('utm_source'),
            'utm_medium'   => $request->query('utm_medium'),
            'utm_campaign' => $request->query('utm_campaign'),
            'utm_term'     => $request->query('utm_term'),
            'utm_content'  => $request->query('utm_content'),
        ];
        $source = SeoGeo::classifySource($request->headers->get('referer'), $utm);
        $path = self::path($request);

        $data = [
            'visitor_key'      => $visitorKey,
            'session_key'      => bin2hex(random_bytes(8)),
            'user_id'          => auth()->id(),
            'ip_address'       => $ip,
            'country'          => $geo['country'],
            'country_code'     => $geo['country_code'],
            'region'           => $geo['region'],
            'city'             => $geo['city'],
            'latitude'         => $geo['lat'],
            'longitude'        => $geo['lng'],
            'device_type'      => $device['device_type'],
            'browser'          => $device['browser'],
            'os'               => $device['os'],
            'user_agent'       => Str::limit((string) $request->userAgent(), 500, ''),
            'source'           => $source['source'],
            'utm_source'       => $utm['utm_source'] ?: $source['source'],
            'utm_medium'       => $utm['utm_medium'] ?: $source['medium'],
            'utm_campaign'     => $utm['utm_campaign'] ?: $source['campaign'],
            'utm_term'         => $utm['utm_term'] ?? null,
            'utm_content'      => $utm['utm_content'] ?? null,
            'referrer'         => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'referrer_host'    => $source['referrer_host'],
            'landing_path'     => Str::limit($path, 190, ''),
            'landing_url'      => Str::limit($request->fullUrl(), 500, ''),
            'page_count'       => 0,
            'duration_seconds' => 0,
            'is_bounce'        => 1,
            'started_at'       => now(),
            'last_seen_at'     => now(),
        ];

        if (Schema::hasColumn('seo_visitor_sessions', 'channel')) {
            $data['channel'] = $source['channel'];
        }
        if (Schema::hasColumn('seo_visitor_sessions', 'clicks')) {
            $data['clicks'] = 0;
        }
        if (Schema::hasColumn('seo_visitor_sessions', 'exit_path')) {
            $data['exit_path'] = Str::limit($path, 190, '');
        }
        if (Schema::hasColumn('seo_visitor_sessions', 'engaged_seconds')) {
            $data['engaged_seconds'] = 0;
        }

        $session = SeoVisitorSession::create($data);
        $request->session()->put(self::SESSION_KEY, $session->id);
        return $session;
    }

    protected static function path(Request $request): string
    {
        $path = '/' . ltrim($request->path(), '/');
        return $path === '/.' ? '/' : $path;
    }
}
