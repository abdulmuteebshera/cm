@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="cm-page-hero cm-page-hero--insights">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-newspaper"></i> @lang('Market Insights')</span>
            <h1 class="cm-page-hero__title">@lang('Global finance news from markets around the world')</h1>
            <p class="cm-page-hero__lead">@lang('A live stream of international business and market headlines — aggregated from leading financial publishers and refreshed throughout the day.')</p>
        </div>
    </section>

    @include($activeTemplate . 'partials.market-ticker', ['marketIndices' => $marketIndices])

    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-insights-toolbar cm-reveal">
                <p class="cm-insights-note">
                    <i class="las la-info-circle"></i>
                    @lang('Headlines are aggregated from public financial news feeds worldwide. Crownmaire does not endorse third-party content.')
                </p>
            </div>

            @if(!empty($newsItems))
                @include($activeTemplate . 'partials.market-news-cards', ['newsItems' => $newsItems])
            @else
                <div class="cm-news-empty cm-reveal">
                    <i class="las la-rss"></i>
                    <h3>@lang('News feed temporarily unavailable')</h3>
                    <p>@lang('Please check back shortly for the latest global finance headlines.')</p>
                </div>
            @endif
        </div>
    </section>

    <section class="cm-section cm-section--alt">
        <div class="cm-container">
            <div class="cm-split">
                <div class="cm-split__copy cm-reveal">
                    <span class="cm-section__tag">@lang('Research perspective')</span>
                    <h2 class="cm-split__title">@lang('How Crownmaire interprets global market signals')</h2>
                    <p class="cm-split__text">@lang('Our quantitative desk monitors macro data, liquidity conditions, and cross-asset volatility across international markets. Public headlines provide context; investment decisions remain governed by proprietary models and institutional risk frameworks.')</p>
                    <a href="{{ route('strategies') }}" class="cm-btn cm-btn--accent">@lang('Our strategies')</a>
                </div>
                <div class="cm-reveal cm-reveal--delay">
                    <div class="cm-research-panel">
                        <div class="cm-research-panel__item">
                            <i class="las la-chart-line"></i>
                            <div>
                                <strong>@lang('Equities & indices')</strong>
                                <p>@lang('Global index moves, sector rotation, and policy-sensitive assets.')</p>
                            </div>
                        </div>
                        <div class="cm-research-panel__item">
                            <i class="las la-coins"></i>
                            <div>
                                <strong>@lang('Commodities & FX')</strong>
                                <p>@lang('Energy, metals, currencies, and cross-border capital flows.')</p>
                            </div>
                        </div>
                        <div class="cm-research-panel__item">
                            <i class="las la-globe"></i>
                            <div>
                                <strong>@lang('Multi-market allocation')</strong>
                                <p>@lang('Structured exposure designed for worldwide macro environments.')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=19">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=19" defer></script>
@endpush
