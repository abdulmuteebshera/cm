@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')

    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end" method="GET">
                <input type="hidden" name="range" value="{{ $range }}">
                <div class="col-md-3">
                    <label class="form-label">@lang('Search')</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="@lang('IP, city, page, source')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Country')</label>
                    <input type="text" name="country" value="{{ request('country') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Device')</label>
                    <select name="device" class="form-control">
                        <option value="">@lang('All')</option>
                        @foreach(['desktop','mobile','tablet'] as $device)
                            <option value="{{ $device }}" @selected(request('device') === $device)>{{ ucfirst($device) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Channel')</label>
                    <input type="text" name="channel" value="{{ request('channel') }}" class="form-control" placeholder="organic, direct">
                </div>
                <div class="col-md-3">
                    <button class="btn btn--primary w-100">@lang('Filter')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Visitor')</th>
                            <th>@lang('Location')</th>
                            <th>@lang('Device')</th>
                            <th>@lang('Source')</th>
                            <th>@lang('Landing / exit')</th>
                            <th>@lang('Pages')</th>
                            <th>@lang('Clicks')</th>
                            <th>@lang('Time on site')</th>
                            <th>@lang('Last seen')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $row)
                            <tr>
                                <td>
                                    <span class="fw-bold">#{{ $row->id }}</span><br>
                                    <small>{{ $row->ip }}</small>
                                </td>
                                <td>{{ $row->city }}, {{ $row->country }}</td>
                                <td>{{ ucfirst($row->device_type) }}<br><small>{{ $row->browser }} · {{ $row->os }}</small></td>
                                <td>{{ $row->channel }}<br><small>{{ $row->source }} @if($row->campaign)/ {{ $row->campaign }}@endif</small></td>
                                <td>{{ $row->landing_page }}<br><small>{{ $row->exit_page }}</small></td>
                                <td>{{ $row->page_views }}</td>
                                <td>{{ $row->clicks }}</td>
                                <td>{{ $row->duration_label }}</td>
                                <td>{{ showDateTime($row->last_seen_at) }}</td>
                                <td>
                                    <a href="{{ route('admin.seo.analytics.session', $row->id) }}" class="btn btn-sm btn-outline--primary">@lang('Journey')</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sessions->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($sessions) }}</div>
        @endif
    </div>
@endsection
