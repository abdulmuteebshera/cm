@extends('admin.layouts.app')
@php
    $items = $record->payoutItems;
    $pendingCount = $items->where('status', \App\Models\PeriodPayoutItem::STATUS_PENDING)->count();
    $approvedCount = $items->where('status', \App\Models\PeriodPayoutItem::STATUS_APPROVED)->count();
    $approvedTotal = $items->where('status', \App\Models\PeriodPayoutItem::STATUS_APPROVED)->sum('amount');
    $pendingTotal = $items->where('status', \App\Models\PeriodPayoutItem::STATUS_PENDING)->sum('amount');
@endphp
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h6 class="mb-3">@lang('Period Summary')</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Strategy')</span><strong>{{ __($record->plan->name) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Period')</span><strong>{{ $record->periodLabel() }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Cycle')</span><strong>{{ $record->plan->payoutFrequencyLabel() }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Dates')</span><strong>{{ showDateTime($record->period_start, 'M d') }} – {{ showDateTime($record->period_end, 'M d, Y') }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Payout Date')</span><strong>{{ showDateTime($record->payout_date ?? \App\Lib\StrategyPayoutService::periodPayoutDate($record->period_end), 'M d, Y') }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Return Rate')</span><strong class="{{ $record->return_percent < 0 ? 'text-danger' : '' }}">{{ showAmount($record->return_percent) }}%</strong></li>
                        <li class="list-group-item px-0">
                            <span class="d-block mb-2">@lang('From weekly returns')</span>
                            @forelse($weeklyBreakdown as $week)
                                <span class="badge badge--primary me-1 mb-1" title="{{ $week->date_label }}">{{ $week->label }} {{ showAmount($week->return_percent) }}%</span>
                            @empty
                                <small class="text-muted">@lang('No weekly data')</small>
                            @endforelse
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Clients')</span><strong>{{ $items->count() }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Pending / Paid')</span><strong>{{ $pendingCount }} / {{ $approvedCount }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Approved Amount')</span><strong>{{ $general->cur_sym }}{{ showAmount($approvedTotal) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Remaining')</span><strong>{{ $general->cur_sym }}{{ showAmount($pendingTotal) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Status')</span>
                            @if($record->isPartiallyDisbursed())
                                <span class="badge badge--info">@lang('Partially Disbursed')</span>
                            @elseif($record->payout_status === 'pending')
                                <span class="badge badge--warning">@lang('Pending Approval')</span>
                            @elseif($record->payout_status === 'approved')
                                <span class="badge badge--success">@lang('Approved')</span>
                            @else
                                <span class="badge badge--danger">{{ ucfirst($record->payout_status) }}</span>
                            @endif
                        </li>
                    </ul>
                    @if($pendingCount > 0)
                        <form action="{{ route('admin.strategy.period.approve', $record->id) }}" method="post" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn--success w-100 confirmationBtn" data-question="@lang('Disburse every remaining pending client using their saved amounts?')">@lang('Disburse Remaining Clients')</button>
                        </form>
                        <form action="{{ route('admin.strategy.period.reject', $record->id) }}" method="post" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn--danger w-100 confirmationBtn" data-question="@lang('Reject every remaining pending client? Already disbursed clients stay paid.')">@lang('Reject Remaining')</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Client')</th>
                                    <th>@lang('Invest Amount')</th>
                                    <th>@lang('Calculated')</th>
                                    <th style="min-width: 220px;">@lang('Payout Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php $formId = 'payout-item-form-'.$item->id; @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ @$item->user->fullname }}</strong>
                                            <small class="d-block text-muted">{{ @$item->user->username }}</small>
                                        </td>
                                        <td>{{ $general->cur_sym }}{{ showAmount(@$item->invest->amount) }}</td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($item->calculated_amount ?: $item->amount) }}</td>
                                        <td>
                                            @if($item->isPending() || $item->isRejected())
                                                <form id="{{ $formId }}" action="{{ route('admin.strategy.payout.item.approve', $item->id) }}" method="post">
                                                    @csrf
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                                        <input type="text"
                                                            inputmode="decimal"
                                                            name="amount"
                                                            id="payout-amount-{{ $item->id }}"
                                                            value="{{ number_format((float) $item->amount, 2, '.', '') }}"
                                                            class="form-control payout-amount-input"
                                                            autocomplete="off">
                                                    </div>
                                                </form>
                                            @else
                                                <strong>{{ $general->cur_sym }}{{ showAmount($item->amount) }}</strong>
                                                @if($item->amount_edited)
                                                    <small class="d-block text-muted">@lang('Edited')</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->isApproved())
                                                <span class="badge badge--success">@lang('Disbursed')</span>
                                            @elseif($item->isRejected())
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->isPending() || $item->isRejected())
                                                <button type="submit"
                                                    form="{{ $formId }}"
                                                    formaction="{{ route('admin.strategy.payout.item.amount', $item->id) }}"
                                                    class="btn btn-sm btn-outline--info mb-1">@lang('Save Amount')</button>
                                                <button type="submit"
                                                    form="{{ $formId }}"
                                                    class="btn btn-sm btn-outline--success js-submit-payout mb-1"
                                                    data-question="@lang('Approve and disburse the amount entered for this client?')">@lang('Approve')</button>
                                            @endif
                                            @if($item->isPending())
                                                <form action="{{ route('admin.strategy.payout.item.reject', $item->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--danger js-submit-payout" data-question="@lang('Reject this client payout? You can still approve them later.')">@lang('Reject')</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="100%" class="text-center text-muted">@lang('No client payouts for this period')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.strategy.payouts') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left"></i> @lang('Back')</a>
    <a href="{{ route('admin.strategy.period.returns', $record->plan_id) }}?year={{ $record->year }}" class="btn btn-sm btn-outline--info">@lang('Period Returns')</a>
@endpush

@push('style')
<style>
    .payout-amount-input {
        min-width: 140px;
        background: #fff !important;
        border: 1px solid #c5c9d4 !important;
        color: #111 !important;
        pointer-events: auto !important;
    }
    .payout-amount-input:focus {
        border-color: #4634ff !important;
        box-shadow: 0 0 0 2px rgba(70, 52, 255, .15);
    }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";
        $(document).on('click', '.js-submit-payout', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var question = $(this).data('question') || 'Are you sure?';
            if (!window.confirm(question)) {
                return;
            }
            var form = this.form || document.getElementById($(this).attr('form'));
            if (form) {
                form.submit();
            }
        });
    })(jQuery);
</script>
@endpush
