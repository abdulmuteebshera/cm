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
        $offset = max(0, (int) $settings->search_cursor);
        $added = [];
        $already = 0;
        $pages = 0;

        while (count($added) < $limit && $pages < 3) {
            $rows = $this->wikidataPage($offset);
            $pages++;
            $offset += 30;
            if ($rows === []) {
                break;
            }
            foreach ($rows as $result) {
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

        $settings->search_cursor = $offset;
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
            'company' => mb_substr($result['company'] !== '' ? $result['company'] : 'Company', 0, 160),
            'title' => mb_substr($result['title'], 0, 160) ?: null,
            'profile_url' => $url,
            'location' => mb_substr($result['location'], 0, 120) ?: null,
            'public_note' => mb_substr($result['note'], 0, 1000) ?: null,
        ]);
        $this->remember($url);

        return $name;
    }

    private function wikidataPage(int $offset): array
    {
        $sparql = <<<SPARQL
SELECT ?personLabel ?linkedin ?descr WHERE {
  ?person wdt:P106 wd:Q484876 .
  ?person wdt:P6634 ?linkedin .
  ?person rdfs:label ?personLabel . FILTER(LANG(?personLabel)="en")
  OPTIONAL { ?person schema:description ?descr . FILTER(LANG(?descr)="en") }
}
LIMIT 30 OFFSET {$offset}
SPARQL;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/sparql-results+json',
                'User-Agent' => 'CrownmairePortal/1.0 (public profile research)',
            ])->timeout(25)->get('https://query.wikidata.org/sparql', [
                'query' => $sparql,
            ]);
        } catch (\Throwable $exception) {
            throw new RuntimeException('The public search could not be reached. Try again in a moment.');
        }

        if (!$response->successful()) {
            throw new RuntimeException('The public search could not be reached. Try again in a moment.');
        }

        $found = [];
        foreach ($response->json()['results']['bindings'] ?? [] as $row) {
            $slug = $this->profileSlug((string) ($row['linkedin']['value'] ?? ''));
            $name = trim((string) ($row['personLabel']['value'] ?? ''));
            $note = trim((string) ($row['descr']['value'] ?? ''));
            if ($slug === '' || $name === '' || isset($found[$slug]) || !$this->usableDescription($note)) {
                continue;
            }
            $found[$slug] = [
                'url' => 'https://www.linkedin.com/in/'.$slug,
                'name' => $name,
                'title' => $this->titleFrom($note),
                'company' => $this->companyFrom($note),
                'location' => '',
                'note' => $note !== '' ? $note : 'Public chief executive profile.',
            ];
        }

        return array_values($found);
    }

    private function profileSlug(string $value): string
    {
        $value = trim(rawurldecode($value));
        if (preg_match('#linkedin\.com/in/([A-Za-z0-9\-_%]+)#i', $value, $match)) {
            $value = $match[1];
        }
        $value = trim($value, "/ \t\n\r");

        return preg_match('/^[A-Za-z0-9][A-Za-z0-9\-_%]{1,98}[A-Za-z0-9]$/', $value) ? $value : '';
    }

    private function usableDescription(string $note): bool
    {
        if (preg_match('/\b(died|deceased)\b|\(\d{4}\s*[–-]\s*\d{4}\)/u', $note)) {
            return false;
        }
        if ($note === '') {
            return true;
        }

        return preg_match('/\b(business|executive|founder|investor|billionaire|compan|chairman|chairwoman|entrepreneur|chief executive|ceo|owner|holding)\b/i', $note) === 1;
    }

    private function titleFrom(string $note): string
    {
        $founder = preg_match('/\bfounder\b/i', $note) === 1;
        $chairwoman = preg_match('/\bchairwoman\b/i', $note) === 1;
        $chairman = preg_match('/\bchairman\b/i', $note) === 1;
        if (preg_match('/\bfamily office\b/i', $note)) {
            return $founder ? 'Founder and family office chief executive' : 'Family office chief executive';
        }
        if ($founder && ($chairman || $chairwoman)) {
            return 'Founder and Chairman';
        }
        if ($founder) {
            return 'Founder and Chief Executive Officer';
        }
        if ($chairwoman) {
            return 'Chairwoman and Chief Executive Officer';
        }
        if ($chairman) {
            return 'Chairman and Chief Executive Officer';
        }

        return 'Chief Executive Officer';
    }

    private function companyFrom(string $note): string
    {
        if (preg_match('/\b(?:of|at)\s+([^.;]{2,80})/i', $note, $match)) {
            $company = trim($match[1]);
            $company = preg_replace('/\s+\(.*$/', '', $company) ?? $company;
            $company = preg_replace('/\s+since\s+\d{4}.*$/i', '', $company) ?? $company;

            return trim($company, " ,");
        }

        return '';
    }

    private function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        if ($name === '' || mb_strlen($name) < 3 || stripos($name, 'linkedin') !== false) {
            return '';
        }

        return $name;
    }
}
