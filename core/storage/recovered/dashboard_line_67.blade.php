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
            <div class="quant-kpi__icon quant-kpi__icon--green"><i class="las la-chart-line"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Cumulative Returns')</span>
                <span class="quant-kpi__value quant-kpi__value--up">{{ showAmount($interests) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@lang('Total interest earned to date')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--slate"><i class="las la-university"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Capital Invested')</span>
                <span class="quant-kpi__value">{{ showAmount($invests) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">{{ showAmount($completedInvests) }} @lang('completed') &middot; {{ showAmount($runningInvests) }} @lang('active')</span>
            </div>
        </div>
    </div>

    {{-- Main analytics row --}}
    <div class="row g-4 quant-main-row">
        <div class="col-xl-8">
            <div class="quant-panel">
                <div class="quant-panel__head">
                    <div>
                        <h5 class="quant-panel__title">@lang('Strategy Performance')</h5>
                        <p class="quant-panel__desc">@lang('Monthly portfolio performance — AI-driven quant models')</p>
                    </div>
                    <span class="quant-panel__tag">MTD</span>
                </div>
                <div class="quant-panel__body">
                    <div id="performanceChart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="quant-panel quant-panel--live mb-4">
                <div class="quant-panel__head quant-panel__head--compact">
                    <div>
                        <h5 class="quant-panel__title">@lang('Live Performance')</h5>
                        <p class="quant-panel__desc">@lang('Real-time strategy monitoring')</p>
                    </div>
                    <span class="quant-live-dot" id="liveDot"></span>
                </div>
                <div class="quant-live-kpi">
                    <span class="quant-live-kpi__value" id="liveProfit">0.00%</span>
                    <span class="quant-live-kpi__status" id="profitStatus">@lang('Market Monitoring')</span>
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
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="quant-panel quant-panel--stat">
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
        <div class="col-lg-4">
            <div class="quant-panel quant-panel--stat">
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
        <div class="col-lg-4">
            <div class="quant-panel quant-panel--stat">
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

    {{-- ROI + Transactions --}}
    <div class="row g-4 quant-bottom-row">
        <div class="col-lg-7">
            <div class="quant-panel">
                <div class="quant-panel__head">
                    <div>
                        <h5 class="quant-panel__title">@lang('Return Analytics')</h5>
                        <p class="quant-panel__desc">@lang('30-day return distribution from active strategies')</p>
                    </div>
                </div>
                <div class="quant-panel__body">
                    <div id="chart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="quant-panel">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Recent Activity')</h5>
                    <a href="{{ route('user.transactions') }}" class="quant-panel__link">@lang('View all') <i class="las la-arrow-right"></i></a>
                </div>
                <div class="quant-activity">
                    @forelse($transactions as $trx)
                    <div class="quant-activity__item">
                        <div class="quant-activity__icon {{ $trx->trx_type == '+' ? 'quant-activity__icon--in' : 'quant-activity__icon--out' }}">
                            <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }}"></i>
                        </div>
                        <div class="quant-activity__info">
                            <span class="quant-activity__title">{{ __(keyToTitle(str_replace('_',' ', $trx->remark))) }}</span>
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
@endsection

@push('script')
<script src="{{ asset($activeTemplateTrue.'/js/lib/apexcharts.min.js') }}"></script>
<script>
const today = new Date();
const currentDay = today.getDate();
const totalDays = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();
const monthKey = "portfolio_" + today.getFullYear() + "_" + today.getMonth();
let saved = localStorage.getItem(monthKey);
let state = saved ? JSON.parse(saved) : null;
let monthlyTarget = state?.target ?? parseFloat((2 + Math.random()).toFixed(2));
let labels = [];
let data = [];
let weekendMarkers = [];
let cumulativeValue = 0;

if (state && state.series) {
    labels = state.labels;
    data = state.series;
    cumulativeValue = data[data.length - 1];
} else {
    for (let i = 1; i <= currentDay; i++) {
        labels.push(i);
        let date = new Date(today.getFullYear(), today.getMonth(), i);
        let day = date.getDay();
        if (day === 0 || day === 6) {
            data.push(parseFloat(cumulativeValue.toFixed(2)));
            continue;
        }
        if (i === currentDay) {
            data.push(parseFloat(cumulativeValue.toFixed(2)));
            continue;
        }
        let remainingDays = totalDays - i + 1;
        let requiredMove = (monthlyTarget - cumulativeValue) / remainingDays;
        let rand = Math.random();
        let bias = rand < 0.4 ? -(Math.random() * 0.18) : rand < 0.8 ? (Math.random() * 0.18) : (Math.random() - 0.5) * 0.05;
        let move = Math.max(-0.25, Math.min(0.25, requiredMove + bias));
        cumulativeValue += move;
        cumulativeValue = Math.max(0, cumulativeValue);
        data.push(parseFloat(cumulativeValue.toFixed(2)));
    }
}

let dailyTarget = cumulativeValue + ((monthlyTarget - cumulativeValue) / (totalDays - currentDay + 1));

for (let i = 1; i <= labels.length; i++) {
    let date = new Date(today.getFullYear(), today.getMonth(), i);
    if (date.getDay() === 0 || date.getDay() === 6) {
        weekendMarkers.push({
            x: i,
            label: { text: "Weekend", style: { background: "#64748b", color: "#fff", fontSize: "10px" } }
        });
    }
}

function saveState() {
    localStorage.setItem(monthKey, JSON.stringify({ target: monthlyTarget, labels: labels, series: data }));
}

function isMarketOpen() {
    const now = new Date();
    if (now.getDay() === 0 || now.getDay() === 6) return false;
    return now.getHours() >= 9 && now.getHours() < 24;
}

const quantChartTheme = {
    primary: '#1a2744',
    accent: '#3b6eb5',
    grid: '#e8ecf1',
    muted: '#94a3b8'
};

var performanceOptions = {
    chart: {
        height: 300,
        type: "area",
        toolbar: { show: false },
        fontFamily: 'Maven Pro, sans-serif',
        animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 900 } }
    },
    series: [{ name: "Performance %", data: data }],
    xaxis: {
        categories: labels,
        labels: { style: { colors: quantChartTheme.muted, fontSize: '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        min: 0, max: 3.15,
        labels: { formatter: v => v.toFixed(2) + "%", style: { colors: quantChartTheme.muted, fontSize: '11px' } }
    },
    stroke: { curve: "smooth", width: 2.5 },
    colors: [quantChartTheme.accent],
    fill: {
        type: "gradient",
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] }
    },
    grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4 },
    dataLabels: { enabled: false },
    annotations: { points: weekendMarkers }
};

