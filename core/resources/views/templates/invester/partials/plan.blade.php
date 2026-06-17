@php use App\Lib\StrategyPayoutService; @endphp
@foreach ($plans as $plan)
    @php
        $usesMonthly = $plan->usesMonthlyPerformanceTracking();
        $approvedRows = $usesMonthly
            ? StrategyPayoutService::approvedMonthlyReturns($plan)
            : StrategyPayoutService::approvedWeeklyReturns($plan);
        $avgReturn = $usesMonthly
            ? StrategyPayoutService::averageMonthlyReturn($plan)
            : StrategyPayoutService::averageWeeklyReturn($plan);
        $ytdReturn = round((float) $approvedRows->sum('return_percent'), 4);
        $displayRows = $approvedRows->sortByDesc(fn ($row) => ($row->year * 100) + ($usesMonthly ? $row->month : $row->week))->take(6);
    @endphp
    <div class="col-xl-4 col-lg-6">
        <div class="quant-strategy-card quant-strategy-card--strategy">
            <div class="quant-strategy-card__head">
                <div class="quant-strategy-card__head-top">
                    <span class="quant-strategy-badge"><i class="las la-microchip"></i> @lang('Strategy')</span>
                    <span class="quant-strategy-badge quant-strategy-badge--muted">{{ $plan->payoutFrequencyLabel() }}</span>
                    @if($approvedRows->isNotEmpty())
                        <span class="quant-strategy-card__ytd {{ $ytdReturn >= 0 ? 'is-profit' : 'is-loss' }}">{{ showAmount($ytdReturn) }}% YTD</span>
                    @endif
                </div>
                <h4 class="quant-strategy-card__title">{{ __($plan->name) }}</h4>
                @if($approvedRows->isNotEmpty())
                    <p class="quant-strategy-card__rate">
                        {{ showAmount($avgReturn) }}%
                        <small>{{ $usesMonthly ? __('avg approved monthly return') : __('avg approved weekly return') }}</small>
                    </p>
                @else
                    <p class="quant-strategy-card__rate quant-strategy-card__rate--muted">
                        — <small>{{ $usesMonthly ? __('Awaiting approved monthly returns') : __('Awaiting approved weekly returns') }}</small>
                    </p>
                @endif
            </div>

            <div class="quant-strategy-card__body">
                <div class="quant-strategy-card__specs">
                    <div class="quant-strategy-card__spec">
                        <span>@lang('Investment Range')</span>
                        <strong>
                            @if($plan->fixed_amount == 0)
                                {{ $general->cur_sym }}{{ showAmount($plan->minimum) }} – {{ $general->cur_sym }}{{ showAmount($plan->maximum) }}
                            @else
                                {{ $general->cur_sym }}{{ showAmount($plan->fixed_amount) }}
                            @endif
                        </strong>
                    </div>
                    <div class="quant-strategy-card__spec">
                        <span>@lang('Fund Source')</span>
                        <strong>@lang('Deposit Wallet')</strong>
                    </div>
                    <div class="quant-strategy-card__spec">
                        <span>@lang('Returns To')</span>
                        <strong>@lang('Interest Wallet')</strong>
                    </div>
                </div>

                @if($displayRows->isNotEmpty())
                    <div class="quant-strategy-card__weeks">
                        <span class="quant-strategy-card__weeks-title">
                            {{ $usesMonthly ? __('Recent Approved Months') : __('Recent Approved Weeks') }}
                            @if($approvedRows->count() > 6)
                                <em>({{ $approvedRows->count() }} @lang('total'))</em>
                            @endif
                        </span>
                        <div class="quant-week-chips">
                            @foreach($displayRows as $row)
                                @if($usesMonthly)
                                    <span class="quant-week-chip {{ $row->return_percent >= 0 ? 'is-profit' : 'is-loss' }}" title="{{ $row->monthLabel() }}">
                                        {{ $row->monthLabel() }} · {{ showAmount($row->return_percent) }}%
                                    </span>
                                @else
                                    <span class="quant-week-chip {{ $row->return_percent >= 0 ? 'is-profit' : 'is-loss' }}" title="{{ StrategyPayoutService::weekDateLabel((int) $row->year, (int) $row->week) }}">
                                        W{{ $row->week }} · {{ showAmount($row->return_percent) }}%
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="quant-strategy-card__footer">
                <button class="quant-strategy-card__btn investModal" data-bs-toggle="modal" data-plan="{{ $plan }}" data-bs-target="#investModal" type="button">
                    @lang('Invest Now') <i class="las la-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
