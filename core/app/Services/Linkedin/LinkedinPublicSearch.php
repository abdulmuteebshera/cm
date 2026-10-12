<?php

namespace App\Services\Linkedin;

use App\Models\LinkedinProspect;
use App\Models\LinkedinSeenProfile;
use App\Models\LinkedinSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LinkedinPublicSearch
{
    public function searchMore(int $limit = 10): string
    {
        $settings = LinkedinSetting::current();
        $queries = $this->queries();
        $start = (int) $settings->search_cursor % count($queries);
        $added = [];
        $already = 0;
        $attempts = 0;

        while (count($added) < $limit && $attempts < 3) {
            $query = $queries[($start + $attempts) % count($queries)];
            $attempts++;
            foreach ($this->resultPages($query) as $result) {
                if (count($added) >= $limit) {
                    break;
                }
                $saved = $this->saveResult($result);
                if ($saved === null) {
                    continue;
                }
                if ($saved === true) {
                    $already++;
                    continue;
                }
                $added[] = $saved;
            }
        }

        $settings->search_cursor = $start + $attempts;
        $settings->save();

        if ($added !== []) {
            app(LinkedinOutreach::class)->reviewProspects();
        }

        if ($added === [] && $already > 0) {
            return 'No new public profiles in this search. Everyone found is already on the list.';
        }

        if ($added === []) {
            return 'The public search did not return any new profile links this time. Try Search more again.';
        }

        return 'Added '.count($added).' new profiles: '.implode(', ', $added).'. Welcome notes are waiting for approval.';
    }

    public function remember(string $url): void
    {
        try {
            $url = LinkedinProspect::normalizeProfileUrl($url);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        LinkedinSeenProfile::query()->firstOrCreate(['profile_url' => $url]);
    }

    private function saveResult(array $result): ?string
    {
        try {
            $url = LinkedinProspect::normalizeProfileUrl($result['url']);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        if (LinkedinSeenProfile::query()->where('profile_url', $url)->exists()
            || LinkedinProspect::query()->where('profile_url', $url)->exists()) {
            $this->remember($url);

            return true;
        }

        $name = $this->cleanName($result['name']);
        if ($name === '') {
            return null;
        }

        LinkedinProspect::query()->create([
            'name' => mb_substr($name, 0, 120),
            'company' => mb_substr($result['company'] !== '' ? $result['company'] : 'Family office', 0, 160),
            'title' => mb_substr($result['title'], 0, 160) ?: null,
            'profile_url' => $url,
            'location' => mb_substr($result['location'], 0, 120) ?: null,
            'public_note' => mb_substr($result['note'], 0, 1000) ?: null,
        ]);
        $this->remember($url);

        return $name;
    }

    private function resultPages(string $query): array
    {
        $found = [];
        foreach ([
            'https://html.duckduckgo.com/html/?q='.rawurlencode($query),
            'https://www.bing.com/search?q='.rawurlencode($query).'&count=20',
        ] as $page) {
            $html = $this->fetch($page);
            $found = array_merge($found, $this->parseDuckDuckGo($html), $this->parseBing($html));
            if ($found !== []) {
                return $found;
            }
        }

        return [];
    }

    private function fetch(string $url): string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept-Language' => 'en-US,en;q=0.9',
            ])->timeout(25)->get($url);
        } catch (\Throwable $exception) {
            throw new RuntimeException('The public search could not be reached. Try again in a moment.');
        }

        if (!$response->successful()) {
            return '';
        }

        return (string) $response->body();
    }

    private function parseDuckDuckGo(string $html): array
    {
        if ($html === '') {
            return [];
        }

        $found = [];
        if (!preg_match_all('#<a[^>]*class="result__a"[^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', $html, $links, PREG_SET_ORDER)) {
            return [];
        }

        preg_match_all('#<a[^>]*class="result__snippet"[^>]*>(.*?)</a>#is', $html, $snippets);
        $snippetList = $snippets[1] ?? [];

        foreach ($links as $index => $link) {
            $url = $this->unwrapUrl(html_entity_decode($link[1]));
            if (!preg_match('#linkedin\.com/in/#i', $url)) {
                continue;
            }
            $heading = $this->plain($link[2]);
            $snippet = $this->plain($snippetList[$index] ?? '');
            $parsed = $this->describe($heading, $snippet);
            $parsed['url'] = $url;
            $found[] = $parsed;
        }

        return $found;
    }

    private function parseBing(string $html): array
    {
        if ($html === '' || !preg_match_all('#<li class="b_algo".*?</li>#is', $html, $items)) {
            return [];
        }

        $found = [];
        foreach ($items[0] as $item) {
            if (!preg_match('#<h2[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>(.*?)</a>#is', $item, $link)) {
                continue;
            }
            $url = $this->decodeBingUrl(html_entity_decode($link[1]));
            if (!preg_match('#linkedin\.com/in/#i', $url)) {
                continue;
            }
            $snippet = '';
            if (preg_match('#<p[^>]*>(.*?)</p>#is', $item, $paragraph)) {
                $snippet = $this->plain($paragraph[1]);
            }
            $parsed = $this->describe($this->plain($link[2]), $snippet);
            $parsed['url'] = $url;
            $found[] = $parsed;
        }

        return $found;
    }

    private function decodeBingUrl(string $url): string
    {
        if (!preg_match('/[?&]u=a1([A-Za-z0-9_\-]+)/', $url, $match)) {
            return $url;
        }

        $token = strtr($match[1], '-_', '+/');
        $pad = strlen($token) % 4;
        if ($pad) {
            $token .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($token, true);

        return is_string($decoded) && $decoded !== '' ? $decoded : $url;
    }

    private function describe(string $heading, string $snippet): array
    {
        $heading = preg_replace('/\s*\|\s*LinkedIn\s*$/i', '', $heading) ?? $heading;
        $name = $heading;
        $role = '';
        if (str_contains($heading, ' - ')) {
            [$name, $role] = array_pad(explode(' - ', $heading, 2), 2, '');
        }

        $company = '';
        if (preg_match('/\bat\s+([^|.]{2,80})/i', $role.' '.$snippet, $match)) {
            $company = trim($match[1]);
        }
        if ($company === '' && preg_match('/\b(Family Office|Holdings|Holding|Group|Capital)\b/i', $role.' '.$snippet, $match)) {
            $company = trim($match[0]);
        }

        $location = '';
        if (preg_match('/\b(Dubai|New York|London|Singapore|Geneva|Zurich|Mumbai|Toronto|Abu Dhabi)\b/i', $snippet, $match)) {
            $location = $match[1];
        }

        return [
            'name' => trim($name),
            'title' => trim($role) !== '' ? trim($role) : 'Founder',
            'company' => $company,
            'location' => $location,
            'note' => $snippet !== '' ? $snippet : 'Found in a public web search for family offices and business owners.',
        ];
    }

    private function unwrapUrl(string $url): string
    {
        if (preg_match('/[?&]uddg=([^&]+)/', $url, $match)) {
            return urldecode($match[1]);
        }

        return $url;
    }

    private function plain(string $html): string
    {
        $text = html_entity_decode(strip_tags($html));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    private function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        if ($name === '' || mb_strlen($name) < 3 || stripos($name, 'linkedin') !== false) {
            return '';
        }

        return $name;
    }

    private function queries(): array
    {
        return [
            'site:linkedin.com/in family office founder CEO Dubai',
            'site:linkedin.com/in family office president "New York"',
            'site:linkedin.com/in single family office principal London',
            'site:linkedin.com/in holding company chairman founder Dubai',
            'site:linkedin.com/in multi family office founder Singapore',
            'site:linkedin.com/in family office chief investment officer',
            'site:linkedin.com/in "family office" founder Geneva',
            'site:linkedin.com/in business owner chairman "holding" Dubai',
        ];
    }
}
