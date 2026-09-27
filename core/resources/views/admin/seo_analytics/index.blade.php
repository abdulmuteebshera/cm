@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')

    @if(!empty($setup))
        <div class="alert border border--warning" role="alert">
            <div class="alert__icon bg--warning"><i class="las la-database"></i></div>
            <p class="alert__message">
                <strong>@lang('Analytics tables are not installed yet.')</strong><br>
                @lang('On this server run'): <code>php artisan migrate --path=database/migrations/2026_09_27_000001_create_seo_analytics_tables.php --force</code><br>
                @lang('On production use'): <code>php artisan migrate:new-tables-only</code>
            </p>
        </div>
    @else
        <div class="row gy-4 mb-4">
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-user-friends f-size--56" title="Visitors" value="{{ $widget['visitors'] }}" bg="primary" />
            </div>
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-stream f-size--56" title="Sessions" value="{{ $widget['sessions'] }}" bg="success" />
            </div>
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-file-alt f-size--56" title="Page views" value="{{ $widget['page_views'] }}" bg="info" />
            </div>
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-mouse-pointer f-size--56" title="Clicks" value="{{ $widget['clicks'] }}" bg="warning" />
            </div>
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-clock f-size--56" title="Avg time on site" value="{{ $widget['avg_time'] }}" bg="dark" />
            </div>
            <div class="col-xxl-2 col-sm-4">
                <x-widget icon="las la-sign-out-alt f-size--56" title="Bounce rate" value="{{ $widget['bounce_rate'] }}" bg="danger" />
            </div>
        </div>

        <div class="row gy-4 mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Visitors & sessions')</h5>
                        <div id="seoTrend"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Devices')</h5>
                        <div id="seoDevices"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="card-header"><h6 class="mb-0">@lang('Top pages')</h6></div>
                        <div class="table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Page')</th>
                                        <th>@lang('Views')</th>
                                        <th>@lang('Avg time')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topPages as $row)
                                        <tr>
                                            <td>{{ $row->path }}</td>
                                            <td>{{ $row->views }}</td>
                                            <td>{{ \App\Models\SeoVisitorSession::formatSeconds((int) $row->avg_time) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="card-header"><h6 class="mb-0">@lang('Top countries')</h6></div>
                        <div class="table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Country')</th>
                                        <th>@lang('Sessions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCountries as $row)
                                        <tr>
                                            <td>{{ $row->country ?: 'Unknown' }}</td>
                                            <td>{{ $row->total }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="mb-0">@lang('Recent visitors')</h6>
                            <a href="{{ route('admin.seo.analytics.visitors', ['range' => $range]) }}" class="btn btn-sm btn-outline--primary">@lang('View all')</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('When')</th>
                                        <th>@lang('Location')</th>
                                        <th>@lang('Device')</th>
                                        <th>@lang('Source')</th>
                                        <th>@lang('Landing')</th>
                                        <th>@lang('Time')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent as $row)
                                        <tr>
                                            <td>{{ showDateTime($row->last_seen_at) }}</td>
                                            <td>{{ $row->city }}, {{ $row->country }}</td>
                                            <td>{{ ucfirst($row->device_type) }} / {{ $row->browser }}</td>
                                            <td>{{ $row->channel }} · {{ $row->source }}</td>
                                            <td>{{ $row->landing_page }}</td>
                                            <td>{{ $row->duration_label }}</td>
                                            <td>
                                                <a href="{{ route('admin.seo.analytics.session', $row->id) }}" class="btn btn-sm btn-outline--primary">@lang('Details')</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@if(empty($setup))
@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
@endpush
@push('script')
<script>
    (function () {
        var days = @json($daily->pluck('day'));
        var visitors = @json($daily->pluck('visitors'));
        var sessions = @json($daily->pluck('sessions'));
        new ApexCharts(document.querySelector('#seoTrend'), {
            chart: { type: 'area', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Visitors', data: visitors },
                { name: 'Sessions', data: sessions }
            ],
            xaxis: { categories: days },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#4634ff', '#28c76f']
        }).render();

        var deviceLabels = @json($devices->pluck('device_type'));
        var deviceValues = @json($devices->pluck('total'));
        new ApexCharts(document.querySelector('#seoDevices'), {
            chart: { type: 'donut', height: 320 },
            series: deviceValues.map(function (n) { return Number(n); }),
            labels: deviceLabels,
            colors: ['#4634ff', '#28c76f', '#ff9f43']
        }).render();
    })();
</script>
@endpush
@endif
