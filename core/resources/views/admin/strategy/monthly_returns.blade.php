@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="mb-1">{{ __($plan->name) }}</h5>
                            <p class="text-muted mb-0">@lang('Track monthly performance for charts. Select a year (2022 to current), enter rates, and approve each month after it ends. Approved data appears on the Strategy Performance page; the dashboard shows the current year only.')</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 fw-bold">@lang('Year')</label>
                            <select class="form-control form-control-sm" onchange="window.location='{{ route('admin.strategy.monthly.returns', $plan->id) }}?year='+this.value">
                                @foreach($entryYears as $entryYear)
                                    <option value="{{ $entryYear }}" @selected($year == $entryYear)>{{ $entryYear }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <form id="monthlyReturnsForm" action="{{ route('admin.strategy.monthly.returns.save', $plan->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        <div class="row g-3">
                            @foreach($periods as $monthMeta)
                                @php
                                    $monthKey = $monthMeta['year'] . '-' . $monthMeta['month'];
                                    $fieldKey = $monthMeta['year'] . '_' . $monthMeta['month'];
                                    $record = $savedReturns->get($monthKey);
                                    $status = $record->payout_status ?? 'draft';
                                    $isApproved = $status === 'approved';
                                    $isFuture = !$monthMeta['is_enterable'];
                                @endphp
                                <div class="col-md-4 col-sm-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div><strong>{{ $monthMeta['date_label'] }}</strong></div>
                                            @if($isFuture)<span class="badge badge--dark">@lang('Upcoming')</span>@elseif($isApproved)<span class="badge badge--success">@lang('Approved')</span>@elseif($status === 'pending')<span class="badge badge--warning">@lang('Pending')</span>@else<span class="badge badge--dark">@lang('Draft')</span>@endif
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" step="0.0001" min="-100" max="100" class="form-control" name="returns[{{ $fieldKey }}]" form="monthlyReturnsForm" value="{{ old('returns.'.$fieldKey, optional($record)->return_percent) }}" @disabled($isApproved || $isFuture)>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @if($record && $status === 'pending' && $record->return_percent != 0)
                                            <button type="button" class="btn btn--success btn-sm w-100 confirmationBtn mt-2" data-question="@lang('Approve this monthly performance for live charts?')" data-action="{{ route('admin.strategy.monthly.approve', $record->id) }}?year={{ $year }}">@lang('Approve for Chart')</button>
                                        @elseif($isApproved)
                                            <small class="text-muted">@lang('Live on charts')</small>
                                        @else
                                            <small class="text-muted">@lang('Save rate, then approve to show on charts')</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                    <button type="submit" form="monthlyReturnsForm" class="btn btn--primary mt-4">@lang('Save Monthly Returns')</button>
                    <a href="{{ route('admin.plan.index') }}" class="btn btn--dark mt-4">@lang('Back')</a>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('admin.strategy.period.returns', $plan->id) }}" class="btn btn-sm btn-outline--info">@lang('Period Returns (Payouts)')</a>
    <a href="{{ route('admin.plan.index') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left"></i> @lang('All Strategies')</a>
@endpush
