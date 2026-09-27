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

        $viewPayload = [
            SeoSchema::sessionFk() => $session->id,
            SeoSchema::visitor('seo_page_views') => $visitorKey,
            'url'              => Str::limit($request->fullUrl(), 500, ''),
            'path'             => Str::limit($path, 190, ''),
            SeoSchema::pageTitle() => Str::limit((string) $request->attributes->get('pageTitle', ''), 190, ''),
            'referrer'         => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'query_string'     => Str::limit((string) $request->getQueryString(), 190, ''),
            'entered_at'       => now(),
            'duration_seconds' => 0,
            'scroll_depth'     => 0,
        ];

        $view = SeoPageView::create(SeoSchema::only('seo_page_views', $viewPayload));

        $countCol = SeoSchema::pageCount();
        $current  = (int) ($session->getAttribute($countCol) ?? 0);
        $updates  = [
            SeoSchema::lastSeen() => now(),
            $countCol             => $current + 1,
        ];
        if (SeoSchema::has('seo_visitor_sessions', 'is_bounce')) {
            $updates['is_bounce'] = ($current + 1) <= 1;
        }
        $exitCol = SeoSchema::exitPage();
        if (SeoSchema::has('seo_visitor_sessions', $exitCol)) {
            $updates[$exitCol] = Str::limit($path, 190, '');
        }

        $session->forceFill(SeoSchema::only('seo_visitor_sessions', $updates))->save();

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
            if ($action === 'leave' && SeoSchema::has('seo_page_views', SeoSchema::leftAt())) {
                $view->setAttribute(SeoSchema::leftAt(), now());
            }
            $view->save();
        }

        $session->setAttribute(SeoSchema::lastSeen(), now());
        if ($duration && $view) {
            $engaged = (int) SeoPageView::where(SeoSchema::sessionFk(), $session->id)->sum('duration_seconds');
            if (SeoSchema::has('seo_visitor_sessions', 'engaged_seconds')) {
                $session->engaged_seconds = $engaged;
            }
            $first = $session->getAttribute(SeoSchema::sessionStart()) ?: $session->created_at;
            $session->duration_seconds = max(0, now()->diffInSeconds($first));
            if (SeoSchema::has('seo_visitor_sessions', 'is_bounce')) {
                $session->is_bounce = (int) $session->getAttribute(SeoSchema::pageCount()) <= 1;
            }
        }
        $session->save();

        if ($action === 'click') {
            $payload = SeoSchema::only('seo_events', [
                SeoSchema::sessionFk('seo_events') => $session->id,
                SeoSchema::pageViewFk()            => $view?->id,
                SeoSchema::visitor('seo_events')   => $session->getAttribute(SeoSchema::visitor()) ?: $session->getAttribute(SeoSchema::visitor('seo_page_views')),
                'event_type'                       => 'click',
                'label'                            => Str::limit((string) $request->input('label'), 190, ''),
                SeoSchema::href()                  => Str::limit((string) $request->input('href'), 500, ''),
                'path'                             => Str::limit((string) ($view->path ?? $request->input('path')), 190, ''),
                'occurred_at'                      => now(),
                'meta'                             => [
                    'tag'  => $request->input('tag'),
                    'x'    => (int) $request->input('x'),
                    'y'    => (int) $request->input('y'),
                    'text' => Str::limit((string) $request->input('text'), 190, ''),
                ],
            ]);
            SeoEvent::create($payload);
            if (SeoSchema::has('seo_visitor_sessions', 'clicks')) {
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
        $visitorCol = SeoSchema::visitor();
        $lastSeen = $session?->getAttribute(SeoSchema::lastSeen());
        if ($session && $session->getAttribute($visitorCol) === $visitorKey && $lastSeen) {
            $lastSeenAt = $lastSeen instanceof \Carbon\CarbonInterface ? $lastSeen : \Carbon\Carbon::parse($lastSeen);
            if ($lastSeenAt->gte($idleLimit)) {
                return $session;
            }
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

        $data = SeoSchema::only('seo_visitor_sessions', [
            $visitorCol                => $visitorKey,
            'session_key'              => bin2hex(random_bytes(8)),
            'user_id'                  => auth()->id(),
            SeoSchema::ip()            => $ip,
            'country'                  => $geo['country'],
            'country_code'             => $geo['country_code'],
            'region'                   => $geo['region'],
            'city'                     => $geo['city'],
            'latitude'                 => $geo['lat'],
            'longitude'                => $geo['lng'],
            'lat'                      => $geo['lat'],
            'lng'                      => $geo['lng'],
            'device_type'              => $device['device_type'],
            'browser'                  => $device['browser'],
            'os'                       => $device['os'],
            'user_agent'               => Str::limit((string) $request->userAgent(), 500, ''),
            'channel'                  => $source['channel'],
            'source'                   => $source['source'],
            'medium'                   => $utm['utm_medium'] ?: $source['medium'],
            'campaign'                 => $utm['utm_campaign'] ?: $source['campaign'],
            'utm_source'               => $utm['utm_source'] ?: $source['source'],
            'utm_medium'               => $utm['utm_medium'] ?: $source['medium'],
            'utm_campaign'             => $utm['utm_campaign'] ?: $source['campaign'],
            'utm_term'                 => $utm['utm_term'] ?? null,
            'utm_content'              => $utm['utm_content'] ?? null,
            'referrer'                 => Str::limit((string) $request->headers->get('referer'), 500, ''),
            'referrer_host'            => $source['referrer_host'],
            SeoSchema::landing()       => Str::limit($path, 190, ''),
            'landing_url'              => Str::limit($request->fullUrl(), 500, ''),
            SeoSchema::pageCount()     => 0,
            'clicks'                   => 0,
            'duration_seconds'         => 0,
            'engaged_seconds'          => 0,
            'is_bounce'                => 1,
            SeoSchema::sessionStart()  => now(),
            SeoSchema::lastSeen()      => now(),
            SeoSchema::exitPage()      => Str::limit($path, 190, ''),
        ]);

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
