@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $programs = [
            ['icon' => 'la-microchip', 'title' => 'Quantitative Core', 'freq' => 'Quarterly distributions', 'text' => 'Proprietary algorithmic strategies deployed across liquid global markets with systematic risk controls and transparent period reporting.', 'points' => ['Multi-asset quantitative models', 'Institutional execution stack', 'Period-based return framework']],
            ['icon' => 'la-chart-area', 'title' => 'Multi-Asset Growth', 'freq' => 'Semi-annual distributions', 'text' => 'Broader capital allocation designed for participants seeking diversified exposure with longer compounding horizons.', 'points' => ['Cross-market diversification', 'Adaptive volatility management', 'Structured payout cycles']],
            ['icon' => 'la-shield-alt', 'title' => 'Capital Preservation', 'freq' => 'Conservative profile', 'text' => 'Risk-first positioning emphasizing drawdown limits, reserve management, and disciplined capital deployment.', 'points' => ['Conservative risk budget', 'Liquidity-aware structures', 'Governance-first oversight']],
        ];
        $process = [
            ['step' => '01', 'title' => 'Qualification & invitation', 'text' => 'Private programs are available to qualified participants following introductory review and compliance checks.'],
            ['step' => '02', 'title' => 'Capital deployment', 'text' => 'Funds are allocated to approved strategies under contractual terms with defined reporting and distribution schedules.'],
            ['step' => '03', 'title' => 'Performance & reporting', 'text' => 'Members access institutional-grade dashboards, period summaries, and portfolio visibility through the Crownmaire portal.'],
            ['step' => '04', 'title' => 'Distributions & liquidity', 'text' => 'Payouts and withdrawal requests follow program-specific notice periods and liquidity policies.'],
        ];
    @endphp

    <section class="cm-page-hero">
        <div class="cm-page-hero__aurora" aria-hidden="true"></div>
        <div class="cm-page-hero__grid" aria-hidden="true"></div>
        <div class="cm-container cm-page-hero__inner cm-reveal">
            <span class="cm-badge"><i class="las la-layer-group"></i> @lang('Investment Strategies')</span>
            <h1 class="cm-page-hero__title">@lang('Structured programs built for quantitative performance')</h1>
            <p class="cm-page-hero__lead">@lang('Crownmaire offers invitation-only investment programs combining algorithmic execution, multi-asset diversification, and institutional reporting — designed for qualified investors seeking disciplined long-term alignment.')</p>
            <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent cm-btn--glow">
                <i class="las la-paper-plane"></i> @lang('Request Invitation')
            </a>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-container">
            <header class="cm-section__header cm-reveal">
                <span class="cm-section__tag">@lang('Program overview')</span>
                <p class="cm-section__lead">@lang('Each strategy is governed by defined risk parameters, transparent reporting cycles, and contractual distribution frameworks — not speculative trading products.')</p>
            </header>
            <div class="cm-strategy-grid">
                @foreach($programs as $i => $program)
                    <article class="cm-strategy-card cm-reveal{{ $i > 0 ? ' cm-reveal--delay' . ($i > 1 ? '-' . $i : '') : '' }}">
                        <div class="cm-strategy-card__top">
                            <div class="cm-strategy-card__icon"><i class="las {{ $program['icon'] }}"></i></div>
                            <span class="cm-strategy-card__freq">{{ __($program['freq']) }}</span>
                        </div>
                        <h3>{{ __($program['title']) }}</h3>
                        <p>{{ __($program['text']) }}</p>
                        <ul>
                            @foreach($program['points'] as $point)
                                <li><i class="las la-check"></i> {{ __($point) }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cm-section cm-section--alt">
        <div class="cm-container">
            <header class="cm-section__header cm-reveal">
                <span class="cm-section__tag cm-section__tag--accent">@lang('Investor journey')</span>
                <p class="cm-section__lead">@lang('A private, professional onboarding process aligned with institutional asset management standards.')</p>
            </header>
            <div class="cm-process-grid">
                @foreach($process as $i => $step)
                    <article class="cm-process-step cm-reveal{{ $i > 0 ? ' cm-reveal--delay' . ($i > 1 ? '-' . $i : '') : '' }}">
                        <span class="cm-process-step__num">{{ $step['step'] }}</span>
                        <h3>{{ __($step['title']) }}</h3>
                        <p>{{ __($step['text']) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-split">
                <div class="cm-split__copy cm-reveal">
                    <span class="cm-section__tag">@lang('Governance')</span>
                    <h2 class="cm-split__title">@lang('Risk management is embedded in every strategy')</h2>
                    <p class="cm-split__text">@lang('Crownmaire applies exposure limits, reserve policies, and compliance procedures including KYC and AML standards. Performance is reported through approved period frameworks — not unaudited marketing claims.')</p>
                    <ul class="cm-feature-list">
                        <li><i class="las la-user-shield"></i> @lang('Invitation-only qualified access')</li>
                        <li><i class="las la-file-contract"></i> @lang('Contractual program terms')</li>
                        <li><i class="las la-balance-scale"></i> @lang('Defined liquidity and withdrawal policies')</li>
                    </ul>
                </div>
                <div class="cm-reveal cm-reveal--delay">
                    <div class="cm-governance-panel">
                        <div class="cm-governance-panel__stat">
                            <span>@lang('Operating regions')</span>
                            <strong>@lang('United States & UAE')</strong>
                        </div>
                        <div class="cm-governance-panel__stat">
                            <span>@lang('Reporting')</span>
                            <strong>@lang('Institutional portal')</strong>
                        </div>
                        <div class="cm-governance-panel__stat">
                            <span>@lang('Approach')</span>
                            <strong>@lang('Quantitative & multi-asset')</strong>
                        </div>
                        <a href="{{ route('plan') }}" class="cm-btn cm-btn--accent cm-btn--block">@lang('View available programs')</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cm-section cm-section--cta">
        <div class="cm-container">
            <div class="cm-banner cm-reveal">
                <div class="cm-banner__pattern" aria-hidden="true"></div>
                <div class="cm-banner__overlay"></div>
                <div class="cm-banner__content">
                    <div class="cm-banner__icon"><i class="las la-rocket"></i></div>
                    <h3>@lang('Ready to explore Crownmaire programs?')</h3>
                    <p>@lang('Speak with our team about qualification, strategy fit, and private investor access.')</p>
                    <div class="cm-banner__actions">
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent">@lang('Contact experts')</a>
                        <a href="{{ route('platform') }}" class="cm-btn cm-btn--ghost cm-btn--on-dark">@lang('Explore platform')</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/crownmaire-landing.css') }}?v=18">
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'js/crownmaire-landing.js') }}?v=18" defer></script>
@endpush
