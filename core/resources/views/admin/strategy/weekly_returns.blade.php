@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="mb-1">{{ __($plan->name) }}</h5>
                            <p class="text-muted mb-0">@lang('Track weekly performance for charts. Select a year (2023 to current), enter rates, and approve each week after it ends. Approved data appears on the Strategy Performance page; the dashboard shows the current year only.')</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 fw-bold">@lang('Year')</label>
                            <select class="form-control form-control-sm" onchange="window.location='{{ route('admin.strategy.weekly.returns', $plan->id) }}?year='+this.value">
                                @foreach($entryYears as $entryYear)
                                    <option value="{{ $entryYear }}" @selected($year == $entryYear)>{{ $entryYear }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2" role="alert">
                        <div>
                            <i class="las la-lock"></i>
                            @lang('Approved weeks are locked. Enable editing to correct already-approved performance values (they stay live on charts).')
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="editApprovedToggle" name="edit_approved" value="1" form="weeklyReturnsForm">
                            <label class="form-check-label fw-bold" for="editApprovedToggle">@lang('Edit approved returns')</label>
                        </div>
                    </div>
                    <form id="weeklyReturnsForm" action="{{ route('admin.strategy.weekly.returns.save', $plan->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="year" value="{{ $year }}">
                        @foreach($quarters as $monthLabel => $weeks)
                            <div class="mb-4">
                                <h6 class="mb-3">{{ $monthLabel }}</h6>
                                <div class="row g-3">
                                    @foreach($weeks as $weekMeta)
                                        @php
                                            $weekKey = $weekMeta['iso_year'] . '-' . $weekMeta['week'];
                                            $fieldKey = $weekMeta['iso_year'] . '_' . $weekMeta['week'];
                                            $record = $savedReturns->get($weekKey);
                                            $status = $record->payout_status ?? 'draft';
                                            $isApproved = $status === 'approved';
                                            $isFuture = !$weekMeta['is_enterable'];
                                        @endphp
                                        <div class="col-md-4 col-sm-6">
                                            <div class="border rounded p-3 h-100">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div><strong>@lang('Week') {{ $weekMeta['week'] }}</strong><small class="d-block text-muted">{{ $weekMeta['date_label'] }}</small></div>
                                                    @if($isFuture)<span class="badge badge--dark">@lang('Upcoming')</span>@elseif($isApproved)<span class="badge badge--success">@lang('Approved')</span>@elseif($status === 'pending')<span class="badge badge--warning">@lang('Pending')</span>@else<span class="badge badge--dark">@lang('Draft')</span>@endif
                                                </div>
                                                <div class="input-group input-group-sm mb-2">
                                                    <input type="number" step="0.0001" min="-100" max="100" class="form-control @if($isApproved) approved-return-input @endif" name="returns[{{ $fieldKey }}]" form="weeklyReturnsForm" value="{{ old('returns.'.$fieldKey, optional($record)->return_percent) }}" @disabled($isApproved || $isFuture) @if($isFuture) data-future="1" @endif>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                @if($record && $status === 'pending' && $record->return_percent != 0)
                                                    <button type="button" class="btn btn--success btn-sm w-100 confirmationBtn" data-question="@lang('Approve this weekly performance for live charts?')" data-action="{{ route('admin.strategy.weekly.approve', $record->id) }}?year={{ $year }}">@lang('Approve for Chart')</button>
                                                @elseif($isApproved)
                                                    <small class="text-muted">@lang('Live on charts')</small>
                                                @else
                                                    <small class="text-muted">@lang('Save rate, then approve to show on charts')</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </form>
                    <button type="submit" form="weeklyReturnsForm" class="btn btn--primary">@lang('Save Weekly Returns')</button>
                    <a href="{{ route('admin.plan.index') }}" class="btn btn--dark">@lang('Back')</a>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection
@push('script')
    <script>
        (function () {
            var toggle = document.getElementById('editApprovedToggle');
            if (!toggle) return;
            toggle.addEventListener('change', function () {
                document.querySelectorAll('.approved-return-input').forEach(function (input) {
                    input.disabled = !toggle.checked;
                    input.classList.toggle('border-warning', toggle.checked);
                });
            });
        })();
    </script>
@endpush
@push('breadcrumb-plugins')
    <a href="{{ route('admin.strategy.period.returns', $plan->id) }}" class="btn btn-sm btn-outline--info">@lang('Period Returns (Payouts)')</a>
    <a href="{{ route('admin.plan.index') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left"></i> @lang('All Strategies')</a>
@endpush
