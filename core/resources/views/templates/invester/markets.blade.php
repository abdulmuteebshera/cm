@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero cm-page-hero--markets">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-chart-line"></i> @lang('Markets')</span>
            <h1 class="cm-page-hero__title">@lang('Global market intelligence')</h1>
            <p class="cm-page-hero__lead">@lang('Live index performance and the latest finance headlines from international markets.')</p>
        </div>
    </section>

    @include($activeTemplate . 'partials.market-ticker', ['marketIndices' => $marketIndices, 'hideTickerLink' => true])

    <section class="cm-section cm-section--markets">
        <div class="cm-container">
            @if(!empty($financeNews))
                @include($activeTemplate . 'partials.market-news-list', ['newsItems' => $financeNews])
                @include($activeTemplate . 'partials.market-news-pagination', [
                    'newsCurrentPage' => $newsCurrentPage,
                    'newsTotalPages' => $newsTotalPages,
                ])
            @else
                <div class="cm-news-empty cm-reveal">
                    <i class="las la-rss"></i>
                    <h3>@lang('News feed temporarily unavailable')</h3>
                    <p>@lang('Please check back shortly for the latest global finance headlines.')</p>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=22">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=22" defer></script>
@endpush
