@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard">
    <div class="quant-header mb-4">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Strategy Performance')</h4>
            @if($selectedPlan)
                <p class="text-muted mb-0">{{ __($selectedPlan->name) }} · @lang('Performance history by year')</p>
            @else
                <p class="text-muted mb-0">@lang('Select a strategy to view performance across all years since 2022.')</p>
            @endif
        </div>
        <div class="quant-header__meta">
            @if($selectedPlan)
                <a href="{{ route('user.strategy.performance') }}" class="quant-panel__link">@lang('All Strategies') <i class="las la-arrow-left"></i></a>
            @else
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            @endif
        </div>
    </div>

    @if(!$selectedPlan)
        <div class="row g-4">
            @forelse($plans as $plan)
                <div class="col-lg-4 col-md-6">
                    <a href="{{ route('user.strategy.performance', ['plan' => $plan->id]) }}" class="text-decoration-none">
                        <div class="quant-panel h-100 strategy-select-card">
                            <div class="quant-panel__body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1 text-dark">{{ __($plan->name) }}</h5>
                                        <small class="text-muted">@lang('View yearly performance and reports')</small>
                                    </div>
                                    <span class="quant-panel__tag">{{ $plan->payoutFrequencyLabel() }}</span>
                                </div>
                                <div class="strategy-select-card__action">
                                    @lang('View Performance') <i class="las la-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="quant-panel">
                        <div class="quant-panel__body">
                            <p class="text-muted mb-0">@lang('No active strategies available yet.')</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        @forelse($yearSections as $section)
            <div class="quant-panel mb-4">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap gap-2">
                        <h5 class="quant-panel__title mb-0">{{ $section->year }}</h5>
                        <div class="text-end">
                            @if($section->chart->point_count)
                                <small class="text-muted d-block">
                                    @if((int) $section->year === (int) date('Y'))
                                        @lang('Return till date')
                                    @else
                                        @lang('Return for the year')
                                    @endif
                                </small>
                                <strong class="text-dark">{{ showAmount($section->chart->ytd_percent) }}%</strong>
                            @else
                                <small class="text-muted">@lang('Report available')</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="quant-panel__body">
                    @if($section->chart->point_count)
                        <div id="strategyYearChart{{ $section->year }}" class="quant-strategy-chart"></div>
                    @else
                        <p class="text-muted mb-0">@lang('No approved performance chart for this year yet.')</p>
                    @endif

                    @if($section->report)
                        <div class="strategy-report-block mt-4 pt-3 border-top">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div>
                                    <h6 class="mb-1">@lang(':year Strategy Report', ['year' => $section->year])</h6>
                                    <small class="text-muted">{{ $section->report->displayName() }}</small>
                                </div>
                                <a href="{{ route('user.strategy.report', [$selectedPlan->id, $section->year]) }}" target="_blank" class="btn btn--base btn--sm">
                                    <i class="las la-file-pdf"></i> @lang('Open PDF')
                                </a>
                            </div>
                            <iframe
                                src="{{ route('user.strategy.report', [$selectedPlan->id, $section->year]) }}"
                                class="strategy-report-frame w-100"
                                title="@lang(':year Strategy Report', ['year' => $section->year])"
                            ></iframe>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="quant-panel">
                <div class="quant-panel__body">
                    <p class="text-muted mb-0">@lang('No approved performance or reports for this strategy yet.')</p>
                </div>
            </div>
        @endforelse
    @endif
</div>
@endsection

@push('style')
<style>
    .strategy-select-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .strategy-select-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(25, 137, 190, 0.12);
    }
    .strategy-select-card__action {
        color: #1989BE;
        font-weight: 600;
        font-size: 14px;
    }
    .strategy-report-frame {
        min-height: 420px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }
</style>
@endpush

@if($selectedPlan)
@push('script-lib')
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
<script>
(function () {
    "use strict";

    const quantChartTheme = { primary: '#1989BE', muted: '#64748b', grid: '#e2e8f0' };

    const yearChartsData = [
        @foreach($yearSections as $section)
            @if($section->chart->point_count)
            {
                id: 'strategyYearChart{{ $section->year }}',
                rates: [@foreach($section->chart->points as $point){{ getAmount($point->rate_percent) }},@endforeach],
                dates: [@foreach($section->chart->points as $point)"{{ $point->date_label }}",@endforeach]
            },
            @endif
        @endforeach
    ];

    function buildStrategyChartOptions(data) {
        return {
            chart: { height: 240, type: 'area', toolbar: { show: false }, fontFamily: 'Maven Pro, sans-serif' },
            series: [{ name: '', data: data.rates.length ? data.rates : [] }],
            colors: [quantChartTheme.primary],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] } },
            stroke: { curve: 'smooth', width: 2 },
            markers: { size: data.rates.length > 0 ? 4 : 0, strokeWidth: 2, strokeColors: '#fff' },
            xaxis: { labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: {
                title: { show: false },
                labels: { formatter: v => v.toFixed(2) + '%', style: { colors: quantChartTheme.muted, fontSize: '11px' } }
            },
            grid: { borderColor: quantChartTheme.grid, strokeDashArray: 4, padding: { left: 8, right: 16 } },
            dataLabels: { enabled: false },
            legend: { show: false },
            tooltip: {
                custom: function ({ seriesIndex, dataPointIndex, w }) {
                    const rate = w.globals.series[seriesIndex][dataPointIndex];
                    const dates = data.dates[dataPointIndex];
                    if (rate === undefined) return '';
                    return '<div class="p-2"><strong>' + dates + '</strong><br>' + rate.toFixed(2) + '%</div>';
                }
            },
            noData: { text: '@lang("No approved performance yet")', align: 'center', style: { color: quantChartTheme.muted } }
        };
    }

    yearChartsData.forEach(function (data) {
        const el = document.getElementById(data.id);
        if (el) new ApexCharts(el, buildStrategyChartOptions(data)).render();
    });
})();
</script>
@endpush
@endif
