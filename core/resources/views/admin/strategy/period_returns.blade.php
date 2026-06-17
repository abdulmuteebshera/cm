@extends('admin.layouts.app')
@php use App\Lib\StrategyPayoutService; @endphp
@section('panel')
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h5 class="mb-1">{{ __($plan->name) }}</h5>
                            <p class="text-muted mb-0">@lang('Payouts follow each investor\'s investment date (not calendar quarters). Batches are queued automatically on the payout date.')</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 fw-bold">@lang('Year')</label>
                            <select class="form-control form-control-sm" onchange="window.location='{{ route('admin.strategy.period.returns', $plan->id) }}?year='+this.value">
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        @forelse($batches as $record)
                            @php
                                $status = $record->payout_status ?? 'draft';
                                $weeklyBreakdown = StrategyPayoutService::periodWeeklyBreakdown($record);
                            @endphp
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded p-3 h-100">
                                    <strong>{{ $record->periodLabel() }}</strong>
                                    <small class="d-block text-muted mb-1">
                                        @lang('Period'): {{ showDateTime($record->period_start, 'M d') }} – {{ showDateTime($record->period_end, 'M d, Y') }}
                                    </small>
                                    <div class="mb-2"><small class="text-muted">@lang('Reference weekly total')</small><br><strong>{{ showAmount($record->return_percent) }}%</strong> · {{ $weeklyBreakdown->count() }} @lang('weeks')</div>
                                    @if($status === 'approved')
                                        <span class="badge badge--success mb-2">@lang('Approved')</span>
                                        <small class="d-block text-muted">@lang('Paid'): {{ $general->cur_sym }}{{ showAmount($record->total_payout) }}</small>
                                    @elseif($status === 'pending')
                                        <span class="badge badge--warning mb-2">@lang('Pending Approval')</span>
                                        <small class="d-block text-muted mb-2">@lang('Est. payout'): {{ $general->cur_sym }}{{ showAmount($record->total_payout) }}</small>
                                        <a href="{{ route('admin.strategy.payout.details', $record->id) }}" class="btn btn--primary btn-sm w-100">@lang('Review & Approve')</a>
                                    @elseif($status === 'rejected')
                                        <span class="badge badge--danger mb-2">@lang('Rejected')</span>
                                    @else
                                        <small class="text-muted">@lang('Draft')</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted mb-0">@lang('No payout batches for this year yet. They appear automatically when an investor\'s payout date arrives and weekly returns are approved.')</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <form action="{{ route('admin.strategy.period.returns.save', $plan->id) }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn--primary">@lang('Refresh Due Batches')</button>
                        </form>
                        <a href="{{ route('admin.strategy.payouts') }}" class="btn btn--warning">@lang('Pending Payout Approvals')</a>
                        <a href="{{ route('admin.plan.index') }}" class="btn btn--dark">@lang('Back')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    @if($plan->usesMonthlyPerformanceTracking())
        <a href="{{ route('admin.strategy.monthly.returns', $plan->id) }}" class="btn btn-sm btn-outline--info">@lang('Monthly Performance')</a>
    @else
        <a href="{{ route('admin.strategy.weekly.returns', $plan->id) }}" class="btn btn-sm btn-outline--info">@lang('Weekly Performance')</a>
    @endif
    <a href="{{ route('admin.strategy.payouts') }}" class="btn btn-sm btn-outline--warning">@lang('Pending Payouts')</a>
@endpush
