@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Strategy')</th>
                                    <th>@lang('Period')</th>
                                    <th>@lang('Cycle')</th>
                                    <th>@lang('Return %')</th>
                                    <th>@lang('Est. / Paid')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodReturns as $row)
                                    <tr>
                                        <td>{{ __($row->plan->name) }}</td>
                                        <td>{{ $row->periodLabel() }}</td>
                                        <td>{{ $row->plan->payoutFrequencyLabel() }}</td>
                                        <td>{{ showAmount($row->return_percent) }}%</td>
                                        <td>
                                            @if($row->payout_status === 'pending')
                                                {{ $general->cur_sym }}{{ showAmount($row->total_payout) }}
                                                <small class="text-muted d-block">@lang('Est.')</small>
                                            @else
                                                {{ $general->cur_sym }}{{ showAmount($row->total_payout) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->payout_status === 'pending')
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @elseif($row->payout_status === 'approved')
                                                <span class="badge badge--success">@lang('Approved')</span>
                                            @elseif($row->payout_status === 'rejected')
                                                <span class="badge badge--danger">@lang('Rejected')</span>
                                            @else
                                                <span class="badge badge--dark">@lang('Draft')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.strategy.payout.details', $row->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-eye"></i></a>
                                            @if($row->isApprovable())
                                                <form action="{{ route('admin.strategy.period.approve', $row->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Approve this period payout and credit investors?')"><i class="las la-check"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="text-muted text-center" colspan="100%">@lang('No period returns found')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($periodReturns->hasPages())
                    <div class="card-footer">{{ paginateLinks($periodReturns) }}</div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="btn-group">
        <a href="{{ route('admin.strategy.payouts', ['status' => 'pending']) }}" class="btn btn-sm btn-outline--warning">@lang('Pending') @if($pendingCount)<span class="badge badge--danger">{{ $pendingCount }}</span>@endif</a>
        <a href="{{ route('admin.strategy.payouts', ['status' => 'approved']) }}" class="btn btn-sm btn-outline--success">@lang('Approved')</a>
        <a href="{{ route('admin.strategy.payouts', ['status' => 'all']) }}" class="btn btn-sm btn-outline--primary">@lang('All')</a>
    </div>
@endpush
