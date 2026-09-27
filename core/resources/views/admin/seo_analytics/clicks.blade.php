@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')
    <div class="row gy-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Most clicked links')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Label')</th>
                                    <th>@lang('URL')</th>
                                    <th>@lang('Clicks')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topLinks as $row)
                                    <tr>
                                        <td>{{ $row->label ?: '—' }}</td>
                                        <td class="white-space-wrap">{{ $row->target_url }}</td>
                                        <td>{{ $row->clicks }}</td>
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
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Recent clicks')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Time')</th>
                                    <th>@lang('Label')</th>
                                    <th>@lang('Target')</th>
                                    <th>@lang('Page')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent as $row)
                                    <tr>
                                        <td>{{ showDateTime($row->occurred_at) }}</td>
                                        <td>{{ $row->label ?: '—' }}</td>
                                        <td class="white-space-wrap">{{ $row->target_url }}</td>
                                        <td>{{ $row->path }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ __($emptyMessage) }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recent->hasPages())
                    <div class="card-footer py-4">{{ paginateLinks($recent) }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
