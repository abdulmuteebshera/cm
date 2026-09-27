@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')
    <div class="row gy-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Countries')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Sessions')</th>
                                    <th>@lang('Visitors')</th>
                                    <th>@lang('Avg time')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $row)
                                    <tr>
                                        <td>{{ $row->country ?: 'Unknown' }} @if($row->country_code)<small>({{ $row->country_code }})</small>@endif</td>
                                        <td>{{ $row->sessions }}</td>
                                        <td>{{ $row->visitors }}</td>
                                        <td>{{ \App\Models\SeoVisitorSession::formatSeconds((int) $row->avg_time) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Cities')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('City')</th>
                                    <th>@lang('Country')</th>
                                    <th>@lang('Sessions')</th>
                                    <th>@lang('Visitors')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cities as $row)
                                    <tr>
                                        <td>{{ $row->city ?: 'Unknown' }}</td>
                                        <td>{{ $row->country }}</td>
                                        <td>{{ $row->sessions }}</td>
                                        <td>{{ $row->visitors }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cities->hasPages())
                    <div class="card-footer py-4">{{ paginateLinks($cities) }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
