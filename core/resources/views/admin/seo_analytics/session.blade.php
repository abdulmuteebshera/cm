@extends('admin.layouts.app')
@section('panel')
    @include('admin.seo_analytics.partials.nav')

    <div class="row gy-4 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Visitor')</h5>
                    <p class="mb-1"><strong>@lang('IP'):</strong> {{ $session->ip }}</p>
                    <p class="mb-1"><strong>@lang('Visitor ID'):</strong> {{ $session->visitor_id }}</p>
                    <p class="mb-1"><strong>@lang('Location'):</strong> {{ $session->city }}, {{ $session->region }} {{ $session->country }}</p>
                    <p class="mb-1"><strong>@lang('Device'):</strong> {{ ucfirst($session->device_type) }} · {{ $session->browser }} · {{ $session->os }}</p>
                    <p class="mb-1"><strong>@lang('Source'):</strong> {{ $session->channel }} / {{ $session->source }} / {{ $session->medium }}</p>
                    @if($session->campaign)
                        <p class="mb-1"><strong>@lang('Campaign'):</strong> {{ $session->campaign }}</p>
                    @endif
                    <p class="mb-1"><strong>@lang('Referrer'):</strong> {{ $session->referrer ?: 'Direct' }}</p>
                    <p class="mb-1"><strong>@lang('First seen'):</strong> {{ showDateTime($session->first_seen_at) }}</p>
                    <p class="mb-1"><strong>@lang('Last seen'):</strong> {{ showDateTime($session->last_seen_at) }}</p>
                    <p class="mb-0"><strong>@lang('Time on site'):</strong> {{ $session->duration_label }} · {{ $session->page_views }} @lang('pages') · {{ $session->clicks }} @lang('clicks')</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Page journey')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Entered')</th>
                                    <th>@lang('Page')</th>
                                    <th>@lang('Time on page')</th>
                                    <th>@lang('Scroll')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->pageViews as $view)
                                    <tr>
                                        <td>{{ showDateTime($view->entered_at) }}</td>
                                        <td>
                                            <div>{{ $view->path }}</div>
                                            <small class="text-muted">{{ $view->url }}</small>
                                        </td>
                                        <td>{{ $view->duration_label }}</td>
                                        <td>{{ $view->scroll_depth }}%</td>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="card-header"><h6 class="mb-0">@lang('Clicks in this session')</h6></div>
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Time')</th>
                                    <th>@lang('Label')</th>
                                    <th>@lang('Target')</th>
                                    <th>@lang('On page')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($session->events as $event)
                                    <tr>
                                        <td>{{ showDateTime($event->occurred_at) }}</td>
                                        <td>{{ $event->label ?: '—' }}</td>
                                        <td>{{ $event->target_url ?: '—' }}</td>
                                        <td>{{ $event->path }}</td>
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
    </div>
@endsection
