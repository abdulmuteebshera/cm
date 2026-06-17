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
        <div class="quant-header__meta">
            <span class="quant-date"><i class="las la-calendar"></i> {{ $todayLabel }}</span>
            <span class="quant-badge quant-badge--ai"><i class="las la-microchip"></i> AI Quant Engine</span>
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

    {{-- Main analytics row — portfolio return --}}
    <div class="row g-4 quant-main-row">
        <div class="col-xl-8">
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">@lang('Return Analytics')</h5>
                        <p class="quant-panel__desc">@lang('Portfolio growth from approved returns after your investment start date')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="quant-panel quant-panel--live mb-4">
                <div class="quant-panel__head quant-panel__head--compact">
                    <div>
                        <h5 class="quant-panel__title">@lang('Return Till Date')</h5>
                        <p class="quant-panel__desc">@lang('Your approved strategy payouts this year')</p>
                    </div>
                    @if($yearToDateReturn)
                        <span class="quant-live-dot quant-live-dot--active"></span>
                    @endif
                </div>
                <div class="quant-live-kpi">
                    @if($yearToDateReturn)
                        <span class="quant-live-kpi__value {{ $yearToDateReturn->total_amount >= 0 ? '' : 'quant-live-kpi__value--loss' }}">{{ $general->cur_sym }}{{ showAmount($yearToDateReturn->total_amount) }}</span>
                        <span class="quant-live-kpi__status {{ $yearToDateReturn->total_amount >= 0 ? 'quant-live-kpi__status--live' : 'quant-live-kpi__status--closed' }}">{{ $yearToDateReturn->payout_count }} @lang('approved payouts') · {{ $yearToDateReturn->year }}</span>
                    @else
                        <span class="quant-live-kpi__value">—</span>
                        <span class="quant-live-kpi__status">@lang('No approved payouts yet this year')</span>
                    @endif
                </div>
            </div>
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--compact">
                    <div>
                        <h5 class="quant-panel__title">@lang('Asset Allocation')</h5>
                        <p class="quant-panel__desc">@lang('Multi-strategy exposure')</p>
                    </div>
                </div>
                <div class="quant-panel__body quant-panel__body--compact">
                    <div id="allocationChart"></div>
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
            @forelse($strategyCharts as $strategyChart)
            <div class="quant-panel {{ !$loop->last ? 'mb-4' : '' }}">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div>
                        <h5 class="quant-panel__title">{{ __($strategyChart->plan_name) }}</h5>
                        <p class="quant-panel__desc">
                            @lang('Approved weekly return %')
                            @if($strategyChart->week_count)
                                · {{ showAmount($strategyChart->ytd_percent) }}% @lang('YTD')
                            @endif
                        </p>
                    </div>
                    <span class="quant-panel__tag">{{ $strategyChart->frequency_label }}</span>
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
                        <p class="quant-panel__desc">@lang('Strategy performance charts appear here once admin approves weekly returns')</p>
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
const portfolioValues = [@foreach($chartData as $cData){{ getAmount($cData->portfolio_value) }},@endforeach];
const portfolioWeeklyReturns = [@foreach($chartData as $cData){{ getAmount($cData->weekly_return) }},@endforeach];

var roiOptions = {
    chart: {
        height: 300,
        type: "area",
        toolbar: { show: false },
        fontFamily: 'Maven Pro, sans-serif',
        dropShadow: { enabled: true, enabledSeries: [0], top: -2, left: 0, blur: 8, opacity: 0.06 }
    },
    dataLabels: { enabled: false },
    series: [{ name: "@lang('Portfolio Value')", data: portfolioValues.length ? portfolioValues : [] }],
    colors: [quantChartTheme.primary],
    fill: {
        type: "gradient",
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
    },
    stroke: { curve: 'smooth', width: 2 },
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
        title: { text: '@lang("Portfolio") ({{ $general->cur_text }})', style: { color: quantChartTheme.muted, fontSize: '11px' } },
        labels: { style: { colors: quantChartTheme.muted, fontSize: '11px' } }
    },
    grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 8, right: 8 } },
    tooltip: {
        custom: function({ dataPointIndex }) {
            const value = portfolioValues[dataPointIndex];
            const weekly = portfolioWeeklyReturns[dataPointIndex];
            const label = portfolioLabels[dataPointIndex];
            if (value === undefined) return '';
            let html = '<div class="p-2"><strong>' + label + '</strong><br>{{ $general->cur_sym }}' + value.toFixed(2);
            if (weekly !== 0) {
                html += '<br>@lang("Week return"): {{ $general->cur_sym }}' + weekly.toFixed(2);
            }
            return html + '</div>';
        }
    },
    noData: { text: '@lang("No approved returns after your investment start date")', align: 'center', style: { color: quantChartTheme.muted } }
};
if (document.querySelector("#chart")) {
    new ApexCharts(document.querySelector("#chart"), roiOptions).render();
}

var allocationOptions = {
    chart: { type: 'donut', height: 260, fontFamily: 'Maven Pro, sans-serif' },
    labels: ['Forex', 'Indices', 'Commodities', 'Futures', 'Crypto'],
    series: [20, 18, 33, 15, 14],
    colors: ['#1989BE', '#14709a', '#47a8d4', '#7fc4e8', '#b3dff5'],
    legend: { position: 'bottom', fontSize: '12px', labels: { colors: '#64748b' } },
    plotOptions: { pie: { donut: { size: '72%', labels: { show: true, total: { show: true, label: 'Exposure', fontSize: '11px', color: '#94a3b8' } } } } },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: ['#fff'] }
};
new ApexCharts(document.querySelector("#allocationChart"), allocationOptions).render();

const strategyChartsData = [
@foreach($strategyCharts as $strategyChart)
    {
        rates: [@foreach($strategyChart->points as $point){{ getAmount($point->rate_percent) }},@endforeach],
        weeks: [@foreach($strategyChart->points as $point){{ $point->week }},@endforeach],
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
        series: [{ name: "@lang('Weekly Return %')", data: data.rates.length ? data.rates : [] }],
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
            title: { text: '@lang("Return %")', style: { color: quantChartTheme.muted, fontSize: '11px' } },
            labels: { formatter: v => v.toFixed(2) + '%', style: { colors: quantChartTheme.muted, fontSize: '11px' } }
        },
        grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 8, right: 16 } },
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: {
            custom: function({ seriesIndex, dataPointIndex, w }) {
                const rate = w.globals.series[seriesIndex][dataPointIndex];
                const weekNum = data.weeks[dataPointIndex];
                const dates = data.dates[dataPointIndex];
                if (rate === undefined) return '';
                return '<div class="p-2"><strong>W' + weekNum + '</strong>' + (dates ? '<br>' + dates : '') + '<br>' + rate.toFixed(2) + '%</div>';
            }
        },
        noData: { text: '@lang("No approved weekly returns yet")', align: 'center', style: { color: quantChartTheme.muted } }
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
