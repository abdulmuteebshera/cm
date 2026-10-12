@extends('admin.layouts.app')
@section('panel')
    @foreach($reports as $report)
        <div class="card b-radius--10 mb-3">
            <div class="card-header"><h5 class="mb-0">{{ $report->report_date->format('d M Y') }}</h5></div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>{{ $report->researched }}</strong><br><span class="text-muted">@lang('Prospects researched')</span></div>
                    <div class="col-md-3"><strong>{{ $report->approved }}</strong><br><span class="text-muted">@lang('Approved messages')</span></div>
                    <div class="col-md-3"><strong>{{ $report->replies }}</strong><br><span class="text-muted">@lang('Replies')</span></div>
                    <div class="col-md-3"><strong>{{ $report->qualified }}</strong><br><span class="text-muted">@lang('Qualified opportunities')</span></div>
                </div>
                <p class="text-muted mb-3">@lang('Declined'): {{ $report->declined }} · @lang('Opted out'): {{ $report->opted_out }} · @lang('Posts published'): {{ $report->posts_published }}</p>
                @if($report->notes)
                    <div class="alert alert-warning">{{ $report->notes }}</div>
                @endif
                @php $summary = $report->summary ?? []; @endphp
                <div class="row">
                    @foreach(['researched' => 'Researched', 'approved' => 'Approved messages', 'replies' => 'Replies', 'qualified' => 'Qualified opportunities'] as $key => $label)
                        <div class="col-md-6 mb-3">
                            <h6>@lang($label)</h6>
                            @if(!empty($summary[$key]))
                                @foreach($summary[$key] as $row)
                                    <div class="mb-1">
                                        {{ $row['name'] ?? '' }}
                                        @if(!empty($row['company']))
                                            — <a href="{{ $row['url'] ?? '#' }}" target="_blank" rel="noopener">{{ $row['company'] }}</a>
                                        @endif
                                        @if(!empty($row['kind']))
                                            <small class="text-muted">({{ $row['kind'] }})</small>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted">@lang('None')</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($reports->hasPages())
        {{ paginateLinks($reports) }}
    @endif
    @if($reports->isEmpty())
        <div class="card b-radius--10"><div class="card-body text-muted">@lang('No report yet. Run today’s preparation from the overview.')</div></div>
    @endif
@endsection

@push('breadcrumb-plugins')
    @include('admin.linkedin.partials.nav')
@endpush