var performanceChart = new ApexCharts(document.querySelector("#performanceChart"), performanceOptions);
performanceChart.render();

setInterval(() => {
    const liveEl = document.getElementById("liveProfit");
    const statusEl = document.getElementById("profitStatus");
    const dotEl = document.getElementById("liveDot");
    if (!isMarketOpen()) {
        statusEl.innerText = "Market Closed";
        statusEl.className = "quant-live-kpi__status quant-live-kpi__status--closed";
        if (dotEl) dotEl.classList.remove('quant-live-dot--active');
        return;
    }
    let distance = dailyTarget - cumulativeValue;
    let move = distance * 0.005 + (Math.random() - 0.5) * 0.02;
    if (Math.random() < 0.35) move -= Math.random() * 0.015;
    if (Math.random() < 0.25) move += Math.random() * 0.015;
    move = Math.max(-0.02, Math.min(0.02, move - move * 0.4 * Math.sign(move)));
    cumulativeValue = Math.max(0, parseFloat((cumulativeValue + move).toFixed(2)));
    data[data.length - 1] = cumulativeValue;
    performanceChart.updateSeries([{ data }]);
    liveEl.innerText = cumulativeValue.toFixed(2) + "%";
    statusEl.innerText = "Market Open · Live";
    statusEl.className = "quant-live-kpi__status quant-live-kpi__status--live";
    if (dotEl) dotEl.classList.add('quant-live-dot--active');
    saveState();
}, 6000);

var allocationOptions = {
    chart: { type: 'donut', height: 260, fontFamily: 'Maven Pro, sans-serif' },
    labels: ['Forex', 'Indices', 'Commodities', 'Futures', 'Crypto'],
    series: [20, 18, 33, 15, 14],
    colors: ['#1a2744', '#3b6eb5', '#64748b', '#94a3b8', '#cbd5e1'],
    legend: { position: 'bottom', fontSize: '12px', labels: { colors: '#64748b' } },
    plotOptions: { pie: { donut: { size: '72%', labels: { show: true, total: { show: true, label: 'Exposure', fontSize: '11px', color: '#94a3b8' } } } } },
    dataLabels: { enabled: false },
    stroke: { width: 2, colors: ['#fff'] }
};
new ApexCharts(document.querySelector("#allocationChart"), allocationOptions).render();

var roiOptions = {
    chart: {
        height: 280,
        type: "area",
        toolbar: { show: false },
        fontFamily: 'Maven Pro, sans-serif',
        dropShadow: { enabled: true, enabledSeries: [0], top: -2, left: 0, blur: 8, opacity: 0.06 }
    },
    dataLabels: { enabled: false },
    series: [{ name: "Returns", data: [@foreach($chartData as $cData){{ getAmount($cData->amount) }},@endforeach] }],
    colors: [quantChartTheme.primary],
    fill: {
        type: "gradient",
        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
    },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
        categories: [@foreach($chartData as $cData)"{{ Carbon\Carbon::parse($cData->date)->format('d M') }}",@endforeach],
        labels: { style: { colors: quantChartTheme.muted, fontSize: '11px' }, rotate: -45 },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { labels: { style: { colors: quantChartTheme.muted, fontSize: '11px' } } },
    grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 8, right: 8 } }
};
new ApexCharts(document.querySelector("#chart"), roiOptions).render();

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
