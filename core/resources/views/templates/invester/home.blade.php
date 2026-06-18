@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $cm = 'https://crownmaire.com/wp-content/uploads';
        $pillars = [
            ['icon' => 'la-brain', 'title' => 'Quantitative Precision Across Global Markets', 'text' => 'Our investment approach is rooted in data science, machine learning, and real-time algorithmic execution. Decisions are informed by proprietary quantitative models designed to identify market inefficiencies and generate risk-adjusted returns across multiple asset classes.'],
            ['icon' => 'la-layer-group', 'title' => 'Multi-Asset Exposure with Structured Yield', 'text' => 'Crownmaire manages diversified exposure across currencies, indices, commodities, futures, and select equities. Portfolios are structured to balance opportunity with risk, allowing flexibility and adaptability across varying market conditions.'],
            ['icon' => 'la-server', 'title' => 'Fintech Infrastructure Built for Performance', 'text' => 'Our proprietary trading systems and automation frameworks enable scalable execution with institutional precision. Members receive secure access to reporting, performance summaries, and capital activity through a protected, institutional-grade platform.'],
            ['icon' => 'la-user-shield', 'title' => 'Exclusive, Investor-First Philosophy', 'text' => 'Access to Crownmaire\'s investment programs is private and invitation-only. We work with a select group of qualified participants through structured investment arrangements, emphasizing transparency, disciplined execution, and long-term alignment.'],
        ];
    @endphp

    {{-- Hero --}}
    <section class="cm-hero" id="cmHero">
        <div class="cm-hero__aurora" aria-hidden="true"></div>
        <div class="cm-hero__aurora cm-hero__aurora--2" aria-hidden="true"></div>
        <div class="cm-hero__grid-bg" aria-hidden="true"></div>
        <div class="cm-hero__noise" aria-hidden="true"></div>

        <div class="cm-container cm-hero__layout">
            <div class="cm-hero__copy cm-reveal">
                <span class="cm-badge"><i class="las la-award"></i> @lang('Crownmaire Capital')</span>
                <h1 class="cm-hero__title">
                    <span class="cm-hero__title-gradient">@lang('Quantitative')</span>
                    @lang('Fintech-Driven Algorithmic Asset Management and Multi-Asset Investment Firm')
                </h1>
                <p class="cm-hero__lead">@lang('Structured yield. Quant-driven performance. Private by design.')</p>
                <div class="cm-hero__actions">
                    @if (auth()->check())
                        <a href="{{ route('user.home') }}" class="cm-btn cm-btn--accent cm-btn--glow"><i class="las la-th-large"></i> @lang('Go to Dashboard')</a>
                    @else
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent cm-btn--glow"><i class="las la-paper-plane"></i> @lang('Request Invitation')</a>
                    @endif
                    <a href="{{ route('home') }}#about" class="cm-btn cm-btn--ghost">@lang('Explore our approach')</a>
                </div>
            </div>

            <div class="cm-hero__stage cm-reveal cm-reveal--delay" id="cmHeroStage">
                <div class="cm-hero__stage-glow" aria-hidden="true"></div>

                <div class="cm-command">
                    <div class="cm-command__bar">
                        <div class="cm-command__brand">
                            <i class="las la-chart-pie"></i>
                            <span>Crownmaire <em>Quant</em></span>
                        </div>
                        <div class="cm-command__tabs">
                            <span class="is-active">@lang('Performance')</span>
                            <span>@lang('Allocation')</span>
                            <span>@lang('Risk')</span>
                        </div>
                        <span class="cm-command__live"><i class="las la-circle"></i> LIVE</span>
                    </div>
                    <div class="cm-command__body">
                        <div class="cm-command__kpis">
                            <div class="cm-command__kpi">
                                <span>@lang('Total revenue')</span>
                                <strong>$104.32K</strong>
                                <small class="cm-trend cm-trend--up"><i class="las la-arrow-up"></i> 12.4%</small>
                            </div>
                            <div class="cm-command__kpi">
                                <span>@lang('Your balance')</span>
                                <strong>$24,124</strong>
                                <small>@lang('Available')</small>
                            </div>
                            <div class="cm-command__kpi cm-command__kpi--accent">
                                <span>@lang('YTD Return')</span>
                                <strong>24%</strong>
                                <small>@lang('Approved payouts')</small>
                            </div>
                        </div>
                        <div class="cm-command__charts">
                            <div class="cm-command__chart-main">
                                <div class="cm-command__chart-head">
                                    <span>@lang('Cumulative strategy performance')</span>
                                    <span class="cm-trend cm-trend--up">+24.0%</span>
                                </div>
                                <svg class="cm-chart-animated" viewBox="0 0 520 160" preserveAspectRatio="none" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="cmHeroGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#1bb0ce" stop-opacity="0.45"/>
                                            <stop offset="100%" stop-color="#1bb0ce" stop-opacity="0"/>
                                        </linearGradient>
                                        <linearGradient id="cmLineGrad" x1="0" y1="0" x2="1" y2="0">
                                            <stop offset="0%" stop-color="#0033ad"/>
                                            <stop offset="100%" stop-color="#1bb0ce"/>
                                        </linearGradient>
                                    </defs>
                                    <path class="cm-chart-animated__area" d="M0,130 L40,118 L80,122 L120,98 L160,108 L200,82 L240,90 L280,68 L320,76 L360,52 L400,58 L440,42 L480,48 L520,38 L520,160 L0,160 Z" fill="url(#cmHeroGrad)"/>
                                    <path class="cm-chart-animated__line" pathLength="100" d="M0,130 L40,118 L80,122 L120,98 L160,108 L200,82 L240,90 L280,68 L320,76 L360,52 L400,58 L440,42 L480,48 L520,38" fill="none" stroke="url(#cmLineGrad)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="cm-command__alloc">
                                <span class="cm-command__alloc-title">@lang('Asset allocation')</span>
                                <div class="cm-donut" aria-hidden="true">
                                    <svg viewBox="0 0 120 120">
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="14"/>
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="#0033ad" stroke-width="14" stroke-dasharray="75 302" stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="#1bb0ce" stroke-width="14" stroke-dasharray="60 302" stroke-dashoffset="-75" transform="rotate(-90 60 60)"/>
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="#47a8d4" stroke-width="14" stroke-dasharray="45 302" stroke-dashoffset="-135" transform="rotate(-90 60 60)"/>
                                        <circle cx="60" cy="60" r="48" fill="none" stroke="#7fc4e8" stroke-width="14" stroke-dasharray="38 302" stroke-dashoffset="-180" transform="rotate(-90 60 60)"/>
                                    </svg>
                                    <div class="cm-donut__center">
                                        <strong>5</strong>
                                        <span>@lang('Markets')</span>
                                    </div>
                                </div>
                                <ul class="cm-alloc-legend">
                                    <li><span style="background:#0033ad"></span> @lang('Commodities') 33%</li>
                                    <li><span style="background:#1bb0ce"></span> @lang('Forex') 20%</li>
                                    <li><span style="background:#47a8d4"></span> @lang('Indices') 18%</li>
                                    <li><span style="background:#7fc4e8"></span> @lang('Crypto') 14%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- What we do --}}
    <section class="cm-section" id="about">
        <div class="cm-container">
            <header class="cm-section__header cm-reveal">
                <span class="cm-section__tag">@lang('What we do')</span>
                <p class="cm-section__lead">@lang('Crownmaire Capital is a modern investment management firm deploying quantitative and algorithmic trading strategies across global markets. We operate at the intersection of finance, technology, and disciplined governance. Built on precision, powered by data, and refined through active risk management.')</p>
            </header>
            <div class="cm-cards">
                @foreach ($pillars as $i => $pillar)
                    <article class="cm-card cm-reveal{{ $i > 0 ? ' cm-reveal--delay' . ($i > 1 ? '-' . $i : '') : '' }}">
                        <div class="cm-card__icon"><i class="las {{ $pillar['icon'] }}"></i></div>
                        <h3>{{ __($pillar['title']) }}</h3>
                        <p>{{ __($pillar['text']) }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Dashboard --}}
    <section class="cm-section cm-section--alt" id="dashboard">
        <div class="cm-container">
            <div class="cm-split">
                <div class="cm-split__copy cm-reveal">
                    <span class="cm-section__tag">@lang('Platform')</span>
                    <h2 class="cm-split__title">@lang('Experience a World-Class Dashboard')</h2>
                    <p class="cm-split__text">@lang('Crownmaire provides secure, real-time visibility into capital activity, performance charts, and portfolio allocations. Our institutional-grade dashboard is designed to enhance transparency, deliver clear reporting, and support informed oversight through intuitive analytics and streamlined navigation.')</p>
                    <ul class="cm-feature-list">
                        <li><i class="las la-chart-line"></i> @lang('Real-time performance analytics')</li>
                        <li><i class="las la-chart-pie"></i> @lang('Portfolio allocation visibility')</li>
                        <li><i class="las la-file-invoice-dollar"></i> @lang('Capital activity reporting')</li>
                    </ul>
                    @if (auth()->check())
                        <a href="{{ route('user.home') }}" class="cm-btn cm-btn--accent"><i class="las la-th-large"></i> @lang('Open Dashboard')</a>
                    @else
                        <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent">@lang('Request Invitation')</a>
                    @endif
                </div>
                <div class="cm-split__media cm-reveal cm-reveal--delay">
                    <div class="cm-platform-shot">
                        <img
                            src="{{ asset($activeTemplateTrue . 'images/crownmaire/dashboard-screenshot.png') }}"
                            alt="@lang('Crownmaire portfolio dashboard')"
                            width="1400"
                            height="900"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="cm-section">
        <div class="cm-container">
            <div class="cm-metrics cm-reveal">
                <div class="cm-metrics__pattern" aria-hidden="true"></div>
                <div class="cm-metrics__overlay"></div>
                <div class="cm-metrics__body">
                    <div class="cm-metrics__intro">
                        <h3>@lang('Crownmaire is reserved for qualified investors')</h3>
                        <p>@lang('Participation in the firm\'s private investment programs is limited to qualified individuals and entities in order to preserve performance discipline, service quality, and operational integrity')</p>
                    </div>
                    <div class="cm-metrics__grid">
                        <div class="cm-metrics__item">
                            <i class="las la-landmark"></i>
                            <span class="cm-metrics__label">@lang('AUM')</span>
                            <span class="cm-metrics__value" data-count="5">$<span>0</span>M</span>
                        </div>
                        <div class="cm-metrics__item">
                            <i class="las la-users"></i>
                            <span class="cm-metrics__label">@lang('Members')</span>
                            <span class="cm-metrics__value" data-count="40" data-suffix="+">0+</span>
                        </div>
                        <div class="cm-metrics__item">
                            <i class="las la-globe"></i>
                            <span class="cm-metrics__label">@lang('Countries')</span>
                            <span class="cm-metrics__value" data-count="6">0</span>
                        </div>
                        <div class="cm-metrics__item">
                            <i class="las la-handshake"></i>
                            <span class="cm-metrics__label">@lang('Retention')</span>
                            <span class="cm-metrics__value" data-count="100" data-suffix="%">0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="cm-section cm-section--alt" id="faqs">
        <div class="cm-container">
            <div class="cm-split cm-split--faq">
                <div class="cm-split__copy cm-reveal">
                    <span class="cm-section__tag cm-section__tag--accent">@lang('frequently asked questions')</span>
                    <h2 class="cm-split__title">@lang('Explore key information to better understand our investment approach and operating model')</h2>
                    <a href="{{ route('contact') }}" class="cm-btn cm-btn--outline">
                        @lang('Read More')
                        <svg width="16" height="16" viewBox="0 0 17 16" fill="none" aria-hidden="true"><path d="M8.5 1L15.5 8M15.5 8L8.5 15M15.5 8H0" stroke="currentColor" stroke-width="1.5"/></svg>
                    </a>
                </div>
                <div class="cm-accordion cm-reveal cm-reveal--delay">
                    @php
                        $faqs = [
                            ['q' => 'How does Crownmaire manage capital across its strategies?', 'a' => 'Crownmaire deploys proprietary quantitative and algorithmic trading strategies informed by data science, machine learning, and real-time execution systems. These strategies operate across multiple global markets, including foreign exchange, indices, commodities, and equities, under defined risk and exposure parameters.'],
                            ['q' => 'What type of performance framework does Crownmaire follow?', 'a' => "Crownmaire's private investment programs are structured around predefined distribution frameworks derived from overall trading performance and internal capital allocation policies. Performance outcomes vary based on market conditions, strategy allocation, and participation structure.\n\nHistorical performance information is shared privately with participants."],
                            ['q' => 'How is capital managed and protected?', 'a' => "Capital is managed under strict internal risk and governance frameworks. Crownmaire applies exposure controls, drawdown limits, and reserve management practices designed to prioritize capital preservation.\n\nAll participation is subject to contractual agreements, risk disclosures, and internal compliance procedures, including KYC and AML standards."],
                            ['q' => 'What are the liquidity and withdrawal terms?', 'a' => "Liquidity terms are defined contractually. Participants may request distributions or capital withdrawals in accordance with their applicable agreement, subject to notice periods and prevailing liquidity conditions.\n\nCrownmaire maintains structured withdrawal and close-out policies to ensure operational stability."],
                            ['q' => 'What distinguishes Crownmaire Capital from other investment managers?', 'a' => "Crownmaire is built as a technology-driven, quantitatively focused investment manager with a disciplined, private operating model.\n\nRather than mass-market products, the firm operates selective investment programs emphasizing structured execution, transparency, and long-term alignment with participants."],
                        ];
                    @endphp
                    @foreach ($faqs as $faq)
                        <details class="cm-accordion__item">
                            <summary>{{ __($faq['q']) }}</summary>
                            <div class="cm-accordion__body">{!! nl2br(e(__($faq['a']))) !!}</div>
                        </details>
                    @endforeach
                </div>
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
