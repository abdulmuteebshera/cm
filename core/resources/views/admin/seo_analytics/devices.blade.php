@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Device type')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead><tr><th>@lang('Device')</th><th>@lang('Sessions')</th></tr></thead>
                            <tbody>
                                @forelse($devices as $row)
                                    <tr><td>{{ ucfirst($row->device_type ?: 'Unknown') }}</td><td>{{ $row->total }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Browsers')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead><tr><th>@lang('Browser')</th><th>@lang('Sessions')</th></tr></thead>
                            <tbody>
                                @forelse($browsers as $row)
                                    <tr><td>{{ $row->browser }}</td><td>{{ $row->total }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Operating systems')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead><tr><th>@lang('OS')</th><th>@lang('Sessions')</th></tr></thead>
                            <tbody>
                                @forelse($oses as $row)
                                    <tr><td>{{ $row->os }}</td><td>{{ $row->total }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
