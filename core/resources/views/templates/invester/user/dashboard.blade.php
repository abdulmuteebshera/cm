@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $welcomeName    = $user->firstname ?: strtok(trim($user->fullname), ' ') ?: $user->username;
    $portfolioValue = $user->deposit_wallet + $user->interest_wallet;
    $todayLabel     = now()->format('l, F j, Y');
@endphp

<div class="dashboard-inner quant-dashboard">

    {{-- Header --}}
    <div class="quant-header">
        <div class="quant-header__main">
            <div class="dashboard-welcome dashboard-welcome--compact">
                <div class="dashboard-welcome__accent" aria-hidden="true"></div>
                <div class="dashboard-welcome__content">
                    <p class="dashboard-welcome__text">
                        Hi <span class="dashboard-welcome__name">{{ $welcomeName }}</span>, welcome to
                        <span class="dashboard-welcome__brand">Crownmaire Capital</span>
                        <span class="dashboard-welcome__sep">&mdash;</span>
                        one of the world&rsquo;s leading AI-powered quant funds, where technology, intelligence, and opportunity come together to help shape your financial future.
                    </p>
                </div>
            </div>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <span class="quant-date"><i class="las la-calendar"></i> {{ $todayLabel }}</span>
                <span class="quant-badge quant-badge--ai"><i class="las la-microchip"></i> AI Quant Engine</span>
            </div>
            <div class="quant-panel quant-panel--live quant-header__ytd">
                <div class="quant-header__ytd-inner">
                    <div class="quant-header__ytd-label">
                        <span class="quant-header__ytd-title">
                            @if($accountReturn->earned > 0)
                                <span class="quant-live-dot quant-live-dot--active"></span>
                            @endif
                            @lang('Return Till Date')
                        </span>
                        <small>@lang('Total return earned on your account')</small>
                    </div>
                    <div class="quant-header__ytd-value">
                        @if($accountReturn->invested > 0)
                            @if($accountReturn->total_percent >= 0)
                                <span class="quant-live-kpi__value">{{ showAmount($accountReturn->total_percent) }}%</span>
                            @else
                                <span class="quant-live-kpi__value quant-live-kpi__value--loss">{{ showAmount($accountReturn->total_percent) }}%</span>
                            @endif
                            <span class="quant-live-kpi__status quant-live-kpi__status--live">{{ $general->cur_sym }}{{ showAmount($accountReturn->earned) }} @lang('earned')</span>
                        @else
                            <span class="quant-live-kpi__value">—</span>
                            <span class="quant-live-kpi__status">@lang('No returns yet')</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System alerts --}}
    <div class="quant-alerts">
        @if ($user->deposit_wallet <= 0 && $user->interest_wallet <= 0)
        <div class="quant-alert quant-alert--danger">
            <i class="las la-exclamation-triangle"></i>
            <div><strong>@lang('Empty Balance')</strong> &mdash; @lang('Please') <a href="{{ route('user.deposit.index') }}">@lang('deposit')</a> @lang('to begin investing.')</div>
        </div>
        @endif
        @if ($user->deposits->where('status',1)->count() == 1 && !$user->invests->count())
        <div class="quant-alert quant-alert--success">
            <i class="las la-check-circle"></i>
            <div><strong>@lang('First Deposit')</strong> &mdash; @lang('Go to') <a href="{{ route('plan') }}">@lang('investment plan')</a> @lang('and invest now.')</div>
        </div>
        @endif
        @if($pendingWithdrawals)
        <div class="quant-alert quant-alert--info">
            <i class="las la-clock"></i>
            <div><strong>@lang('Withdrawal Pending')</strong> &mdash; {{ showAmount($pendingWithdrawals) }} {{ $general->cur_text }}. <a href="{{ route('user.withdraw.history') }}">@lang('View history')</a></div>
        </div>
        @endif
        @if($pendingDeposits)
        <div class="quant-alert quant-alert--info">
            <i class="las la-clock"></i>
            <div><strong>@lang('Deposit Pending')</strong> &mdash; {{ showAmount($pendingDeposits) }} {{ $general->cur_text }}. <a href="{{ route('user.deposit.history') }}">@lang('View history')</a></div>
        </div>
        @endif
        @if(!$user->ts)
        <div class="quant-alert quant-alert--warning">
            <i class="las la-shield-alt"></i>
            <div><strong>@lang('2FA Recommended')</strong> &mdash; <a href="{{ route('user.twofactor') }}">@lang('Enable 2FA')</a> @lang('to secure your account.')</div>
        </div>
        @endif
        @if($isHoliday)
        <div class="quant-alert quant-alert--info">
            <i class="las la-pause-circle"></i>
            <div><strong>@lang('Holiday')</strong> &mdash; @lang('Operations resume in') <span id="counter" class="fw-bold"></span>.</div>
        </div>
        @endif
        @if($user->kv == 0)
        <div class="quant-alert quant-alert--info">
            <i class="las la-id-card"></i>
            <div><strong>@lang('KYC Required')</strong> &mdash; <a href="{{ route('user.kyc.form') }}">@lang('Submit KYC')</a> @lang('to enable withdrawals.')</div>
        </div>
        @elseif($user->kv == 2)
        <div class="quant-alert quant-alert--warning">
            <i class="las la-hourglass-half"></i>
            <div><strong>@lang('KYC Pending')</strong> &mdash; <a href="{{ route('user.kyc.data') }}">@lang('View submission')</a></div>
        </div>
        @endif
    </div>

    {{-- Primary KPIs --}}
    <div class="quant-kpi-grid">
        <div class="quant-kpi quant-kpi--primary">
            <div class="quant-kpi__icon"><i class="las la-wallet"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Total Portfolio Value')</span>
                <span class="quant-kpi__value">{{ showAmount($portfolioValue) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">{{ showAmount($user->deposit_wallet) }} @lang('liquid') &middot; {{ showAmount($user->interest_wallet) }} @lang('returns')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-area"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Active Capital')</span>
                <span class="quant-kpi__value">{{ showAmount($runningInvests) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@lang('Currently deployed in strategies')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-line"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Cumulative Returns')</span>
                <span class="quant-kpi__value">{{ showAmount($interests) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@lang('Total interest earned to date')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-university"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Capital Invested')</span>
                <span class="quant-kpi__value">{{ showAmount($invests) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">{{ showAmount($completedInvests) }} @lang('completed') &middot; {{ showAmount($runningInvests) }} @lang('active')</span>
            </div>
        </div>
    </div>

    {{-- Membership tier --}}
    <div class="quant-panel quant-tier-card">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Membership Tier')</h5>
                <p class="quant-panel__desc">@lang('Your status based on total invested capital')</p>
            </div>
            <a href="{{ route('user.tiers') }}" class="quant-panel__link">@lang('View tiers') <i class="las la-arrow-right"></i></a>
        </div>
        <div class="quant-panel__body">
            @if($tierStanding->current)
                @php $cur = $tierStanding->current; @endphp
                <div class="quant-tier">
                    <div class="quant-tier__badge" style="--tier-color: {{ $cur['color'] }};">
                        <span class="quant-tier__emoji">{{ $cur['emoji'] }}</span>
                    </div>
                    <div class="quant-tier__info">
                        <div class="quant-tier__head">
                            <span class="quant-tier__name" style="color: {{ $cur['color'] }};">{{ __($cur['name']) }}</span>
                            <span class="quant-tier__amount">{{ showAmount($tierStanding->amount) }} {{ $general->cur_text }} @lang('invested')</span>
                        </div>
                        @if($tierStanding->is_top)
                            <div class="quant-tier__top">
                                <i class="las la-crown"></i> @lang('You have reached the highest tier')
                            </div>
                        @elseif($tierStanding->next)
                            @php $nx = $tierStanding->next; @endphp
                            <div class="quant-tier__progress">
                                <div class="quant-tier__progress-bar">
                                    <span style="width: {{ $tierStanding->progress }}%; background: {{ $nx['color'] }};"></span>
                                </div>
                                <div class="quant-tier__progress-meta">
                                    <span>{{ $tierStanding->progress }}%</span>
                                    <span>{{ showAmount($tierStanding->needed) }} {{ $general->cur_text }} @lang('to') {{ $nx['emoji'] }} {{ __($nx['name']) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="quant-tier quant-tier--none">
                    <div class="quant-tier__badge quant-tier__badge--muted">
                        <span class="quant-tier__emoji"><i class="las la-medal"></i></span>
                    </div>
                    <div class="quant-tier__info">
                        <div class="quant-tier__head">
                            <span class="quant-tier__name">@lang('No Membership Status')</span>
                            <span class="quant-tier__amount">@lang('Invest to unlock your first tier')</span>
                        </div>
                        @php $first = \App\Lib\TierProgram::tiers()[0]; @endphp
                        <div class="quant-tier__progress">
                            <div class="quant-tier__progress-bar">
                                <span style="width: {{ $tierStanding->progress }}%; background: {{ $first['color'] }};"></span>
                            </div>
                            <div class="quant-tier__progress-meta">
                                <span>{{ $tierStanding->progress }}%</span>
                                <span>{{ showAmount($tierStanding->needed) }} {{ $general->cur_text }} @lang('to') {{ $first['emoji'] }} {{ __($first['name']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Invested strategies / programs --}}
    <div class="quant-panel quant-invested">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Your Strategies')</h5>
                <p class="quant-panel__desc">@lang('Programs you are currently invested in')</p>
            </div>
            <a href="{{ route('user.invest.statistics') }}" class="quant-panel__link">@lang('Details') <i class="las la-arrow-right"></i></a>
        </div>
        <div class="quant-panel__body">
            @if($investedStrategies->count())
                <div class="quant-invested__list">
                    @foreach($investedStrategies as $st)
                    <div class="quant-invested__row">
                        <div class="quant-invested__identity">
                            <span class="quant-invested__icon"><i class="las la-layer-group"></i></span>
                            <div class="quant-invested__heading">
                                <span class="quant-invested__name">{{ __($st->name) }}</span>
                                @if($st->frequency_label)
                                    <span class="quant-panel__tag">{{ $st->frequency_label }}</span>
                                @else
                                    <span class="quant-invested__type">@lang('Program')</span>
                                @endif
                            </div>
                        </div>
                        <div class="quant-invested__figure">
                            <span class="quant-invested__figure-label">@lang('Invested')</span>
                            <span class="quant-invested__amount">{{ showAmount($st->invested_amount ?: $st->running_amount) }} <small>{{ $general->cur_text }}</small></span>
                            @if($st->returns_amount > 0)
                                <span class="quant-invested__sub">+{{ showAmount($st->returns_amount) }} {{ $general->cur_text }} @lang('returns') · {{ showAmount($st->running_amount) }} @lang('value')</span>
                            @endif
                        </div>
                        <div class="quant-invested__meta">
                            @if($st->running_count)
                                <span class="quant-invested__status quant-invested__status--active">
                                    <span class="quant-live-dot quant-live-dot--active"></span>
                                    {{ $st->running_count }} @lang('active')
                                </span>
                            @endif
                            @if($st->completed_count)
                                <span class="quant-invested__status">
                                    <i class="las la-check-circle"></i> {{ $st->completed_count }} @lang('completed')
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="quant-invested__empty">
                    <i class="las la-seedling"></i>
                    <div>
                        <strong>@lang('No active investments yet')</strong>
                        <p class="mb-0">@lang('Explore our strategies and start investing.') <a href="{{ route('plan') }}">@lang('View strategies')</a></p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Main analytics row — portfolio return --}}
    <div class="row g-4 quant-main-row align-items-stretch">
        <div class="col-xl-8">
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Return Analytics')</h5>
                        <p class="quant-panel__desc">@lang('Approved period return % from your strategies (quarterly, 6-month, or yearly)')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="quant-panel quant-panel--allocation quant-panel--fill h-100">
                <div class="quant-panel__head quant-panel__head--compact quant-panel__head--with-icon">
                    <div class="quant-panel__head-icon quant-panel__head-icon--blue" aria-hidden="true">
                        <i class="las la-chart-pie"></i>
                    </div>
                    <div>
                        <h5 class="quant-panel__title">@lang('Asset Allocation')</h5>
                        <p class="quant-panel__desc">@lang('AI-managed multi-market exposure across global asset classes')</p>
                    </div>
                </div>
                <div class="quant-panel__body quant-panel__body--allocation">
                    <div class="quant-allocation-chart-wrap">
                        <div id="allocationChart"></div>
                    </div>

                    <p class="quant-allocation-footnote">
                        <i class="las la-info-circle"></i>
                        @lang('Allocation reflects Crownmaire Capital\'s quant model distribution. Positions are monitored and adjusted by the AI engine in real time.')
                    </p>

                    <a href="{{ route('user.portfolio') }}" class="quant-allocation-btn">
                        <i class="las la-chart-pie"></i> @lang('See allocation in detail')
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Capital flow --}}
    <div class="row g-4 quant-capital-row">
        <div class="col-lg-4 d-flex">
            <div class="quant-panel quant-panel--stat w-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Deposits')</h5>
                    <a href="{{ route('user.deposit.index') }}" class="quant-panel__link">@lang('Add') <i class="las la-arrow-right"></i></a>
                </div>
                <div class="quant-stat-hero">{{ showAmount($successfulDeposits) }} <small>{{ $general->cur_text }}</small></div>
                <div class="quant-stat-grid">
                    <div><span>@lang('Pending')</span><strong>{{ $general->cur_sym }}{{ showAmount($pendingDeposits) }}</strong></div>
                    <div><span>@lang('Rejected')</span><strong>{{ $general->cur_sym }}{{ showAmount($rejectedDeposits) }}</strong></div>
                    <div><span>@lang('Submitted')</span><strong>{{ $general->cur_sym }}{{ showAmount($submittedDeposits) }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-flex">
            <div class="quant-panel quant-panel--stat w-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Withdrawals')</h5>
                    <a href="{{ route('user.withdraw') }}" class="quant-panel__link">@lang('Request') <i class="las la-arrow-right"></i></a>
                </div>
                <div class="quant-stat-hero">{{ showAmount($successfulWithdrawals) }} <small>{{ $general->cur_text }}</small></div>
                <div class="quant-stat-grid">
                    <div><span>@lang('Pending')</span><strong>{{ $general->cur_sym }}{{ showAmount($pendingWithdrawals) }}</strong></div>
                    <div><span>@lang('Rejected')</span><strong>{{ $general->cur_sym }}{{ showAmount($rejectedWithdrawals) }}</strong></div>
                    <div><span>@lang('Submitted')</span><strong>{{ $general->cur_sym }}{{ showAmount($submittedWithdrawals) }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-flex">
            <div class="quant-panel quant-panel--stat w-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Portfolio Mix')</h5>
                    <a href="{{ route('user.invest.statistics') }}" class="quant-panel__link">@lang('Details') <i class="las la-arrow-right"></i></a>
                </div>
                <div class="quant-stat-grid quant-stat-grid--rows">
                    <div class="quant-stat-row">
                        <span>@lang('Running Strategies')</span>
                        <strong>{{ $general->cur_sym }}{{ showAmount($runningInvests) }}</strong>
                    </div>
                    <div class="quant-stat-row">
                        <span>@lang('Completed')</span>
                        <strong>{{ $general->cur_sym }}{{ showAmount($completedInvests) }}</strong>
                    </div>
                    <div class="quant-stat-row">
                        <span>@lang('Referral Earnings')</span>
                        <strong class="text--success">{{ $general->cur_sym }}{{ showAmount($referral_earnings) }}</strong>
                    </div>
                </div>
                <div class="quant-stat-foot">
                    <small>{{ $general->cur_sym }}{{ showAmount($depositWalletInvests) }} @lang('from deposit wallet') &middot; {{ $general->cur_sym }}{{ showAmount($interestWalletInvests) }} @lang('from returns wallet')</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Strategy charts + Recent activity --}}
    <div class="row g-4 quant-bottom-row">
        <div class="col-lg-7">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h6 class="mb-0">@lang('Strategy Performance') · {{ $strategyChartYear ?? date('Y') }}</h6>
                    <small class="text-muted">@lang('Current year approved performance')</small>
                </div>
                <a href="{{ route('user.strategy.performance') }}" class="quant-panel__link">@lang('All strategies') <i class="las la-arrow-right"></i></a>
            </div>
            @forelse($strategyCharts as $strategyChart)
            <div class="quant-panel {{ !$loop->last ? 'mb-4' : '' }}">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2 strategy-chart-head">
                        <div class="strategy-chart-head__left">
                            <h5 class="quant-panel__title mb-0">{{ __($strategyChart->plan_name) }}</h5>
                            <small class="text-muted">{{ $strategyChartYear ?? date('Y') }}</small>
                        </div>
                        <div class="strategy-chart-head__center text-center">
                            <span class="quant-panel__tag">{{ $strategyChart->frequency_label }}</span>
                        </div>
                        @if($strategyChart->point_count ?? $strategyChart->week_count)
                            <div class="strategy-chart-head__right text-end">
                                <small class="text-muted d-block">@lang('Return till date')</small>
                                <strong class="text-dark">{{ showAmount($strategyChart->ytd_percent) }}%</strong>
                            </div>
                        @else
                            <div class="strategy-chart-head__right"></div>
                        @endif
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="strategyChart{{ $loop->index }}" class="quant-strategy-chart"></div>
                </div>
            </div>
            @empty
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Strategy Performance')</h5>
                        <p class="quant-panel__desc">@lang('Strategy performance charts appear here once admin approves returns')</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="col-lg-5">
            <div class="quant-panel quant-panel--activity">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Recent Activity')</h5>
                        <p class="quant-panel__desc">@lang('Latest movements across your portfolio')</p>
                    </div>
                    <a href="{{ route('user.transactions') }}" class="quant-panel__link">@lang('View all') <i class="las la-arrow-right"></i></a>
                </div>
                <div class="quant-panel__body quant-panel__body--activity">
                    <div class="quant-activity">
                        @forelse($transactions as $trx)
                        <div class="quant-activity__item">
                            <div class="quant-activity__icon {{ $trx->trx_type == '+' ? 'quant-activity__icon--in' : 'quant-activity__icon--out' }}">
                                <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }}"></i>
                            </div>
                            <div class="quant-activity__info">
                                <span class="quant-activity__title">{{ __(keyToTitle($trx->remark)) }}</span>
                                <span class="quant-activity__meta">{{ $trx->created_at->format('M d, Y · H:i') }}</span>
                            </div>
                            <div class="quant-activity__amount {{ $trx->trx_type == '+' ? 'quant-activity__amount--in' : 'quant-activity__amount--out' }}">
                                {{ $trx->trx_type }}{{ showAmount($trx->amount) }}
                            </div>
                        </div>
                        @empty
                        <div class="quant-activity__empty">
                            <i class="las la-inbox"></i>
                            <p>@lang('No recent transactions')</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('style')
<style>
    .quant-header {
        align-items: flex-start;
    }
    .quant-header__aside {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
        flex-shrink: 0;
        width: auto;
    }
    .quant-header__aside .quant-header__meta {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 4px;
        width: auto;
    }
    .quant-header__aside .quant-date,
    .quant-header__aside .quant-badge--ai {
        flex-shrink: 0;
        white-space: nowrap;
    }
    .quant-header__ytd {
        margin: 0;
        border-radius: 12px;
        width: 100%;
        min-width: 300px;
        max-width: 340px;
    }
    .quant-header__ytd-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
    }
    .quant-header__ytd-label {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .quant-header__ytd-title {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: "Maven Pro", sans-serif;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .quant-header__ytd-label small {
        font-size: 0.75rem;
        color: #888;
        line-height: 1.3;
    }
    .quant-header__ytd-value {
        text-align: right;
        flex-shrink: 0;
    }
    .quant-header__ytd-value .quant-live-kpi__value {
        display: block;
        font-size: 1.625rem;
        line-height: 1.1;
    }
    .quant-header__ytd-value .quant-live-kpi__status {
        display: block;
        font-size: 0.6875rem;
        margin-top: 2px;
    }
    .quant-panel--allocation .quant-panel__body--allocation {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding-bottom: 20px;
        height: 100%;
        overflow: visible;
    }
    .quant-allocation-chart-wrap {
        flex-shrink: 0;
        min-height: 230px;
        height: auto;
        margin-bottom: 4px;
        overflow: hidden;
    }
    .quant-panel--allocation #allocationChart {
        min-height: 0;
        width: 100%;
    }
    .quant-allocation-footnote {
        margin: 0;
        padding: 12px 14px;
        font-size: 0.75rem;
        line-height: 1.55;
        color: #64748b;
        background: #fafbfc;
        border-radius: 10px;
        border-left: 3px solid #1989BE;
    }
    .quant-allocation-footnote i {
        color: #1989BE;
        margin-right: 4px;
    }
    .quant-allocation-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
        padding: 11px 16px;
        border-radius: 10px;
        background: #1989BE;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s ease;
    }
    .quant-allocation-btn:hover {
        background: #146a96;
        color: #fff;
    }
    .quant-main-row > .col-xl-4 {
        display: flex;
    }
    @media (max-width: 991px) {
        .quant-header__aside {
            align-items: stretch;
            width: 100%;
            max-width: none;
        }
        .quant-header__aside .quant-header__meta {
            justify-content: flex-start;
            flex-wrap: wrap;
        }
        .quant-header__ytd {
            min-width: 0;
            max-width: none;
        }
    }
    @media (max-width: 1199px) {
        .quant-allocation-chart-wrap {
            min-height: 250px;
        }
    }
    @media (max-width: 575px) {
        .quant-allocation-chart-wrap {
            min-height: 260px;
        }
        .quant-header__ytd-inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .quant-header__ytd-value {
            text-align: left;
            width: 100%;
        }
    }
    .quant-invested {
        margin-bottom: 24px;
    }
    .quant-invested__list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .quant-invested__row {
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid var(--quant-border, #e5e5e5);
        border-radius: 14px;
        padding: 16px 20px;
        background: #fff;
        transition: box-shadow .2s ease, border-color .2s ease;
    }
    .quant-invested__row:hover {
        border-color: rgba(25, 137, 190, 0.35);
        box-shadow: 0 6px 20px rgba(25, 137, 190, 0.08);
    }
    .quant-invested__identity {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1 1 auto;
        min-width: 0;
    }
    .quant-invested__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--quant-primary-light, #e8f4fa);
        color: var(--quant-primary, #1989BE);
        flex-shrink: 0;
    }
    .quant-invested__heading {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        min-width: 0;
    }
    .quant-invested__name {
        font-family: "Maven Pro", sans-serif;
        font-weight: 700;
        color: var(--quant-text, #1a1a1a);
        font-size: 1rem;
        line-height: 1.2;
    }
    .quant-invested__type {
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .quant-invested__figure {
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: right;
        flex-shrink: 0;
    }
    .quant-invested__figure-label {
        font-size: 0.6875rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .quant-invested__amount {
        font-family: "Maven Pro", sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--quant-text, #1a1a1a);
        line-height: 1.1;
    }
    .quant-invested__amount small {
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--quant-text-muted, #888);
    }
    .quant-invested__sub {
        display: block;
        margin-top: 4px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #16a34a;
    }
    .quant-invested__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 16px;
        flex-shrink: 0;
        min-width: 150px;
    }
    .quant-invested__status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #888;
        white-space: nowrap;
    }
    .quant-invested__status--active {
        color: var(--quant-profit, #16a34a);
    }
    .quant-invested__status i {
        font-size: 0.95rem;
    }
    @media (max-width: 575px) {
        .quant-invested__row {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .quant-invested__identity {
            width: 100%;
        }
        .quant-invested__figure {
            text-align: left;
        }
        .quant-invested__meta {
            justify-content: flex-start;
            min-width: 0;
        }
    }
    .quant-invested__empty {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--quant-text-muted, #64748b);
    }
    .quant-invested__empty i {
        font-size: 1.75rem;
        color: var(--quant-primary, #1989BE);
    }
    .quant-invested__empty p {
        font-size: 0.85rem;
    }
    .strategy-chart-head {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 12px;
    }
    .strategy-chart-head__center {
        white-space: nowrap;
    }
    .strategy-chart-head__right {
        justify-self: end;
    }
    @media (max-width: 575px) {
        .strategy-chart-head {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .strategy-chart-head__right {
            justify-self: center;
        }
    }
    .quant-tier-card {
        margin-bottom: 24px;
    }
    .quant-tier {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .quant-tier__badge {
        --tier-color: #1989BE;
        flex: 0 0 auto;
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--tier-color) 15%, transparent);
        border: 1px solid color-mix(in srgb, var(--tier-color) 35%, transparent);
    }
    .quant-tier__badge--muted {
        --tier-color: #94a3b8;
        background: rgba(148, 163, 184, 0.12);
        border-color: rgba(148, 163, 184, 0.3);
    }
    .quant-tier__emoji {
        font-size: 1.7rem;
        line-height: 1;
        color: var(--tier-color);
    }
    .quant-tier__info {
        flex: 1 1 auto;
        min-width: 0;
    }
    .quant-tier__head {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 4px 12px;
        margin-bottom: 8px;
    }
    .quant-tier__name {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--quant-text, #0f172a);
    }
    .quant-tier__amount {
        font-size: 0.82rem;
        color: var(--quant-text-muted, #64748b);
    }
    .quant-tier__top {
        font-size: 0.88rem;
        font-weight: 600;
        color: #d4a017;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .quant-tier__progress-bar {
        position: relative;
        height: 8px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.2);
        overflow: hidden;
    }
    .quant-tier__progress-bar span {
        position: absolute;
        inset: 0 auto 0 0;
        height: 100%;
        border-radius: 999px;
        transition: width 0.4s ease;
    }
    .quant-tier__progress-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 6px;
        font-size: 0.78rem;
        color: var(--quant-text-muted, #64748b);
    }
    .quant-tier__progress-meta span:first-child {
        font-weight: 600;
        color: var(--quant-text, #0f172a);
    }
    @media (max-width: 480px) {
        .quant-tier {
            align-items: flex-start;
            gap: 14px;
        }
    }
</style>
@endpush

@push('script')
<script src="{{ asset($activeTemplateTrue.'/js/lib/apexcharts.min.js') }}"></script>
<script>
const quantChartTheme = {
    primary: '#1989BE',
    profit: '#16a34a',
    loss: '#FF5252',
    grid: '#ebebeb',
    muted: '#999999'
};

const portfolioLabels = [@foreach($chartData as $cData)"{{ $cData->label }}",@endforeach];
const portfolioReturnPercents = [@foreach($chartData as $cData){{ getAmount($cData->return_percent) }},@endforeach];
const portfolioCumulativePercents = [@foreach($chartData as $cData){{ getAmount($cData->cumulative_percent) }},@endforeach];

var roiOptions = {
    chart: {
        height: 300,
        type: "area",
        toolbar: { show: false },
        fontFamily: 'Maven Pro, sans-serif',
        dropShadow: { enabled: true, enabledSeries: [0], top: -2, left: 0, blur: 8, opacity: 0.06 }
    },
    dataLabels: { enabled: false },
    series: [{ name: "@lang('Cumulative Return %')", data: portfolioCumulativePercents.length ? portfolioCumulativePercents : [] }],
    colors: [quantChartTheme.primary],
    fill: {
        type: "gradient",
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
    },
    stroke: { curve: 'smooth', width: 2 },
    markers: { size: 4, strokeColors: '#ffffff', strokeWidth: 2, hover: { size: 6 } },
    xaxis: {
        categories: portfolioLabels.length ? portfolioLabels : [],
        labels: {
            style: { colors: quantChartTheme.muted, fontSize: '11px' },
            rotate: -45,
            hideOverlappingLabels: true
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: {
            style: { colors: quantChartTheme.muted, fontSize: '11px' },
            formatter: v => v.toFixed(2) + '%'
        }
    },
    grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 8, right: 8 } },
    tooltip: {
        custom: function({ dataPointIndex }) {
            const periodPct = portfolioReturnPercents[dataPointIndex];
            const cumulative = portfolioCumulativePercents[dataPointIndex];
            const label = portfolioLabels[dataPointIndex];
            if (periodPct === undefined) return '';
            return '<div class="p-2"><strong>' + label + '</strong><br>@lang("Period return"): ' + periodPct.toFixed(2) + '%<br>@lang("Cumulative"): ' + cumulative.toFixed(2) + '%</div>';
        }
    },
    noData: { text: '@lang("Approved period payouts will appear here after admin approval")', align: 'center', style: { color: quantChartTheme.muted } }
};
if (document.querySelector("#chart")) {
    new ApexCharts(document.querySelector("#chart"), roiOptions).render();
}

@php
    $allocationData = ($allocations ?? collect())->map(function ($a) {
        return ['name' => $a->name, 'value' => (float) $a->percentage, 'color' => $a->color];
    });
    if ($allocationData->isEmpty()) {
        $allocationData = collect([
            ['name' => 'Forex',       'value' => 20, 'color' => '#1989BE'],
            ['name' => 'Indices',     'value' => 18, 'color' => '#14709a'],
            ['name' => 'Commodities', 'value' => 33, 'color' => '#47a8d4'],
            ['name' => 'Futures',     'value' => 15, 'color' => '#7fc4e8'],
            ['name' => 'Crypto',      'value' => 14, 'color' => '#b3dff5'],
        ]);
    }
@endphp
var allocationOptions = {
    chart: { type: 'donut', height: 230, fontFamily: 'Maven Pro, sans-serif' },
    labels: [@foreach($allocationData as $a)'{{ addslashes($a['name']) }}',@endforeach],
    series: [@foreach($allocationData as $a){{ $a['value'] }},@endforeach],
    colors: [@foreach($allocationData as $a)'{{ $a['color'] }}',@endforeach],
    legend: {
        position: 'bottom',
        fontSize: '11px',
        horizontalAlign: 'center',
        offsetY: 2,
        itemMargin: { horizontal: 6, vertical: 3 },
        labels: { colors: '#64748b' },
        markers: { width: 8, height: 8, radius: 2 }
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%',
                labels: { show: true, total: { show: true, label: 'Exposure', fontSize: '11px', color: '#94a3b8' } }
            }
        }
    },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: ['#fff'] }
};
if (document.querySelector("#allocationChart")) {
    new ApexCharts(document.querySelector("#allocationChart"), allocationOptions).render();
}

const strategyChartsData = [
@foreach($strategyCharts as $strategyChart)
    {
        rates: [@foreach($strategyChart->points as $point){{ getAmount($point->rate_percent) }},@endforeach],
        dates: [@foreach($strategyChart->points as $point)"{{ $point->date_label }}",@endforeach]
    },
@endforeach
];

function buildStrategyChartOptions(data) {
    return {
        chart: {
            height: 240,
            type: "area",
            toolbar: { show: false },
            fontFamily: 'Maven Pro, sans-serif',
            dropShadow: { enabled: true, enabledSeries: [0], top: -2, left: 0, blur: 8, opacity: 0.06 }
        },
        series: [{ name: '', data: data.rates.length ? data.rates : [] }],
        colors: [quantChartTheme.primary],
        fill: {
            type: "gradient",
            gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
        },
        stroke: { curve: 'smooth', width: 2 },
        markers: {
            size: data.rates.length > 0 ? 4 : 0,
            strokeWidth: 2,
            strokeColors: '#fff',
            hover: { size: 6 }
        },
        xaxis: {
            labels: { show: false },
            axisBorder: { show: false },
            axisTicks: { show: false },
            title: { show: false }
        },
        yaxis: {
            title: { show: false },
            labels: { formatter: v => v.toFixed(2) + '%', style: { colors: quantChartTheme.muted, fontSize: '11px' } }
        },
        grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 0, right: 0, top: 0, bottom: 0 } },
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: {
            custom: function({ seriesIndex, dataPointIndex, w }) {
                const rate = w.globals.series[seriesIndex][dataPointIndex];
                const dates = data.dates[dataPointIndex];
                if (rate === undefined) return '';
                return '<div class="p-2"><strong>' + dates + '</strong><br>' + rate.toFixed(2) + '%</div>';
            }
        },
        noData: { text: '@lang("No approved performance yet")', align: 'center', style: { color: quantChartTheme.muted } }
    };
}

strategyChartsData.forEach(function(data, index) {
    const el = document.querySelector("#strategyChart" + index);
    if (el) {
        new ApexCharts(el, buildStrategyChartOptions(data)).render();
    }
});

@if($isHoliday)
function createCountDown(elementId, sec) {
    var tms = sec;
    var x = setInterval(function () {
        var distance = tms * 1000;
        var d = Math.floor(distance / (1000 * 60 * 60 * 24));
        var h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById(elementId).innerHTML = d+'d '+h+'h '+m+'m '+s+'s';
        if (distance < 0) { clearInterval(x); document.getElementById(elementId).innerHTML = "COMPLETE"; }
        tms--;
    }, 1000);
}
createCountDown('counter', {{\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()}});
@endif
</script>
@endpush
