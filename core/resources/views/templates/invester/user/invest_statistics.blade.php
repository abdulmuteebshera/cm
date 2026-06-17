@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $totalInvest = auth()->user()->invests->sum('amount');
    $totalProfit = auth()->user()->transactions()->where('remark','interest')->sum('amount');
@endphp
<div class="dashboard-inner quant-dashboard quant-invest-page">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <h3 class="quant-invest-title">@lang('Investment Portfolio')</h3>
            <p class="quant-invest-sub">@lang('Track active strategies, weekly returns, and capital deployed across your account.')</p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            <a href="{{ route('plan') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">
                <i class="las la-plus"></i> @lang('New Investment')
            </a>
        </div>
    </div>

    <div class="quant-kpi-grid quant-kpi-grid--3 mb-4">
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-pie"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Total Deployed')</span>
                <span class="quant-kpi__value">{{ showAmount($totalInvest) }} <small>{{ $general->cur_text }}</small></span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-line"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Total Returns')</span>
                <span class="quant-kpi__value">{{ showAmount($totalProfit) }} <small>{{ $general->cur_text }}</small></span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-play-circle"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Active Strategies')</span>
                <span class="quant-kpi__value">{{ $activePlan }}</span>
            </div>
        </div>
    </div>

    @if($investChart->count())
        <div class="quant-panel mb-4">
            <div class="quant-panel__head quant-panel__head--compact">
                <h5 class="quant-panel__title">@lang('Allocation by Strategy')</h5>
            </div>
            <div class="quant-panel__body">
                <div class="row align-items-center g-4">
                    <div class="col-md-5">
                        <div class="quant-allocation-list">
                            @foreach($investChart as $chart)
                                @php $pct = showAmount(($chart->investAmount / max($investChart->sum('investAmount'), 1)) * 100); @endphp
                                <div class="quant-allocation-list__item">
                                    <span class="quant-allocation-dot"></span>
                                    <span class="quant-allocation-list__name">{{ __($chart->plan->name) }}</span>
                                    <strong>{{ $pct }}%</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="quant-allocation-chart-wrap">
                            <canvas height="180" id="chartjs-pie-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="quant-panel">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Active Investments')</h5>
                <p class="quant-panel__desc mb-0">{{ $activePlan }} @lang('running') · <a href="{{ route('user.invest.log') }}">@lang('Full history')</a></p>
            </div>
        </div>
        <div class="quant-panel__body">
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
                backgroundColor: ['#1989BE','#14709a','#47a8d4','#7fc4e8','#b3dff5','#0d5a7a'],
                borderWidth: 0
            }],
            labels: [@foreach($investChart as $chart)'{{ __($chart->plan->name) }}',@endforeach]
        },
        options: { cutout: '65%', plugins: { legend: { display: false } } }
    });
})();
</script>
@endif
@endpush