@endforeach

<div class="modal fade" id="investModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-content-bg">
        <div class="modal-content quant-invest-modal">
            <div class="modal-header quant-invest-modal__header">
                <div>
                    <span class="quant-strategy-badge"><i class="las la-microchip"></i> @lang('Strategy')</span>
                    <h5 class="modal-title mb-0 mt-2">
                        @auth
                            @lang('Invest in') <span class="planName"></span>
                        @else
                            @lang('Sign in to invest')
                        @endauth
                    </h5>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <form action="{{ route('user.invest.submit') }}" method="post">
                @csrf
                <input type="hidden" name="plan_id">
                <input type="hidden" name="wallet_type" value="deposit_wallet">
                @auth
                    <div class="modal-body">
                        <div class="quant-invest-modal__grid">
                            <div class="quant-invest-modal__box">
                                <span class="quant-invest-modal__box-label">@lang('Deposit Wallet')</span>
                                <strong>{{ $general->cur_sym }}{{ showAmount(auth()->user()->deposit_wallet) }}</strong>
                            </div>
                            <div class="quant-invest-modal__box">
                                <span class="quant-invest-modal__box-label">@lang('Investment Range')</span>
                                <strong class="investAmountRange">—</strong>
                            </div>
                        </div>

                        <div class="quant-invest-modal__field">
                            <label class="form-label">@lang('Invest Amount')</label>
                            <div class="input-group quant-invest-modal__input">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input type="number" step="any" min="0" class="form-control form--control" name="amount" id="strategyInvestAmount" required placeholder="0.00">
                                <span class="input-group-text">{{ $general->cur_text }}</span>
                            </div>
                            <small class="text-muted">@lang('Funds move from deposit wallet into your active investment')</small>
                        </div>

                        <div class="quant-invest-modal__note">
                            <i class="las la-info-circle"></i>
                            <span>@lang('Returns follow approved period payouts and are credited to your interest wallet after admin approval.')</span>
                        </div>
                    </div>
                @endauth
                <div class="modal-footer quant-invest-modal__footer">
                    @auth
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--base" id="strategyInvestSubmit">@lang('Confirm Investment')</button>
                    @else
                        <a href="{{ route('user.login') }}" class="btn btn--base w-100">@lang('Sign In')</a>
                    @endauth
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
(function($) {
    "use strict";
    var symbol = '{{ $general->cur_sym }}';
    var depositBalance = parseFloat('{{ auth()->check() ? getAmount(auth()->user()->deposit_wallet) : 0 }}');
    var plan;

    $('.investModal').click(function() {
        var modal = $('#investModal');
        plan = $(this).data('plan');
        modal.find('[name=plan_id]').val(plan.id);
        modal.find('.planName').text(plan.name);

        let fixedAmount = parseFloat(plan.fixed_amount);
        let minimumAmount = parseFloat(plan.minimum);
        let maximumAmount = parseFloat(plan.maximum);
        let amountInput = modal.find('[name=amount]');
        let investAll = Math.min(depositBalance, maximumAmount > 0 ? maximumAmount : depositBalance);

        if (plan.fixed_amount > 0) {
            modal.find('.investAmountRange').text(symbol + fixedAmount.toFixed(2));
            amountInput.val(fixedAmount.toFixed(2)).attr('readonly', true);
        } else {
            modal.find('.investAmountRange').text(symbol + minimumAmount.toFixed(2) + ' – ' + symbol + maximumAmount.toFixed(2));
            amountInput.val(investAll > 0 ? investAll.toFixed(2) : '').removeAttr('readonly');
        }

        $('#strategyInvestSubmit').prop('disabled', depositBalance <= 0);
    });
})(jQuery);
</script>
@endpush
