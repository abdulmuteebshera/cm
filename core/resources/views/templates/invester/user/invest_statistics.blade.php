@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $totalInvest = auth()->user()->invests->sum('initial_amount');
    $totalProfit = auth()->user()->transactions()->where('remark','interest')->sum('amount');
    $portfolioValue = (float) auth()->user()->invests->where('status', 1)->sum('amount');
    $returnPct = $totalInvest > 0 ? round(($totalProfit / $totalInvest) * 100, 2) : 0;
    $chartColors = ['#1989BE', '#14709a', '#47a8d4', '#7fc4e8', '#b3dff5', '#0d5a7a'];
@endphp
<div class="dashboard-inner quant-dashboard quant-invest-page">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <h3 class="quant-invest-title">@lang('Investment Portfolio')</h3>
            <p class="quant-invest-sub">@lang('Track active strategies, returns earned, and capital deployed across your Crownmaire account.')</p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            <a href="{{ route('plan') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">
                <i class="las la-plus"></i> @lang('New Investment')
            </a>
        </div>
    </div>

    <div class="quant-kpi-grid quant-kpi-grid--invest mb-4">
        <div class="quant-kpi quant-kpi--primary">
            <div class="quant-kpi__icon"><i class="las la-wallet"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Total Deployed')</span>
                <span class="quant-kpi__value">{{ showAmount($totalInvest) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@lang('Principal invested')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-line"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Total Returns')</span>
                <span class="quant-kpi__value">{{ showAmount($totalProfit) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@if($returnPct > 0)+{{ showAmount($returnPct) }}% @lang('on capital')@else @lang('Returns earned to date')@endif</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-layer-group"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Portfolio Value')</span>
                <span class="quant-kpi__value">{{ showAmount($portfolioValue) }} <small>{{ $general->cur_text }}</small></span>
                <span class="quant-kpi__sub">@lang('Active strategies compounded')</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-play-circle"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Active Strategies')</span>
                <span class="quant-kpi__value">{{ $activePlan }}</span>
                <span class="quant-kpi__sub">@lang('Currently running')</span>
            </div>
        </div>
    </div>

    @if($investChart->count())
        <div class="quant-panel quant-panel--allocation mb-4">
            <div class="quant-panel__head quant-panel__head--compact quant-panel__head--with-icon">
                <div class="quant-panel__head-icon quant-panel__head-icon--blue" aria-hidden="true">
                    <i class="las la-chart-pie"></i>
                </div>
                <div>
                    <h5 class="quant-panel__title">@lang('Allocation by Strategy')</h5>
                    <p class="quant-panel__desc mb-0">@lang('How your deployed capital is split across strategies')</p>
                </div>
            </div>
            <div class="quant-panel__body quant-panel__body--allocation">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5 order-lg-1 order-2">
                        <div class="quant-allocation-list quant-allocation-list--invest">
                            @php $chartTotal = max($investChart->sum('investAmount'), 1); @endphp
                            @foreach($investChart as $i => $chart)
                                @php $pct = round(($chart->investAmount / $chartTotal) * 100, 1); @endphp
                                <div class="quant-allocation-list__item">
                                    <span class="quant-allocation-dot" style="background: {{ $chartColors[$i % count($chartColors)] }}"></span>
                                    <span class="quant-allocation-list__name">{{ __($chart->plan->name) }}</span>
                                    <strong>{{ $pct }}%</strong>
                                    <small>{{ $general->cur_sym }}{{ showAmount($chart->investAmount) }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-7 order-lg-2 order-1">
                        <div class="quant-allocation-chart-wrap quant-allocation-chart-wrap--invest">
                            <canvas height="220" id="chartjs-pie-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="quant-panel quant-panel--portfolio">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Your Investments')</h5>
                <p class="quant-panel__desc mb-0">
                    {{ $activePlan }} {{ $activePlan == 1 ? __('active strategy') : __('active strategies') }} ·
                    <a href="{{ route('user.invest.log') }}">@lang('Full history')</a>
                </p>
            </div>
            <a href="{{ route('plan') }}" class="quant-panel__link d-none d-md-inline-flex">
                @lang('Add strategy') <i class="las la-plus"></i>
            </a>
        </div>
        <div class="quant-panel__body quant-panel__body--portfolio">
            @include($activeTemplate.'partials.invest_history',['invests'=>$invests])
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/chart.min.js') }}"></script>
@if($investChart->count())
<script>
(function() {
    var ctx = document.getElementById('chartjs-pie-chart');
    if (!ctx) return;
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [@foreach($investChart as $chart){{ $chart->investAmount }},@endforeach],
                backgroundColor: [@foreach($investChart as $i => $chart)'{{ $chartColors[$i % count($chartColors)] }}',@endforeach],
                borderWidth: 0,
                hoverOffset: 6
            }],
            labels: [@foreach($investChart as $chart)'{{ __($chart->plan->name) }}',@endforeach]
        },
        options: {
            cutout: '68%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
})();
</script>
@endif
@endpush
