<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoEvent;
use App\Models\SeoPageView;
use App\Models\SeoVisitorSession;
use App\Support\Seo\SeoCatalog;
use App\Support\Seo\SeoSchema;
use App\Support\Seo\SeoSite;
use App\Support\Seo\SeoTracker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeoAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'SEO Analytics';

        $startCol   = SeoSchema::sessionStart();
        $visitorCol = SeoSchema::visitor();
        $countCol   = SeoSchema::pageCount();

        $sessions = SeoVisitorSession::where($startCol, '>=', $from);
        $views    = SeoPageView::where('entered_at', '>=', $from);
        $events   = SeoEvent::where(SeoSchema::eventTime(), '>=', $from);

        $sessionCount = (clone $sessions)->count();
        $visitorCount = (clone $sessions)->distinct($visitorCol)->count($visitorCol);
        $pageViews    = (clone $views)->count();
        $clickCount   = (clone $events)->where('event_type', 'click')->count();
        $avgEngage    = (int) round((clone $sessions)->avg(SeoSchema::engage()) ?: 0);
        $bounces      = (clone $sessions)->where($countCol, '<=', 1)->count();
        $bounceRate   = $sessionCount ? round(($bounces / $sessionCount) * 100, 1) : 0;

        $daily = SeoVisitorSession::select(
            DB::raw('DATE(' . $startCol . ') as day'),
            DB::raw('COUNT(*) as sessions'),
            DB::raw('COUNT(DISTINCT ' . $visitorCol . ') as visitors')
        )
            ->where($startCol, '>=', $from)
            ->groupBy(DB::raw('DATE(' . $startCol . ')'))
            ->orderBy(DB::raw('DATE(' . $startCol . ')'))
            ->get();

        $topPages = SeoPageView::select('path', DB::raw('COUNT(*) as views'), DB::raw('AVG(duration_seconds) as avg_time'))
            ->where('entered_at', '>=', $from)
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        $topCountries = SeoVisitorSession::select('country', DB::raw('COUNT(*) as total'))
            ->where($startCol, '>=', $from)
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $devices = SeoVisitorSession::select('device_type', DB::raw('COUNT(*) as total'))
            ->where($startCol, '>=', $from)
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        $sources = SeoVisitorSession::select(SeoSchema::channel() . ' as channel', DB::raw('COUNT(*) as total'))
            ->where($startCol, '>=', $from)
            ->groupBy(SeoSchema::channel())
            ->orderByDesc('total')
            ->get();

        $recent = SeoVisitorSession::orderByDesc(SeoSchema::lastSeen())->limit(10)->get();

        $widget = [
            'visitors'    => $visitorCount,
            'sessions'    => $sessionCount,
            'page_views'  => $pageViews,
            'clicks'      => $clickCount,
            'avg_time'    => SeoVisitorSession::formatSeconds($avgEngage),
            'bounce_rate' => $bounceRate . '%',
        ];

        return view('admin.seo_analytics.index', compact(
            'pageTitle', 'range', 'widget', 'daily', 'topPages', 'topCountries',
            'devices', 'sources', 'recent'
        ));
    }

    public function visitors(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'SEO Visitors';

        $query = SeoVisitorSession::where(SeoSchema::sessionStart(), '>=', $from)->orderByDesc(SeoSchema::lastSeen());

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('device')) {
            $query->where('device_type', $request->device);
        }
        if ($request->filled('channel')) {
            $query->where(SeoSchema::channel(), $request->channel);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($inner) use ($q) {
                $inner->where(SeoSchema::ip(), 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%")
                    ->orWhere(SeoSchema::landing(), 'like', "%{$q}%")
                    ->orWhere('source', 'like', "%{$q}%");
            });
        }

        $sessions = $query->paginate(getPaginate());

        return view('admin.seo_analytics.visitors', compact('pageTitle', 'sessions', 'range'));
    }

    public function session($id)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        $session = SeoVisitorSession::with(['pageViews' => function ($q) {
            $q->orderBy('entered_at');
        }, 'events' => function ($q) {
            $q->orderBy(SeoSchema::eventTime());
        }])->findOrFail($id);

        $pageTitle = 'Visitor session #' . $session->id;

        return view('admin.seo_analytics.session', compact('pageTitle', 'session'));
    }

    public function pages(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'Pages visited';

        $pages = SeoPageView::select(
            'path',
            DB::raw('COUNT(*) as views'),
            DB::raw('COUNT(DISTINCT ' . SeoSchema::visitor('seo_page_views') . ') as visitors'),
            DB::raw('AVG(duration_seconds) as avg_time'),
            DB::raw('AVG(scroll_depth) as avg_scroll'),
            DB::raw('MAX(entered_at) as last_visit')
        )
            ->where('entered_at', '>=', $from)
            ->groupBy('path')
            ->orderByDesc('views')
            ->paginate(getPaginate());

        return view('admin.seo_analytics.pages', compact('pageTitle', 'pages', 'range'));
    }

    public function locations(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'Visitor locations';

        $countries = SeoVisitorSession::select(
            'country',
            'country_code',
            DB::raw('COUNT(*) as sessions'),
            DB::raw('COUNT(DISTINCT ' . SeoSchema::visitor() . ') as visitors'),
            DB::raw('AVG(' . SeoSchema::engage() . ') as avg_time')
        )
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('country', 'country_code')
            ->orderByDesc('sessions')
            ->get();

        $cities = SeoVisitorSession::select(
            'city',
            'country',
            DB::raw('COUNT(*) as sessions'),
            DB::raw('COUNT(DISTINCT ' . SeoSchema::visitor() . ') as visitors')
        )
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('city', 'country')
            ->orderByDesc('sessions')
            ->paginate(getPaginate());

        return view('admin.seo_analytics.locations', compact('pageTitle', 'countries', 'cities', 'range'));
    }

    public function devices(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'Devices & browsers';

        $devices = SeoVisitorSession::select('device_type', DB::raw('COUNT(*) as total'))
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        $browsers = SeoVisitorSession::select('browser', DB::raw('COUNT(*) as total'))
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        $oses = SeoVisitorSession::select('os', DB::raw('COUNT(*) as total'))
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('os')
            ->orderByDesc('total')
            ->get();

        return view('admin.seo_analytics.devices', compact('pageTitle', 'devices', 'browsers', 'oses', 'range'));
    }

    public function sources(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'Traffic sources';

        $channelCol = SeoSchema::channel();
        $mediumCol = SeoSchema::medium();
        $campaignCol = SeoSchema::campaign();
        $channels = SeoVisitorSession::select($channelCol . ' as channel', DB::raw('COUNT(*) as total'))
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy($channelCol)
            ->orderByDesc('total')
            ->get();

        $sources = SeoVisitorSession::select(
            'source',
            $mediumCol . ' as medium',
            $channelCol . ' as channel',
            DB::raw('COUNT(*) as sessions'),
            DB::raw('COUNT(DISTINCT ' . SeoSchema::visitor() . ') as visitors')
        )
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->groupBy('source', $mediumCol, $channelCol)
            ->orderByDesc('sessions')
            ->paginate(getPaginate());

        $campaigns = SeoVisitorSession::select($campaignCol . ' as campaign', DB::raw('COUNT(*) as total'))
            ->where(SeoSchema::sessionStart(), '>=', $from)
            ->whereNotNull($campaignCol)
            ->where($campaignCol, '!=', '')
            ->groupBy($campaignCol)
            ->orderByDesc('total')
            ->get();

        return view('admin.seo_analytics.sources', compact('pageTitle', 'channels', 'sources', 'campaigns', 'range'));
    }

    public function clicks(Request $request)
    {
        if (!$this->ready()) {
            return $this->setupView();
        }

        [$from, $range] = $this->range($request);
        $pageTitle = 'Clicks & engagement';

        $eventTime = SeoSchema::eventTime();
        $hrefCol = SeoSchema::href();
        $topLinks = SeoEvent::select(
            $hrefCol . ' as target_url',
            'label',
            DB::raw('COUNT(*) as clicks')
        )
            ->where('event_type', 'click')
            ->where($eventTime, '>=', $from)
            ->groupBy($hrefCol, 'label')
            ->orderByDesc('clicks')
            ->limit(40)
            ->get();

        $recent = SeoEvent::where('event_type', 'click')
            ->where($eventTime, '>=', $from)
            ->orderByDesc($eventTime)
            ->paginate(getPaginate());

        return view('admin.seo_analytics.clicks', compact('pageTitle', 'topLinks', 'recent', 'range'));
    }

    public function results()
    {
        $pageTitle = 'SEO results';
        $checklist = SeoCatalog::auditChecklist();
        $pages     = [];

        foreach (SeoCatalog::pages() as $route => $meta) {
            if (!in_array($route, ['insights', 'blogs'], true)) {
                try {
                    $url = $route === 'careers.apply' ? route('careers') : route($route);
                } catch (\Throwable $e) {
                    $url = '#';
                }
                $pages[] = [
                    'route'       => $route,
                    'title'       => $meta['title'],
                    'description' => $meta['description'],
                    'url'         => $url,
                    'indexable'   => true,
                ];
            }
        }

        $stats = [
            'sitemap'   => url('/sitemap.xml'),
            'robots'    => url('/robots.txt'),
            'canonical' => 'https://' . SeoSite::PRIMARY_HOST,
            'alias'     => 'https://' . SeoSite::ALIAS_HOST,
            'ready'     => $this->ready(),
            'sessions'  => $this->ready() ? SeoVisitorSession::count() : 0,
            'views'     => $this->ready() ? SeoPageView::count() : 0,
        ];

        return view('admin.seo_analytics.results', compact('pageTitle', 'checklist', 'pages', 'stats'));
    }

    protected function range(Request $request): array
    {
        $range = $request->get('range', '30');
        if (!in_array($range, ['7', '30', '90', '365'], true)) {
            $range = '30';
        }

        return [Carbon::now()->subDays((int) $range)->startOfDay(), $range];
    }

    protected function ready(): bool
    {
        return SeoTracker::ready();
    }

    protected function setupView()
    {
        $pageTitle = 'SEO Analytics';
        return view('admin.seo_analytics.index', [
            'pageTitle' => $pageTitle,
            'range'     => '30',
            'setup'     => true,
        ]);
    }
}
