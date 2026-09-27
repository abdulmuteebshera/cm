@php
    $catalog = $seoPage ?? \App\Support\Seo\SeoCatalog::resolve(get_defined_vars());
    $socialImage = $catalog->image ?? \App\Support\Seo\SeoSite::logoUrl();
    $canonical = $catalog->canonical ?? \App\Support\Seo\SeoSite::currentCanonical();
    $jsonLd = $catalog->indexable ? \App\Support\Seo\SeoCatalog::jsonLd($catalog) : null;

    $fallbackSeo = $seo ?? null;
    if (isset($seoContents) && (is_array($seoContents) || is_object($seoContents))) {
        $legacy = json_decode(json_encode($seoContents));
        if (!empty($legacy->image)) {
            $socialImage = $legacy->image;
        }
    }
@endphp

<title>{{ $catalog->title }}</title>
<meta name="title" content="{{ $catalog->title }}">
<meta name="description" content="{{ $catalog->description }}">
<meta name="keywords" content="{{ implode(',', (array) $catalog->keywords) }}">
<meta name="author" content="{{ \App\Support\Seo\SeoSite::BRAND }}">
<meta name="robots" content="{{ $catalog->robots }}">
<meta name="googlebot" content="{{ $catalog->robots }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $canonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

<link rel="shortcut icon" href="{{ getImage(getFilePath('logoIcon') . '/favicon.png') }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}">
<meta name="theme-color" content="#0b1f2a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="{{ \App\Support\Seo\SeoSite::BRAND }}">
<meta name="application-name" content="{{ \App\Support\Seo\SeoSite::BRAND }}">
<meta name="format-detection" content="telephone=yes">

<meta name="geo.region" content="US-NY">
<meta name="geo.placename" content="New York">
<meta name="geo.position" content="40.7069;-74.0086">
<meta name="ICBM" content="40.7069, -74.0086">

<meta itemprop="name" content="{{ $catalog->title }}">
<meta itemprop="description" content="{{ $catalog->description }}">
<meta itemprop="image" content="{{ $socialImage }}">

<meta property="og:locale" content="en_US">
<meta property="og:type" content="{{ $catalog->og_type ?? 'website' }}">
<meta property="og:site_name" content="{{ \App\Support\Seo\SeoSite::BRAND }}">
<meta property="og:title" content="{{ $catalog->title }}">
<meta property="og:description" content="{{ $catalog->description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $socialImage }}">
<meta property="og:image:alt" content="{{ $catalog->title }}">
<meta property="og:image:width" content="{{ $catalog->image_width ?? 1200 }}">
<meta property="og:image:height" content="{{ $catalog->image_height ?? 630 }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $catalog->title }}">
<meta name="twitter:description" content="{{ $catalog->description }}">
<meta name="twitter:image" content="{{ $socialImage }}">

@if($jsonLd && !empty($jsonLd['@graph']))
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endif
