@extends($activeTemplate.'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard">
    <div class="quant-header mb-4">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Strategy Performance')</h4>
            @if($selectedPlan)
                <p class="text-muted mb-0">{{ __($selectedPlan->name) }} · @lang('Performance history by year')</p>
            @else
                <p class="text-muted mb-0">@lang('Select a strategy to view performance across all years since 2023.')</p>
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

        @if($comparison->rows->count() > 1)
            <div class="quant-panel mt-4 strategy-compare">
                <div class="quant-panel__head quant-panel__head--aligned">
                    <h5 class="quant-panel__title mb-0">@lang('Strategy Comparison')</h5>
                    <small class="text-muted">@lang('Compare performance, payouts, fees and risk side by side')</small>
                </div>
                <div class="quant-panel__body">
                    @if(count($comparison->years))
                        <h6 class="strategy-compare__subtitle">@lang('Annual Performance Comparison')</h6>
                        <div id="strategyCompareChart" class="quant-strategy-chart mb-4"></div>
                    @endif

                    <div class="table-responsive">
                        <table class="table strategy-compare__table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Comparison')</th>
                                    @foreach($comparison->rows as $row)
                                        <th class="text-center">
                                            {{ __($row->name) }}
                                            <small class="d-block text-muted fw-normal">{{ $row->frequency_label }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Avg. Annual Return')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center fw-bold text-success">{{ showAmount($row->avg_annual) }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Return (:year)', ['year' => date('Y')])</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ $row->ytd_current !== null ? showAmount($row->ytd_current) . '%' : '—' }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Best Year')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ showAmount($row->best_year) }}%</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Strategy Style')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center fw-semibold">{{ __($row->strategy_style) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Suitable For')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ __($row->suitable_for) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Payout Frequency')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center"><span class="quant-panel__tag">{{ $row->frequency_label }}</span></td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Management Fee')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ showAmount($row->management_fee) }}% @lang('p.a.')</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Risk Involved')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">
                                            <span class="strategy-compare__risk strategy-compare__risk--{{ $row->risk_score >= 8 ? 'high' : ($row->risk_score >= 6 ? 'medium' : 'low') }}">{{ __($row->risk_label) }}</span>
                                            <span class="strategy-compare__risk-bar"><span style="width: {{ min(100, $row->risk_score * 10) }}%"></span></span>
                                        </td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Investment Horizon')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ __($row->horizon) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Minimum Investment')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">{{ showAmount($row->minimum) }} {{ __(gs('cur_text')) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric">@lang('Objective')</th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center small text-muted">{{ __($row->objective) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="strategy-compare__metric"></th>
                                    @foreach($comparison->rows as $row)
                                        <td class="text-center">
                                            <a href="{{ route('user.strategy.performance', ['plan' => $row->plan_id]) }}" class="btn btn--base btn--sm">@lang('View Details')</a>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
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
    .strategy-compare__subtitle {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 12px;
    }
    .strategy-compare__table th,
    .strategy-compare__table td {
        border-color: #eef2f7;
        vertical-align: middle;
    }
    .strategy-compare__table thead th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }
    .strategy-compare__metric {
        text-align: left;
        color: #475569;
        font-weight: 600;
        white-space: nowrap;
    }
    .strategy-compare__table tbody tr:nth-child(odd) td,
    .strategy-compare__table tbody tr:nth-child(odd) .strategy-compare__metric {
        background: #fcfdff;
    }
    .strategy-compare__risk {
        display: inline-block;
        font-weight: 600;
        font-size: 12px;
        padding: 2px 10px;
        border-radius: 999px;
    }
    .strategy-compare__risk--low { background: rgba(16, 185, 129, .12); color: #059669; }
    .strategy-compare__risk--medium { background: rgba(245, 158, 11, .14); color: #d97706; }
    .strategy-compare__risk--high { background: rgba(239, 68, 68, .12); color: #dc2626; }
    .strategy-compare__risk-bar {
        display: block;
        width: 80px;
        height: 5px;
        margin: 6px auto 0;
        background: #eef2f7;
        border-radius: 999px;
        overflow: hidden;
    }
    .strategy-compare__risk-bar > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #10b981, #f59e0b, #ef4444);
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

@if(!$selectedPlan && $comparison->rows->count() > 1 && count($comparison->years))
@push('script-lib')
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
<script>
(function () {
    "use strict";

    const el = document.getElementById('strategyCompareChart');
    if (!el || typeof ApexCharts === 'undefined') return;

    const categories = [@foreach($comparison->years as $y)"{{ $y }}",@endforeach];
    const series = [
        @foreach($comparison->rows as $row)
        {
            name: "{{ addslashes(__($row->name)) }}",
            data: [@foreach($comparison->years as $y){{ isset($row->yearly[$y]) ? getAmount($row->yearly[$y]) : 'null' }},@endforeach]
        },
        @endforeach
    ];

    const options = {
        chart: { height: 320, type: 'bar', toolbar: { show: false }, fontFamily: 'Maven Pro, sans-serif' },
        series: series,
        colors: ['#1989BE', '#10b981', '#f59e0b'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: { categories: categories, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { formatter: v => v.toFixed(0) + '%', style: { colors: '#64748b', fontSize: '11px' } } },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'left', fontSize: '13px', markers: { radius: 4 } },
        tooltip: { y: { formatter: v => (v === null ? 'N/A' : v.toFixed(2) + '%') } }
    };

    new ApexCharts(el, options).render();
})();
</script>
@endpush
@endif
