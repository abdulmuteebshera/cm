@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Channels')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead><tr><th>@lang('Channel')</th><th>@lang('Sessions')</th></tr></thead>
                            <tbody>
                                @forelse($channels as $row)
                                    <tr><td>{{ ucfirst($row->channel ?: 'Unknown') }}</td><td>{{ $row->total }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Campaigns')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead><tr><th>@lang('Campaign')</th><th>@lang('Sessions')</th></tr></thead>
                            <tbody>
                                @forelse($campaigns as $row)
                                    <tr><td>{{ $row->campaign }}</td><td>{{ $row->total }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Sources')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Source')</th>
                                    <th>@lang('Medium')</th>
                                    <th>@lang('Channel')</th>
                                    <th>@lang('Sessions')</th>
                                    <th>@lang('Visitors')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sources as $row)
                                    <tr>
                                        <td>{{ $row->source }}</td>
                                        <td>{{ $row->medium }}</td>
                                        <td>{{ $row->channel }}</td>
                                        <td>{{ $row->sessions }}</td>
                                        <td>{{ $row->visitors }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sources->hasPages())
                    <div class="card-footer py-4">{{ paginateLinks($sources) }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
