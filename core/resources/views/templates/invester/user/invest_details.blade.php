@extends($activeTemplate . 'layouts.master')
@section('content')
@php
    $plan = $invest->plan;
    $isStrategy = $plan && $plan->isStrategy();
    $principal = (float) $invest->initial_amount;
    $returns = (float) $invest->paid;
    $returnPct = $principal > 0 ? round(($returns / $principal) * 100, 2) : 0;
    $start = $invest->last_time ?: $invest->created_at;
    $progress = $invest->status == 1 ? diffDatePercent($start, $invest->next_time) : 100;
@endphp

@if($isStrategy)
<div class="dashboard-inner quant-dashboard quant-invest-page quant-invest-detail">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <a href="{{ route('user.invest.statistics') }}" class="quant-invest-detail__back">
                <i class="las la-arrow-left"></i> @lang('Portfolio')
            </a>
            <h3 class="quant-invest-title">{{ __($plan->name) }}</h3>
            <p class="quant-invest-sub mb-0">
                {{ $plan->payoutFrequencyLabel() }} · @lang('Started') {{ showDateTime($invest->created_at, 'M d, Y') }}
            </p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            <span class="quant-invest-card__status {{ $invest->status == 1 ? 'is-live' : '' }}">
                @if($invest->status == 1)<span class="quant-live-dot quant-live-dot--active"></span>@endif
                {{ $invest->status == 1 ? __('Active') : __('Completed') }}
            </span>
        </div>
    </div>

    <div class="quant-kpi-grid quant-kpi-grid--invest mb-4">
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-wallet"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Invested')</span>
                <span class="quant-kpi__value">{{ $general->cur_sym }}{{ showAmount($invest->initial_amount) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-chart-line"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Returns')</span>
                <span class="quant-kpi__value text-success">{{ $general->cur_sym }}{{ showAmount($invest->paid) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-layer-group"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Current Value')</span>
                <span class="quant-kpi__value">{{ $general->cur_sym }}{{ showAmount($invest->amount) }}</span>
            </div>
        </div>
        <div class="quant-kpi">
            <div class="quant-kpi__icon quant-kpi__icon--blue"><i class="las la-percentage"></i></div>
            <div class="quant-kpi__body">
                <span class="quant-kpi__label">@lang('Return')</span>
                <span class="quant-kpi__value">{{ $returnPct > 0 ? '+' : '' }}{{ showAmount($returnPct) }}%</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="quant-panel h-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Payout Schedule')</h5>
                </div>
                <div class="quant-panel__body">
                    @if($invest->status == 1)
                        <div class="quant-invest-card__progress quant-invest-card__progress--detail mb-4">
                            <div class="quant-invest-card__progress-head">
                                <span>@lang('Current cycle progress')</span>
                                <span>{{ showAmount(min(100, max(0, $progress))) }}%</span>
                            </div>
                            <div class="quant-invest-card__progress-track">
                                <div class="quant-invest-card__progress-bar" style="width: {{ min(100, max(0, $progress)) }}%"></div>
                            </div>
                        </div>
                    @endif
                    <div class="quant-detail-grid">
                        <div class="quant-detail-grid__item">
                            <span>@lang('Next Payout')</span>
                            <strong>{{ showDateTime($invest->next_time, 'M d, Y') }}</strong>
                        </div>
                        <div class="quant-detail-grid__item">
                            <span>@lang('Payout Frequency')</span>
                            <strong>{{ $plan->payoutFrequencyLabel() }}</strong>
                        </div>
                        <div class="quant-detail-grid__item">
                            <span>@lang('Periods Paid')</span>
                            <strong>{{ $invest->return_rec_time }}</strong>
                        </div>
                        <div class="quant-detail-grid__item">
                            <span>@lang('Last Payout')</span>
                            <strong>{{ $invest->last_time ? showDateTime($invest->last_time, 'M d, Y') : '—' }}</strong>
                        </div>
                        <div class="quant-detail-grid__item">
                            <span>@lang('Transaction ID')</span>
                            <strong class="text-muted">{{ $invest->trx }}</strong>
                        </div>
                        <div class="quant-detail-grid__item">
                            <span>@lang('Wallet')</span>
                            <strong>{{ __(keyToTitle($invest->wallet_type)) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="quant-panel h-100">
                <div class="quant-panel__head quant-panel__head--compact">
                    <h5 class="quant-panel__title">@lang('Strategy')</h5>
                </div>
                <div class="quant-panel__body">
                    <ul class="quant-detail-list">
                        <li><span>@lang('Strategy')</span><strong>{{ __($plan->name) }}</strong></li>
                        <li><span>@lang('Payout')</span><strong>{{ $plan->payoutFrequencyLabel() }}</strong></li>
                        <li><span>@lang('Min. Horizon')</span><strong>@lang('1 Year')</strong></li>
                        <li><span>@lang('Status')</span><strong>{{ $invest->status ? __('Running') : __('Completed') }}</strong></li>
                    </ul>
                    <a href="{{ route('user.strategy.performance') }}" class="quant-invest-card__btn quant-invest-card__btn--block mt-3">
                        @lang('View Strategy Performance') <i class="las la-external-link-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="quant-panel">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Return History')</h5>
                <p class="quant-panel__desc mb-0">@lang('Interest and period payouts for this investment')</p>
            </div>
        </div>
        <div class="quant-panel__body quant-panel__body--activity">
            <div class="quant-activity">
                @forelse($transactions as $trx)
                    <div class="quant-activity__item">
                        <div class="quant-activity__icon {{ $trx->trx_type == '+' ? 'quant-activity__icon--in' : 'quant-activity__icon--out' }}">
                            <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }}"></i>
                        </div>
                        <div class="quant-activity__info">
                            <span class="quant-activity__title">{{ __(keyToTitle($trx->remark)) }}</span>
                            <span class="quant-activity__meta">{{ showDateTime($trx->created_at, 'M d, Y · H:i') }} · #{{ $trx->trx }}</span>
                        </div>
                        <div class="quant-activity__amount {{ $trx->trx_type == '+' ? 'quant-activity__amount--in' : 'quant-activity__amount--out' }}">
                            {{ $trx->trx_type }}{{ showAmount($trx->amount) }}
                        </div>
                    </div>
                @empty
                    <div class="quant-activity__empty">
                        <i class="las la-inbox"></i>
                        <p>@lang('No returns posted yet')</p>
                    </div>
                @endforelse
            </div>
            @if ($transactions->hasPages())
                <div class="quant-invest-pagination mt-3">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
</div>
@else
<div class="dashboard-inner">
    <div>
        <p>@lang('Investment')</p>
        <div class="d-flex flex-wrap justify-content-between mb-4">
            <h3>@lang('Investment Details')</h3>
            @if ($invest->eligibleCapitalBack())
                <button class="btn btn--base btn--smd" data-bs-toggle="modal" data-bs-target="#capitalModal">@lang('Manage Capital')</button>
            @endif
        </div>
    </div>
    <div class="row gy-3">
        <div class="col-xl-4">
            <div class="card custom--card">
                <div class="card-header"><h5 class="title">@lang('Plan Information')</h5></div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">@lang('Plan Name')<span>{{ __($plan->name) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Investable Amount')
                            <span>
                                @if ($plan->fixed_amount > 0)
                                    {{ $general->cur_sym }}{{ showAmount($plan->fixed_amount) }}
                                @else
                                    {{ $general->cur_sym }}{{ showAmount($plan->minimum) }} - {{ $general->cur_sym }}{{ showAmount($plan->maximum) }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Interest')
                            <span>{{ showAmount($plan->interest) }}{{ $plan->interest_type == 1 ? '%' : " $general->cur_text" }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card custom--card">
                <div class="card-header"><h5 class="title">@lang('Basic Information')</h5></div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">@lang('Initial Invest')<span>{{ $general->cur_sym }}{{ showAmount($invest->initial_amount) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">@lang('Current Invest')<span>{{ $general->cur_sym }}{{ showAmount($invest->amount) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">@lang('Invested')<span>{{ showDateTime($invest->created_at) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Status')
                            <span>@if ($invest->status)<span class="badge badge--success">@lang('Running')</span>@else<span class="badge badge--info">@lang('Completed')</span>@endif</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card custom--card">
                <div class="card-header"><h5 class="title">@lang('Other Information')</h5></div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">@lang('Total Paid Amount')<span>{{ $general->cur_sym }}{{ showAmount($invest->paid) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">@lang('Next Pay Time')<span>{{ showDateTime($invest->next_time) }}</span></li>
                        <li class="list-group-item d-flex justify-content-between">@lang('Net Interest')<span>{{ $general->cur_sym }}{{ showAmount($invest->net_interest) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-2 mt-4">@lang('All Interests')</h4>
    <div class="accordion table--acordion" id="transactionAccordion">
        @forelse($transactions as $transaction)
            <div class="accordion-item transaction-item">
                <h2 class="accordion-header" id="h-{{ $loop->iteration }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c-{{ $loop->iteration }}">
                        <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                            <div class="left">
                                <div class="icon tr-icon @if ($transaction->trx_type == '+') icon-success @else icon-danger @endif">
                                    <i class="las la-long-arrow-alt-right"></i>
                                </div>
                                <div class="content">
                                    <h6 class="trans-title">{{ __(keyToTitle($transaction->remark)) }}</h6>
                                    <span class="text-muted font-size--14px mt-2">{{ showDateTime($transaction->created_at, 'M d Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                            <p><b>{{ showAmount($transaction->amount) }} {{ $general->cur_text }}</b></p>
                        </div>
                    </button>
                </h2>
                <div id="c-{{ $loop->iteration }}" class="accordion-collapse collapse" data-bs-parent="#transactionAccordion">
                    <div class="accordion-body">
                        <ul class="caption-list">
                            <li><span class="caption">@lang('Details')</span><span class="value">{{ __($transaction->details) }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="accordion-body text-center">
                <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
            </div>
        @endforelse
    </div>
    @if ($transactions->hasPages())<div class="mt-4">{{ $transactions->links() }}</div>@endif
</div>
@endif

@if ($invest->eligibleCapitalBack())
<div class="modal fade" id="capitalModal">
    <div class="modal-dialog modal-dialog-centered modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Manage Invest Capital')</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <form action="{{ route('user.invest.capital.manage') }}" method="post">
                @csrf
                <input type="hidden" name="invest_id" value="{{ $invest->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Investment Capital')</label>
                        <select name="capital" class="form-control form--control form-select">
                            <option value="reinvest">@lang('Reinvest')</option>
                            <option value="capital_back">@lang('Capital Back')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
