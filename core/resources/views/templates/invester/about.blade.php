@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $highlights = [
            ['icon' => 'la-brain', 'title' => 'Quantitative Research', 'text' => 'Data science and AI-enhanced analytics inform every decision.'],
            ['icon' => 'la-chart-line', 'title' => 'Algorithmic Execution', 'text' => 'Real-time trading systems deployed across global markets.'],
            ['icon' => 'la-layer-group', 'title' => 'Multi-Asset Strategies', 'text' => 'Disciplined exposure across currencies, indices, commodities, and more.'],
            ['icon' => 'la-shield-alt', 'title' => 'Risk-Aware Performance', 'text' => 'Capital preservation and adaptive controls across market cycles.'],
        ];
    @endphp

    {{-- Page hero --}}
    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-info-circle"></i> @lang('About us')</span>
            <h1 class="cm-page-hero__title">@lang('Redefining Asset Management Through Precision and Technology')</h1>
            <p class="cm-page-hero__lead">@lang('Crownmaire Capital — modern investment management through quantitative research, algorithmic trading, and technology-driven decision systems.')</p>
            <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent cm-btn--glow">
                <i class="las la-paper-plane"></i> @lang('Request Invitation')
            </a>
        </div>
    </section>

    {{-- Introduction --}}
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-about-intro cm-reveal">
                <div class="cm-about-intro__copy">
                    <span class="cm-section__tag">@lang('Who we are')</span>
                    <p>@lang('Crownmaire Capital is built to modernize investment management through the integration of quantitative research, algorithmic trading, and technology-driven decision systems. By combining data science, AI-enhanced analytics, and multi-asset strategies, we deliver a disciplined approach to capital management focused on precision, consistency, and risk-aware performance.')</p>
                    <p>@lang('Registered and operating across the United States and the United Arab Emirates, Crownmaire operates with a global perspective and a clear operational framework. Our proprietary infrastructure supports real-time market execution, structured portfolio management, and transparent reporting through an institutional-grade platform.')</p>
                    <p>@lang('We are not a traditional asset manager. The firm operates private investment programs and strategies structured under contractual arrangements, emphasizing capital preservation, disciplined allocation, and long-term sustainability.')</p>
                    <p>@lang('Crownmaire works with select high-net-worth individuals and institutional counterparties through invitation-only access, offering tailored investment programs aligned with long-term objectives and responsible capital management.')</p>
                </div>
                <div class="cm-about-highlights">
                    @foreach ($highlights as $i => $item)
                        <div class="cm-about-highlight cm-reveal{{ $i > 0 ? ' cm-reveal--delay' . ($i > 1 ? '-' . $i : '') : '' }}">
                            <div class="cm-about-highlight__icon"><i class="las {{ $item['icon'] }}"></i></div>
                            <div>
                                <h3>{{ __($item['title']) }}</h3>
                                <p>{{ __($item['text']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Performance philosophy --}}
    <section class="cm-section cm-section--alt">
        <div class="cm-container">
            <div class="cm-about-performance cm-reveal">
                <div class="cm-about-performance__copy">
                    <span class="cm-section__tag cm-section__tag--accent">@lang('Our approach')</span>
                    <h2 class="cm-split__title">@lang('Performance driven by quantitative discipline')</h2>
                    <p>@lang('Our performance is driven by real-time, quantitatively informed trading strategies deployed across global markets. Capital allocation and distribution frameworks are structured to balance performance objectives with disciplined risk management, while maintaining internal reserves to support operational stability and long-term growth.')</p>
                    <p>@lang('Crownmaire\'s approach emphasizes intelligent capital deployment, precision, and adaptive risk controls designed to perform across varying market environments and achieve consistent results.')</p>
                </div>
                <blockquote class="cm-about-quote">
                    <i class="las la-quote-left" aria-hidden="true"></i>
                    <p>@lang('Crownmaire exists to serve a select group of qualified participants who value discipline, transparency, and long-term alignment.')</p>
                </blockquote>
            </div>
        </div>
    </section>

    {{-- Offices --}}
    <section class="cm-section">
        <div class="cm-container">
            <header class="cm-section__header cm-reveal">
                <span class="cm-section__tag">@lang('Global presence')</span>
                <p class="cm-section__lead">@lang('Registered and operating across the United States and the United Arab Emirates with a global perspective and clear operational framework.')</p>
            </header>
            <div class="cm-offices">
                <article class="cm-office-card cm-reveal">
                    <div class="cm-office-card__icon"><i class="las la-building"></i></div>
                    <h3>@lang('New York')</h3>
                    <p>
                        <strong>Crownmaire Capital LLC</strong><br>
                        100 Wall Street Ct<br>
                        New York, NY 10005<br>
                        @lang('United States of America')
                    </p>
                </article>
                <article class="cm-office-card cm-reveal cm-reveal--delay">
                    <div class="cm-office-card__icon"><i class="las la-city"></i></div>
                    <h3>@lang('Dubai')</h3>
                    <p>
                        <strong>Crownmaire Capital LLC</strong><br>
                        2402 Al-Manara Tower<br>
                        Business Bay, Dubai 00000<br>
                        @lang('United Arab Emirates')
                    </p>
                </article>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cm-section cm-section--cta">
        <div class="cm-container">
            <div class="cm-banner cm-reveal">
                <div class="cm-banner__pattern" aria-hidden="true"></div>
                <div class="cm-banner__overlay"></div>
                <div class="cm-banner__content">
                    <div class="cm-banner__icon"><i class="las la-rocket"></i></div>
                    <h3>@lang('Access the future of smart investment strategies')</h3>
                    <p>@lang('Connect with our team to learn how Crownmaire\'s quantitative programs align with your capital objectives.')</p>
                    <div class="cm-banner__actions">
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent">
                            <i class="las la-envelope"></i> @lang('Contact experts')
                        </a>
                        @if (auth()->check())
                            <a href="{{ route('user.home') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark">
                                <i class="las la-th-large"></i> @lang('Dashboard')
                            </a>
                        @else
                            <a href="{{ route('user.login') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark">
                                <i class="las la-sign-in-alt"></i> @lang('Member Login')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=17">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=17" defer></script>
@endpush
