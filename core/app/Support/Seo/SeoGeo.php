<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Cache;

class SeoGeo
{
    public static function lookup(string $ip): array
    {
        $defaults = [
            'ip'         => $ip,
            'country'    => 'Unknown',
            'country_code' => '',
            'region'     => '',
            'city'       => 'Unknown',
            'lat'        => null,
            'lng'        => null,
        ];

        if (self::isPrivateIp($ip)) {
            $defaults['country'] = 'Local';
            $defaults['city'] = 'Local network';
            return $defaults;
        }

        return Cache::remember('seo_geo_' . md5($ip), now()->addDay(), function () use ($ip, $defaults) {
            try {
                $json = self::fetchJson('http://ip-api.com/json/' . urlencode($ip) . '?fields=status,country,countryCode,regionName,city,lat,lon', 1.2);
                if (!empty($json['status']) && $json['status'] === 'success') {
                    return [
                        'ip'           => $ip,
                        'country'      => $json['country'] ?? 'Unknown',
                        'country_code' => $json['countryCode'] ?? '',
                        'region'       => $json['regionName'] ?? '',
                        'city'         => $json['city'] ?? 'Unknown',
                        'lat'          => isset($json['lat']) ? (float) $json['lat'] : null,
                        'lng'          => isset($json['lon']) ? (float) $json['lon'] : null,
                    ];
                }
            } catch (\Throwable $e) {
                //
            }

            return $defaults;
        });
    }

    public static function device(string $ua): array
    {
        $uaLower = strtolower($ua);
        $type = 'desktop';
        if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) {
            $type = 'tablet';
        } elseif (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone|blackberry/i', $ua)) {
            $type = 'mobile';
        }

        $osBrowser = osBrowser();

        return [
            'device_type' => $type,
            'browser'     => $osBrowser['browser'] ?? 'Unknown',
            'os'          => $osBrowser['os_platform'] ?? 'Unknown',
            'is_mobile'   => $type !== 'desktop',
        ];
    }

    public static function classifySource(?string $referrer, array $utm = []): array
    {
        $utmSource = trim((string) ($utm['utm_source'] ?? ''));
        $utmMedium = trim((string) ($utm['utm_medium'] ?? ''));
        $utmCampaign = trim((string) ($utm['utm_campaign'] ?? ''));

        if ($utmSource !== '') {
            $medium = strtolower($utmMedium);
            $channel = 'campaign';
            if (in_array($medium, ['cpc', 'ppc', 'paid', 'paidsearch'], true)) {
                $channel = 'paid';
            } elseif (in_array($medium, ['email', 'newsletter'], true)) {
                $channel = 'email';
            } elseif (in_array($medium, ['social', 'social-media'], true)) {
                $channel = 'social';
            }
            return [
                'channel'      => $channel,
                'source'       => $utmSource,
                'medium'       => $utmMedium ?: $channel,
                'campaign'     => $utmCampaign,
                'referrer'     => $referrer,
                'referrer_host'=> self::host($referrer),
            ];
        }

        $host = self::host($referrer);
        if ($host === '') {
            return [
                'channel' => 'direct',
                'source'  => 'direct',
                'medium'  => 'none',
                'campaign'=> $utmCampaign,
                'referrer'=> $referrer,
                'referrer_host' => '',
            ];
        }

        $owned = SeoSite::ownedHosts();
        if (in_array($host, $owned, true)) {
            return [
                'channel' => 'internal',
                'source'  => $host,
                'medium'  => 'internal',
                'campaign'=> $utmCampaign,
                'referrer'=> $referrer,
                'referrer_host' => $host,
            ];
        }

        $organic = ['google', 'bing', 'yahoo', 'duckduckgo', 'yandex', 'baidu', 'ecosia'];
        foreach ($organic as $engine) {
            if (str_contains($host, $engine)) {
                return [
                    'channel' => 'organic',
                    'source'  => $engine,
                    'medium'  => 'organic',
                    'campaign'=> $utmCampaign,
                    'referrer'=> $referrer,
                    'referrer_host' => $host,
                ];
            }
        }

        $social = [
            'facebook' => 'facebook', 'fb.com' => 'facebook', 'instagram' => 'instagram',
            'linkedin' => 'linkedin', 'twitter' => 'twitter', 't.co' => 'twitter',
            'x.com' => 'twitter', 'youtube' => 'youtube', 'tiktok' => 'tiktok',
            'whatsapp' => 'whatsapp', 'telegram' => 'telegram',
        ];
        foreach ($social as $needle => $name) {
            if (str_contains($host, $needle)) {
                return [
                    'channel' => 'social',
                    'source'  => $name,
                    'medium'  => 'social',
                    'campaign'=> $utmCampaign,
                    'referrer'=> $referrer,
                    'referrer_host' => $host,
                ];
            }
        }

        return [
            'channel' => 'referral',
            'source'  => $host,
            'medium'  => 'referral',
            'campaign'=> $utmCampaign,
            'referrer'=> $referrer,
            'referrer_host' => $host,
        ];
    }

    public static function isBot(?string $ua): bool
    {
        if (!$ua) {
            return true;
        }

        return (bool) preg_match(
            '/googlebot|bingbot|slurp|duckduckbot|yandex|baiduspider|facebookexternalhit|twitterbot|linkedinbot|pingdom|uptimerobot|semrush|ahrefs|mj12|dotbot|petalsearch|bytespider|gptbot|claudebot|applebot|amazonbot|screaming frog|lighthouse|headlesschrome|phantomjs/i',
            $ua
        );
    }

    protected static function isPrivateIp(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    protected static function scalar($value): string
    {
        if (is_array($value)) {
            return trim((string) reset($value));
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return trim((string) $value);
        }
        return trim((string) $value);
    }

    protected static function fetchJson(string $url, float $timeout): array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\n",
            ],
        ]);
        $raw = @file_get_contents($url, false, $context);
        if (!$raw) {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    protected static function host(?string $url): string
    {
        if (!$url) {
            return '';
        }
        $host = parse_url($url, PHP_URL_HOST);
        return strtolower((string) $host);
    }
}
