<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class MarketNewsService
{
    protected int $cacheMinutes = 20;

    public const NEWS_PER_PAGE = 10;

    public const NEWS_MAX_PAGES = 10;

    public const NEWS_POOL_SIZE = 100;

    /** @var array<int, array<string, string>> */
    protected array $feeds = [
        ['url' => 'https://feeds.content.dowjones.io/public/rss/mw_topstories', 'source' => 'MarketWatch'],
        ['url' => 'https://feeds.content.dowjones.io/public/rss/mw_realtimeheadlines', 'source' => 'MarketWatch'],
        ['url' => 'https://finance.yahoo.com/news/rssindex', 'source' => 'Yahoo Finance'],
        ['url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?partnerId=wrss01&id=10000664', 'source' => 'CNBC Markets'],
        ['url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?partnerId=wrss01&id=10000958', 'source' => 'CNBC Investing'],
        ['url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?partnerId=wrss01&id=20910258', 'source' => 'CNBC Economy'],
        ['url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?partnerId=wrss01&id=100003114', 'source' => 'CNBC'],
        ['url' => 'https://feeds.a.dj.com/rss/RSSMarketsMain.xml', 'source' => 'WSJ Markets'],
        ['url' => 'https://feeds.bbci.co.uk/news/business/rss.xml', 'source' => 'BBC Business'],
        ['url' => 'https://feeds.npr.org/1007/rss.xml', 'source' => 'NPR Business'],
    ];

    /** @var array<int, array<string, string>> */
    protected array $indexSymbols = [
        ['symbol' => '^GSPC', 'label' => 'S&P 500'],
        ['symbol' => '^IXIC', 'label' => 'NASDAQ'],
        ['symbol' => '^DJI', 'label' => 'Dow Jones'],
        ['symbol' => '^FTSE', 'label' => 'FTSE 100'],
        ['symbol' => '^N225', 'label' => 'Nikkei 225'],
        ['symbol' => 'DFMGI.DU', 'label' => 'DFM Index'],
        ['symbol' => 'FADGI.AD', 'label' => 'ADX Index'],
    ];

    public function marketIndices(): array
    {
        return Cache::remember('cm_market_indices', now()->addMinutes(10), function () {
            $indices = [];

            foreach ($this->indexSymbols as $item) {
                $quote = $this->fetchYahooQuote($item['symbol']);
                if (!$quote) {
                    continue;
                }

                $indices[] = array_merge($item, $quote);
            }

            return $indices;
        });
    }

    public function globalNews(int $limit = self::NEWS_POOL_SIZE): array
    {
        $limit = max(1, min(self::NEWS_POOL_SIZE, $limit));

        return Cache::remember('cm_stock_market_news_pool_v2', now()->addMinutes($this->cacheMinutes), function () use ($limit) {
            $items = [];

            foreach ($this->feeds as $feed) {
                $items = array_merge($items, $this->fetchRss($feed['url'], $feed['source']));
            }

            $items = array_values(array_filter($items, fn ($item) => $this->isStockMarketNews($item)));
            $items = $this->uniqueNews($items);
            usort($items, fn ($a, $b) => ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0));

            if (count($items) < $limit) {
                $items = array_merge($items, $this->fallbackNews($limit - count($items)));
            }

            return array_slice($items, 0, $limit);
        });
    }

    /**
     * @return array{items: array, currentPage: int, totalPages: int, totalItems: int}
     */
    public function paginatedNews(int $page): array
    {
        $page = max(1, min(self::NEWS_MAX_PAGES, $page));
        $all = $this->globalNews(self::NEWS_POOL_SIZE);
        $totalPages = min(self::NEWS_MAX_PAGES, max(1, (int) ceil(count($all) / self::NEWS_PER_PAGE)));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * self::NEWS_PER_PAGE;

        return [
            'items' => array_slice($all, $offset, self::NEWS_PER_PAGE),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => count($all),
        ];
    }

    /** @deprecated Use globalNews() */
    public function combinedNews(int $limit = 12): array
    {
        return $this->globalNews($limit);
    }

    /** @deprecated Use globalNews() */
    public function newsByRegion(string $region, int $limit = 8): array
    {
        return $this->globalNews($limit);
    }

    protected function fetchYahooQuote(string $symbol): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; CrownmaireCapital/1.0)'])
                ->get('https://query1.finance.yahoo.com/v8/finance/chart/' . rawurlencode($symbol), [
                    'interval' => '1d',
                    'range' => '5d',
                ]);

            if (!$response->successful()) {
                return null;
            }

            $meta = data_get($response->json(), 'chart.result.0.meta');
            if (!$meta) {
                return null;
            }

            $price = (float) ($meta['regularMarketPrice'] ?? 0);
            $prev = (float) ($meta['chartPreviousClose'] ?? $meta['previousClose'] ?? $price);
            $change = $price - $prev;
            $changePct = $prev > 0 ? ($change / $prev) * 100 : 0;

            return [
                'price' => round($price, 2),
                'change' => round($change, 2),
                'change_pct' => round($changePct, 2),
                'direction' => $change >= 0 ? 'up' : 'down',
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fetchRss(string $url, string $source): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; CrownmaireCapital/1.0)'])
                ->get($url);

            if (!$response->successful()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body(), SimpleXMLElement::class, LIBXML_NOCDATA);
            if (!$xml) {
                return [];
            }

            $items = [];
            $nodes = $xml->channel->item ?? $xml->entry ?? [];

            foreach ($nodes as $node) {
                $title = trim((string) ($node->title ?? ''));
                $link = trim((string) ($node->link ?? $node->guid ?? ''));
                if ($link instanceof SimpleXMLElement) {
                    $link = trim((string) ($link['href'] ?? $link));
                }

                if ($title === '' || $link === '') {
                    continue;
                }

                $description = trim(strip_tags((string) ($node->description ?? $node->summary ?? '')));
                $description = preg_replace('/\s+/', ' ', $description ?? '') ?? '';
                $pubDate = (string) ($node->pubDate ?? $node->published ?? $node->updated ?? '');
                $timestamp = $pubDate ? strtotime($pubDate) : time();

                $items[] = [
                    'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    'url' => $link,
                    'excerpt' => mb_strimwidth($description, 0, 200, '…'),
                    'source' => $source,
                    'published' => $timestamp ? date('M d, Y', $timestamp) : '',
                    'timestamp' => $timestamp ?: time(),
                ];
            }

            return $items;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    protected function uniqueNews(array $items): array
    {
        $seen = [];
        $unique = [];

        foreach ($items as $item) {
            $key = md5(strtolower($item['title'] ?? ''));
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $item;
        }

        return $unique;
    }

    protected function isStockMarketNews(array $item): bool
    {
        $title = strtolower($item['title'] ?? '');
        $excerpt = strtolower($item['excerpt'] ?? '');
        $text = $title . ' ' . $excerpt;

        $exclude = [
            'form 4', 'form 144', 'form 3', 'soccer', 'football', 'migrants', 'abduction',
            'celebrity', 'hollywood', 'wedding', 'movie premiere', 'tv series', 'nba ',
            'nfl ', 'mlb ', 'world cup', 'tennis', 'golf tournament', 'fashion week',
            'insider trading news/kevin tang buys',
        ];

        foreach ($exclude as $pattern) {
            if (str_contains($text, $pattern)) {
                return false;
            }
        }

        if (preg_match('/^form [0-9]/i', $item['title'] ?? '')) {
            return false;
        }

        $keywords = [
            'stock', 'stocks', 'equity', 'equities', 'share', 'shares', 'market', 'markets',
            'nasdaq', 's&p', 's&p 500', 'sp 500', 'dow jones', 'dow', 'wall street', 'russell',
            'ftse', 'nikkei', 'index', 'indices', 'benchmark', 'futures', 'trading', 'trader',
            'investor', 'investing', 'investment', 'portfolio', 'etf', 'mutual fund', 'ipo',
            'earnings', 'revenue', 'profit', 'dividend', 'guidance', 'analyst', 'valuation',
            'market cap', 'bull market', 'bear market', 'rally', 'selloff', 'correction',
            'fed ', 'federal reserve', 'interest rate', 'rate cut', 'rate hike', 'inflation',
            'treasury', 'bond', 'yield', 'gdp', 'economy', 'economic', 'recession',
            'central bank', 'monetary', 'fiscal', 'sector', 'financial', 'finance', 'bank',
            'banking', 'asset', 'capital', 'hedge fund', 'private equity', 'merger',
            'acquisition', 'takeover', 'buyout', 'commodity', 'commodities', 'oil price',
            'crude oil', 'gold price', 'forex', 'currency', 'dollar', 'euro', 'yen',
            'bitcoin', 'crypto', 'volatility', 'vix', 'options', 'short selling',
            'wall st', 'bourse', 'exchange', 'nyse', 'sec ', 'quarterly results',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        $financeSources = ['MarketWatch', 'WSJ Markets', 'CNBC Markets', 'CNBC Investing', 'CNBC Economy', 'Yahoo Finance'];

        return in_array($item['source'] ?? '', $financeSources, true);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fallbackNews(int $limit): array
    {
        $pool = [
            ['title' => 'S&P 500 and NASDAQ futures edge higher ahead of economic data releases', 'excerpt' => 'US equity index futures point to a positive open as investors await inflation prints and Fed commentary.'],
            ['title' => 'Wall Street stocks mixed as tech leads NASDAQ while Dow lags', 'excerpt' => 'Sector rotation continues across large-cap indices with growth names outperforming value on earnings momentum.'],
            ['title' => 'Global equities react to central bank policy and inflation data', 'excerpt' => 'International stock markets assess rate expectations and cross-border capital flows affecting index performance.'],
            ['title' => 'Treasury yields move after jobs report, impacting equity valuations', 'excerpt' => 'Fixed income markets drive discount-rate assumptions for S&P 500 and NASDAQ multiples.'],
            ['title' => 'Technology and financial stocks drive index sentiment this week', 'excerpt' => 'Earnings season updates continue to shape risk appetite across US equity benchmarks.'],
            ['title' => 'Oil and commodity prices influence energy sector stocks on major indices', 'excerpt' => 'Energy names contribute to S&P 500 performance as crude markets react to supply data.'],
            ['title' => 'Corporate earnings beat expectations across key S&P 500 sectors', 'excerpt' => 'Analysts revise forward guidance as revenue growth supports equity valuations on Wall Street.'],
            ['title' => 'Federal Reserve signals data-dependent approach to interest rate decisions', 'excerpt' => 'Policy outlook remains central to stock market positioning across US and global indices.'],
            ['title' => 'Asian and European stock markets track overnight moves in US futures', 'excerpt' => 'Cross-market correlation affects global equity benchmarks including FTSE and Nikkei.'],
            ['title' => 'Gold and safe-haven flows rise as equity volatility picks up', 'excerpt' => 'Investors reposition across stocks, bonds, and alternatives amid macro uncertainty.'],
        ];

        $items = [];

        for ($i = 0; $i < max(0, $limit); $i++) {
            $row = $pool[$i % count($pool)];
            $items[] = [
                'title' => $row['title'],
                'url' => route('markets'),
                'excerpt' => $row['excerpt'],
                'source' => 'Market Brief',
                'published' => date('M d, Y', time() - ($i * 7200)),
                'timestamp' => time() - ($i * 7200),
                'is_fallback' => true,
            ];
        }

        return $items;
    }
}
