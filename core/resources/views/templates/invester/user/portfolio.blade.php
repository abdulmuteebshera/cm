@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $totalPercent = $allocations->sum('percentage');
@endphp
<div class="dashboard-inner quant-dashboard">

    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Portfolio Allocation')</h4>
            <p class="text-muted mb-0">@lang('How Crownmaire Capital deploys capital across global asset classes')</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    @if($allocations->count())
        <div class="row g-4 align-items-stretch">
            {{-- Donut chart --}}
            <div class="col-lg-5">
                <div class="quant-panel h-100">
                    <div class="quant-panel__head quant-panel__head--aligned">
                        <div>
                            <h5 class="quant-panel__title">@lang('Allocation Overview')</h5>
                            <p class="quant-panel__desc">@lang('AI-managed multi-market exposure')</p>
                        </div>
                    </div>
                    <div class="quant-panel__body">
                        <div id="portfolioChart"></div>
                    </div>
                </div>
            </div>

            {{-- Breakdown --}}
            <div class="col-lg-7">
                <div class="quant-panel h-100">
                    <div class="quant-panel__head quant-panel__head--aligned">
                        <div>
                            <h5 class="quant-panel__title">@lang('Asset Class Breakdown')</h5>
                            <p class="quant-panel__desc">@lang('Weighting across the strategy universe')</p>
                        </div>
                    </div>
                    <div class="quant-panel__body">
                        <div class="pf-list">
                            @foreach($allocations as $allocation)
                                <div class="pf-item">
                                    <div class="pf-item__head">
                                        <span class="pf-item__name">
                                            <span class="pf-dot" style="background: {{ $allocation->color }};"></span>
                                            {{ __($allocation->name) }}
                                        </span>
                                        <span class="pf-item__pct">{{ showAmount($allocation->percentage) }}%</span>
                                    </div>
                                    <div class="pf-bar">
                                        <span style="width: {{ min(100, $allocation->percentage) }}%; background: {{ $allocation->color }};"></span>
                                    </div>
                                    @if($allocation->description)
                                        <p class="pf-item__desc">{{ __($allocation->description) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-muted small mt-3 mb-0">
            <i class="las la-info-circle"></i>
            @lang('Allocation reflects Crownmaire Capital\'s current quant-model distribution and is actively monitored and rebalanced by the AI engine.')
        </p>
    @else
        <div class="quant-panel">
            <div class="quant-panel__body">
                <div class="pf-empty">
                    <i class="las la-chart-pie"></i>
                    <p class="mb-0">@lang('Portfolio allocation will be published here shortly.')</p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('style')
<style>
    .pf-list { display: flex; flex-direction: column; gap: 18px; }
    .pf-item__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .pf-item__name {
        display: flex;
        align-items: center;
        gap: 9px;
        font-weight: 600;
        color: var(--quant-text, #0f172a);
        font-size: 0.95rem;
    }
    .pf-dot { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }
    .pf-item__pct { font-weight: 700; color: var(--quant-text, #0f172a); }
    .pf-bar {
        height: 9px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.18);
        overflow: hidden;
    }
    .pf-bar span { display: block; height: 100%; border-radius: 999px; transition: width 0.5s ease; }
    .pf-item__desc { margin: 8px 0 0; font-size: 0.82rem; color: var(--quant-text-muted, #64748b); }
    .pf-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--quant-text-muted, #64748b);
    }
    .pf-empty i { font-size: 1.75rem; color: var(--quant-primary, #1989BE); }
</style>
@endpush

@push('script')
@if($allocations->count())
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
<script>
(function () {
    var options = {
        chart: { type: 'donut', height: 320, fontFamily: 'Maven Pro, sans-serif' },
        labels: [@foreach($allocations as $a)'{{ addslashes($a->name) }}',@endforeach],
        series: [@foreach($allocations as $a){{ getAmount($a->percentage) }},@endforeach],
        colors: [@foreach($allocations as $a)'{{ $a->color }}',@endforeach],
        legend: {
            position: 'bottom',
            fontSize: '12px',
            horizontalAlign: 'center',
            itemMargin: { horizontal: 8, vertical: 4 },
            labels: { colors: '#64748b' },
            markers: { width: 9, height: 9, radius: 3 }
        },
        plotOptions: {
            pie: { donut: { size: '64%', labels: { show: true, total: { show: true, label: 'Exposure', fontSize: '12px', color: '#94a3b8' } } } }
        },
        dataLabels: { enabled: true, formatter: function (val) { return val.toFixed(1) + '%'; } },
        stroke: { width: 2, colors: ['#fff'] },
        tooltip: { y: { formatter: function (val) { return val + '%'; } } }
    };
    var el = document.querySelector('#portfolioChart');
    if (el) { new ApexCharts(el, options).render(); }
})();
</script>
@endif
@endpush
