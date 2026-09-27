<?php

namespace App\Support\Seo;

use App\Models\Frontend;

class SeoSite
{
    public const PRIMARY_HOST = 'crownmairecapital.com';
    public const ALIAS_HOST = 'crownmaire.com';
    public const BRAND = 'Crownmaire Capital';
    public const LEGAL_NAME = 'Crownmaire Capital LLC';
    public const EMAIL = 'Info@crownmaire.com';
    public const PHONE = '+1 917 500 6476';
    public const PHONE_TEL = '+19175006476';

    public static function ownedHosts(): array
    {
        return [
            self::PRIMARY_HOST,
            'www.' . self::PRIMARY_HOST,
            self::ALIAS_HOST,
            'www.' . self::ALIAS_HOST,
        ];
    }

    public static function isOwnedHost(?string $host): bool
    {
        $host = strtolower((string) $host);
        return in_array($host, self::ownedHosts(), true);
    }

    public static function isLocalHost(?string $host): bool
    {
        $host = strtolower((string) $host);
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.test')
            || preg_match('/^\d+\.\d+\.\d+\.\d+$/', $host);
    }

    public static function shouldCanonicalize(?string $host): bool
    {
        $host = strtolower((string) $host);
        if (self::isLocalHost($host) || $host === '') {
            return false;
        }

        return in_array($host, [
            self::ALIAS_HOST,
            'www.' . self::ALIAS_HOST,
            'www.' . self::PRIMARY_HOST,
        ], true);
    }

    public static function publicBase(): string
    {
        $host = strtolower((string) request()->getHost());
        if (self::isOwnedHost($host) || !self::isLocalHost($host)) {
            if (self::isOwnedHost($host) || $host === self::PRIMARY_HOST) {
                return 'https://' . self::PRIMARY_HOST;
            }
        }

        return rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/');
    }

    public static function canonicalForPath(string $path = '/', array $query = []): string
    {
        $path = '/' . ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $url = self::publicBase() . ($path === '/' ? '/' : $path);
        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public static function currentCanonical(): string
    {
        $path = '/' . ltrim(request()->path(), '/');
        if ($path === '/.') {
            $path = '/';
        }

        $keep = [];
        foreach (['page'] as $key) {
            if (request()->filled($key)) {
                $keep[$key] = request()->query($key);
            }
        }

        return self::canonicalForPath($path === '/' ? '/' : $path, $keep);
    }

    public static function offices(): array
    {
        return [
            [
                '@type'           => 'PostalAddress',
                'streetAddress'   => '100 Wall Street Ct',
                'addressLocality' => 'New York',
                'addressRegion'   => 'NY',
                'postalCode'      => '10005',
                'addressCountry'  => 'US',
                'label'           => 'New York',
            ],
            [
                '@type'           => 'PostalAddress',
                'streetAddress'   => '2402 Al-Manara Tower, Business Bay',
                'addressLocality' => 'Dubai',
                'postalCode'      => '00000',
                'addressCountry'  => 'AE',
                'label'           => 'Dubai',
            ],
        ];
    }

    public static function defaultKeywords(): array
    {
        return [
            'Crownmaire Capital',
            'asset management firm',
            'quantitative asset management',
            'algorithmic investment management',
            'multi-asset investment firm',
            'private investment programs',
            'institutional asset manager',
            'quantitative trading',
            'New York asset manager',
            'Dubai investment firm',
            'fintech asset management',
            'qualified investor programs',
        ];
    }

    public static function defaultDescription(): string
    {
        return 'Crownmaire Capital is a quantitative asset management firm offering invitation-only multi-asset investment programs for qualified investors, with offices in New York and Dubai.';
    }

    public static function logoUrl(): string
    {
        $path = getFilePath('logoIcon') . '/logo.png';
        $url  = getImage($path);
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        return self::publicBase() . '/' . ltrim($url, '/');
    }

    public static function socialImage(): array
    {
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        $image = $seo->data_values->image ?? null;
        if ($image) {
            $url = getImage(getFilePath('seo') . '/' . $image);
            if (!str_starts_with((string) $url, 'http')) {
                $url = self::publicBase() . '/' . ltrim($url, '/');
            }
            $size = explode('x', (string) getFileSize('seo'));
            return [
                'url'    => $url,
                'width'  => $size[0] ?? '1200',
                'height' => $size[1] ?? '630',
            ];
        }

        return [
            'url'    => self::logoUrl(),
            'width'  => '512',
            'height' => '512',
        ];
    }

    public static function ensureDefaultSeo(): void
    {
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        if (!$seo) {
            $frontend              = new Frontend();
            $frontend->data_keys   = 'seo.data';
            $frontend->data_values = [
                'keywords'           => self::defaultKeywords(),
                'description'        => self::defaultDescription(),
                'social_title'       => self::BRAND . ' | Quantitative Asset Management Firm',
                'social_description' => self::defaultDescription(),
                'image'              => null,
            ];
            $frontend->save();
            return;
        }

        $values = (array) ($seo->data_values ?? []);
        $description = trim((string) ($values['description'] ?? ''));
        $keywords = $values['keywords'] ?? [];
        if ($description !== '' && !empty($keywords)) {
            return;
        }

        if ($description === '') {
            $values['description'] = self::defaultDescription();
        }
        if (empty($keywords)) {
            $values['keywords'] = self::defaultKeywords();
        }
        if (empty($values['social_title'])) {
            $values['social_title'] = self::BRAND . ' | Quantitative Asset Management Firm';
        }
        if (empty($values['social_description'])) {
            $values['social_description'] = self::defaultDescription();
        }

        $seo->data_values = $values;
        $seo->save();
    }
}
