@extends('admin.layouts.app')
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
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Investors')</span><strong>{{ $record->payout_status === 'approved' ? $record->payoutItems->count() : ($preview['count'] ?? 0) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Total Payout')</span><strong>{{ $general->cur_sym }}{{ showAmount($record->payout_status === 'approved' ? $record->total_payout : ($preview['total'] ?? 0)) }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between px-0"><span>@lang('Status')</span>
                            @if($record->payout_status === 'pending')
                                <span class="badge badge--warning">@lang('Pending Approval')</span>
                            @elseif($record->payout_status === 'approved')
                                <span class="badge badge--success">@lang('Approved')</span>
                            @else
                                <span class="badge badge--danger">{{ ucfirst($record->payout_status) }}</span>
                            @endif
                        </li>
                    </ul>
                    @if($record->isApprovable())
                        <form action="{{ route('admin.strategy.period.approve', $record->id) }}" method="post" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn--success w-100 confirmationBtn" data-question="@lang('Approve this period payout? Amount will be credited to investor interest wallets.')">@lang('Approve Period Payout')</button>
                        </form>
                        <form action="{{ route('admin.strategy.period.reject', $record->id) }}" method="post" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn--danger w-100 confirmationBtn" data-question="@lang('Reject this period payout?')">@lang('Reject')</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Invest Amount')</th>
                                    <th>@lang('Period Payout')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($record->payout_status === 'approved')
                                    @forelse($record->payoutItems as $item)
                                        <tr>
                                            <td>{{ @$item->user->username }}</td>
                                            <td>{{ $general->cur_sym }}{{ showAmount(@$item->invest->amount) }}</td>
                                            <td class="fw-bold">{{ $general->cur_sym }}{{ showAmount($item->amount) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="100%" class="text-center text-muted">@lang('No payout lines')</td></tr>
                                    @endforelse
                                @else
                                    @forelse($preview['lines'] ?? [] as $line)
                                        <tr>
                                            <td>{{ @$line['user']->username }}</td>
                                            <td>{{ $general->cur_sym }}{{ showAmount($line['invest']->amount) }}</td>
                                            <td class="fw-bold">{{ $general->cur_sym }}{{ showAmount($line['amount']) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="100%" class="text-center text-muted">@lang('No active investments for this period')</td></tr>
                                    @endforelse
                                @endif
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
