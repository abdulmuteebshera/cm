@php
    $platformModules = [
        [
            'group' => 'Performance & Analytics',
            'desc' => 'Real-time visibility into returns, strategy performance, and portfolio KPIs.',
            'items' => [
                ['icon' => 'la-chart-line', 'title' => 'Return Analytics', 'text' => 'Interactive charts showing approved period returns from your strategies — quarterly, semi-annual, or yearly — with cumulative performance tracking over time.'],
                ['icon' => 'la-chart-area', 'title' => 'Strategy Performance', 'text' => 'Dedicated performance views for each quantitative program, including period return history, payout records, and strategy-level analytics.'],
                ['icon' => 'la-wallet', 'title' => 'Portfolio Command Center', 'text' => 'At-a-glance KPIs for total portfolio value, active capital deployed, cumulative returns earned, and capital invested across all programs.'],
            ],
        ],
        [
            'group' => 'Investments & Allocation',
            'desc' => 'Track deployed capital, allocations, and upcoming payout cycles.',
            'items' => [
                ['icon' => 'la-layer-group', 'title' => 'Investment Portfolio', 'text' => 'Full portfolio page with deployed capital, returns earned, allocation-by-strategy charts, and detailed investment cards with cycle progress and next payout dates.'],
                ['icon' => 'la-chart-pie', 'title' => 'AI Asset Allocation', 'text' => 'Visual breakdown of Crownmaire\'s multi-market exposure across commodities, forex, indices, crypto, and equities — monitored and adjusted by the AI Quant Engine.'],
                ['icon' => 'la-calendar-check', 'title' => 'Payout Schedule', 'text' => 'Transparent calendar of upcoming strategy payout cycles so members always know when the next distribution period begins.'],
            ],
        ],
        [
            'group' => 'Membership & Recognition',
            'desc' => 'Status tiers, rankings, and verifiable investment credentials.',
            'items' => [
                ['icon' => 'la-medal', 'title' => 'Membership Tiers', 'text' => 'Progressive tier system based on total invested capital — unlock status levels, track progress to the next tier, and view full tier benefits.'],
                ['icon' => 'la-trophy', 'title' => 'Leaderboard & Rankings', 'text' => 'Community leaderboard and investment rankings that recognize top-performing members within the Crownmaire investor network.'],
                ['icon' => 'la-certificate', 'title' => 'Investment Certificates', 'text' => 'Verifiable digital certificates for your investments — professionally formatted, shareable, and linked to your account records.'],
            ],
        ],
        [
            'group' => 'Treasury & Operations',
            'desc' => 'Secure funding, transfers, and a complete transaction audit trail.',
            'items' => [
                ['icon' => 'la-university', 'title' => 'Deposits & Withdrawals', 'text' => 'Secure funding and withdrawal workflows with full history, status tracking, and multiple payment gateway support.'],
                ['icon' => 'la-exchange-alt', 'title' => 'Balance Transfers', 'text' => 'Internal transfer capability between wallet balances for flexible capital management within your account.'],
                ['icon' => 'la-list-alt', 'title' => 'Transaction Ledger', 'text' => 'Complete audit trail of every deposit, return, withdrawal, and transfer — searchable and export-ready for your records.'],
            ],
        ],
        [
            'group' => 'Security & Communication',
            'desc' => 'Institutional security standards and direct firm communications.',
            'items' => [
                ['icon' => 'la-shield-alt', 'title' => 'KYC & Two-Factor Auth', 'text' => 'Identity verification workflow and Google Authenticator 2FA to protect accounts with institutional-grade security standards.'],
                ['icon' => 'la-bullhorn', 'title' => 'Firm Announcements', 'text' => 'Direct channel for Crownmaire communications — policy updates, market notices, and important investor information delivered inside the portal.'],
                ['icon' => 'la-mobile', 'title' => 'Mobile App Access', 'text' => 'Native Crownmaire Capital mobile app for iOS and Android — full portal access on the go with secure authentication and optimized navigation.'],
            ],
        ],
    ];
@endphp

<div class="cm-platform-showcase">
    <div class="cm-platform-showcase__visual cm-reveal">
        <div class="cm-platform-frame">
            <div class="cm-platform-frame__bar" aria-hidden="true">
                <span></span><span></span><span></span>
                <em>Crownmaire Member Portal</em>
            </div>
            <div class="cm-platform-shot cm-platform-shot--hero">
                <img
                    src="{{ asset($activeTemplateTrue . 'images/crownmaire/dashboard-screenshot.png') }}"
                    alt="@lang('Crownmaire Capital investor dashboard')"
                    width="1400"
                    height="900"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        </div>
        <p class="cm-platform-showcase__caption">
            <i class="las la-microchip"></i>
            @lang('Live member dashboard — portfolio KPIs, return analytics, strategy allocation, and AI Quant Engine status in real time.')
        </p>
    </div>

    <div class="cm-platform-modules">
        @foreach($platformModules as $gi => $group)
            <section class="cm-platform-section cm-reveal{{ $gi > 0 ? ' cm-reveal--delay' . min($gi, 3) : '' }}">
                <header class="cm-platform-section__head">
                    <div>
                        <h3 class="cm-platform-section__title">{{ __($group['group']) }}</h3>
                        <p class="cm-platform-section__desc">{{ __($group['desc']) }}</p>
                    </div>
                </header>
                <div class="cm-platform-cards">
                    @foreach($group['items'] as $feature)
                        <article class="cm-platform-card">
                            <div class="cm-platform-card__icon" aria-hidden="true">
                                <i class="las {{ $feature['icon'] }}"></i>
                            </div>
                            <h4 class="cm-platform-card__title">{{ __($feature['title']) }}</h4>
                            <p class="cm-platform-card__text">{{ __($feature['text']) }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="cm-platform-showcase__cta cm-reveal">
        @if (auth()->check())
            <a href="{{ route('user.home') }}" class="cm-btn cm-btn--accent cm-btn--glow"><i class="las la-th-large"></i> @lang('Open Your Dashboard')</a>
        @else
            <a href="{{ route('contact') }}" class="cm-btn cm-btn--accent cm-btn--glow"><i class="las la-paper-plane"></i> @lang('Request Invitation')</a>
            <a href="{{ route('user.login') }}" class="cm-btn cm-btn--outline"><i class="las la-sign-in-alt"></i> @lang('Member Login')</a>
        @endif
    </div>
</div>
