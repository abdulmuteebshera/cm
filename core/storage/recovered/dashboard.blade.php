@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $welcomeName    = $user->firstname ?: strtok(trim($user->fullname), ' ') ?: $user->username;
    $portfolioValue = $user->deposit_wallet + $user->interest_wallet + $runningInvests;
    $todayLabel     = now()->format('l, F j, Y');
@endphp

<div class="dashboard-inner quant-dashboard">

    @if($user->deposit_wallet <= 0 && $user->interest_wallet <= 0)
        <div class="alert border border--danger mb-4" role="alert">
            <div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-exclamation-triangle"></i></div>
            <p class="alert__message mb-0">
                <span class="fw-bold">@lang('Empty Balance')</span><br>
                <small><i>@lang('Your balance is empty. Please make') <a href="{{ route('user.deposit.index') }}" class="link-color">@lang('deposit')</a> @lang('for your next investment.')</i></small>
            </p>
        </div>
    @endif

    <div class="quant-header">
        <div class="quant-header__main">
            <div class="dashboard-welcome dashboard-welcome--compact">
                <div class="dashboard-welcome__accent" aria-hidden="true"></div>
                <div class="dashboard-welcome__content">
                    <p class="dashboard-welcome__text">
                        @lang('Hi') <span class="dashboard-welcome__name">{{ $welcomeName }}</span>,
                        @lang('welcome to') <span class="dashboard-welcome__brand">Crownmaire Capital</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="quant-header__meta">
            <span class="quant-date"><i class="las la-calendar"></i> {{ $todayLabel }}</span>
        </div>
    </div>

    <div class="quant-kpi-grid mb-4">
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-wallet"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Portfolio Value')</span>
                <span class="quant-kpi__value">{{ $general->cur_sym }}{{ showAmount($portfolioValue) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-pie"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Active Investment')</span>
                <span class="quant-kpi__value">{{ $general->cur_sym }}{{ showAmount($runningInvests) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-hand-holding-usd"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Return Till Date')</span>
                <span class="quant-kpi__value {{ $yearToDateReturn >= 0 ? 'quant-kpi__value--up' : 'quant-kpi__value--down' }}">{{ $general->cur_sym }}{{ showAmount($yearToDateReturn) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-piggy-bank"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Interest Wallet')</span>
                <span class="quant-kpi__value">{{ $general->cur_sym }}{{ showAmount($user->interest_wallet) }}</span>
            </div>
        </div>
    </div>

    @if($strategyCharts->count())
        <div class="quant-panel mb-4">
            <div class="quant-panel__head quant-panel__head--aligned">
                <div>
                    <h5 class="quant-panel__title">@lang('Strategy Performance')</h5>
                    <p class="quant-panel__desc mb-0">@lang('Approved weekly returns — chart only until period payout is approved')</p>
                </div>
                <a href="{{ route('plan') }}" class="quant-panel__link">@lang('Invest') <i class="las la-arrow-right"></i></a>
            </div>
            <div class="quant-panel__body">
                <div class="row g-4">
                    @foreach($strategyCharts as $strategyChart)
                        @php $plan = $strategyChart['plan']; @endphp
                        <div class="col-xl-4 col-lg-6">
                            <div class="quant-strategy-chart-card">
                                <div class="quant-strategy-chart-card__head">
                                    <div>
                                        <h6 class="mb-1">{{ __($plan->name) }}</h6>
                                        <small class="text-muted">{{ $plan->payoutFrequencyLabel() }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="quant-strategy-chart-card__ytd {{ $strategyChart['ytd'] >= 0 ? 'is-profit' : 'is-loss' }}">{{ showAmount($strategyChart['ytd']) }}% YTD</span>
                                        @if($strategyChart['avg_weekly'])
                                            <small class="d-block text-muted">~{{ showAmount($strategyChart['avg_weekly']) }}% @lang('avg/wk')</small>
                                        @endif
                                    </div>
                                </div>
                                <div id="strategyChart{{ $plan->id }}" class="quant-strategy-chart"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 quant-main-row mb-4">
        <div class="col-xl-8">
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Return Analytics')</h5>
                        <p class="quant-panel__desc mb-0">@lang('Your approved strategy payouts this year')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="portfolioChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="quant-panel quant-panel--live h-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Quick Actions')</h5>
                </div>
                <div class="quant-panel__body">
                    <div class="quant-quick-actions">
                        <a href="{{ route('plan') }}" class="quant-quick-actions__item"><i class="las la-chart-line"></i> @lang('Invest in Strategy')</a>
                        <a href="{{ route('user.deposit.index') }}" class="quant-quick-actions__item"><i class="las la-plus-circle"></i> @lang('Deposit Funds')</a>
                        <a href="{{ route('user.withdraw') }}" class="quant-quick-actions__item"><i class="las la-money-bill-wave"></i> @lang('Withdraw')</a>
                        <a href="{{ route('user.invest.statistics') }}" class="quant-quick-actions__item"><i class="las la-briefcase"></i> @lang('My Portfolio')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 quant-bottom-row align-items-stretch">
        <div class="col-lg-7 d-flex">
            <div class="quant-panel quant-panel--fill w-100">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Interest History')</h5>
                        <p class="quant-panel__desc mb-0">@lang('30-day credited returns')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 d-flex">
            <div class="quant-panel quant-panel--fill w-100">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Recent Activity')</h5>
                        <p class="quant-panel__desc mb-0">@lang('Latest wallet transactions')</p>
                    </div>
                    <a href="{{ route('user.transactions') }}" class="quant-panel__link">@lang('View all')</a>
                </div>
                <div class="quant-panel__body quant-activity-list">
                    @forelse($transactions as $trx)
                        <div class="quant-activity">
                            <div class="quant-activity__main">
                                <span class="quant-activity__title">{{ __(keyToTitle($trx->remark)) }}</span>
                                <span class="quant-activity__date">{{ showDateTime($trx->created_at, 'M d, Y') }}</span>
                            </div>
                            <span class="quant-activity__amount {{ $trx->trx_type == '+' ? 'is-credit' : 'is-debit' }}">
                                {{ $trx->trx_type }}{{ $general->cur_sym }}{{ showAmount($trx->amount) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">@lang('No transactions yet')</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset($activeTemplateTrue.'/js/lib/apexcharts.min.js') }}"></script>
<script>
(function() {
    const quantChartTheme = {
        primary: '#1989BE',
        profit: '#16a34a',
        loss: '#FF5252',
        grid: '#ebebeb',
        muted: '#999999'
    };

    @foreach($strategyCharts as $strategyChart)
        @php
            $points = $strategyChart['points'];
            $planId = $strategyChart['plan']->id;
        @endphp
        (function() {
            var el = document.querySelector('#strategyChart{{ $planId }}');
            if (!el) return;
            var labels = @json($points->pluck('label'));
            var series = @json($points->pluck('return_percent'));
            if (!labels.length) {
                el.innerHTML = '<p class="text-muted text-center py-4 mb-0">@lang('No approved weekly data yet')</p>';
                return;
            }
            new ApexCharts(el, {
                chart: { type: 'area', height: 200, sparkline: { enabled: false }, toolbar: { show: false } },
                series: [{ name: '@lang('Weekly %')', data: series }],
                xaxis: { categories: labels, labels: { rotate: -45, style: { fontSize: '10px' } } },
                yaxis: { labels: { formatter: v => v.toFixed(2) + '%' } },
                stroke: { curve: 'smooth', width: 2 },
                colors: [quantChartTheme.primary],
                fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
                dataLabels: { enabled: false },
                grid: { borderColor: quantChartTheme.grid }
            }).render();
        })();
    @endforeach

    var portfolioEl = document.querySelector('#portfolioChart');
    if (portfolioEl) {
        var portfolioLabels = @json($portfolioChart['labels'] ?? []);
        var portfolioSeries = @json($portfolioChart['series']['cumulative_returns'] ?? []);
        if (portfolioLabels.length && portfolioSeries.some(v => v > 0)) {
            new ApexCharts(portfolioEl, {
                chart: { type: 'bar', height: 320, toolbar: { show: false } },
                series: [{ name: '@lang('Approved Payouts')', data: portfolioSeries }],
                xaxis: { categories: portfolioLabels },
                yaxis: { labels: { formatter: v => '{{ $general->cur_sym }}' + v.toFixed(2) } },
                colors: [quantChartTheme.primary],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                grid: { borderColor: quantChartTheme.grid }
            }).render();
        } else {
            portfolioEl.innerHTML = '<p class="text-muted text-center py-5 mb-0">@lang('Approved period payouts will appear here')</p>';
        }
    }

    var interestEl = document.querySelector('#chart');
    if (interestEl) {
        new ApexCharts(interestEl, {
            chart: { height: 320, type: 'area', toolbar: { show: false } },
            series: [{ name: '@lang('Interest')', data: [@foreach($chartData as $cData){{ getAmount($cData->amount) }},@endforeach] }],
            xaxis: { categories: [@foreach($chartData as $cData)"{{ Carbon\Carbon::parse($cData->date)->format('d M') }}",@endforeach] },
            stroke: { curve: 'smooth', width: 2 },
            colors: [quantChartTheme.primary],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
            dataLabels: { enabled: false },
            grid: { borderColor: quantChartTheme.grid }
        }).render();
    }
})();
</script>
@endpush
