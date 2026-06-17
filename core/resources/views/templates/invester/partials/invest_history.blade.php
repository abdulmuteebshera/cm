<div class="quant-invest-list">
    @forelse($invests as $invest)
        @php
            $isStrategy = $invest->plan && $invest->plan->isStrategy();
            $start = $invest->last_time ?: $invest->created_at;
        @endphp

        @if($isStrategy)
            <div class="quant-invest-card {{ $invest->status == 1 ? 'is-active' : 'is-complete' }}">
                <div class="quant-invest-card__top">
                    <div class="quant-invest-card__identity">
                        <div class="quant-invest-card__icon"><i class="las la-microchip"></i></div>
                        <div>
                            <div class="quant-invest-card__name">{{ __($invest->plan->name) }}</div>
                            <div class="quant-invest-card__meta">{{ $invest->plan->payoutFrequencyLabel() }} · {{ showDateTime($invest->created_at, 'M d, Y') }}</div>
                        </div>
                    </div>
                    <span class="quant-invest-card__status {{ $invest->status == 1 ? 'is-live' : '' }}">
                        {{ $invest->status == 1 ? __('Active') : __('Completed') }}
                    </span>
                </div>
                <div class="quant-invest-card__stats">
                    <div class="quant-invest-card__stat">
                        <span>@lang('Invested')</span>
                        <strong>{{ $general->cur_sym }}{{ showAmount($invest->amount) }}</strong>
                    </div>
                    <div class="quant-invest-card__stat">
                        <span>@lang('Payout Cycle')</span>
                        <strong>{{ $invest->plan->payoutFrequencyLabel() }}</strong>
                    </div>
                    <div class="quant-invest-card__stat">
                        <span>@lang('Returns Received')</span>
                        <strong>{{ $general->cur_sym }}{{ showAmount($invest->paid) }}</strong>
                    </div>
                    <div class="quant-invest-card__stat">
                        <span>@lang('Next Period End')</span>
                        <strong>{{ showDateTime($invest->next_time, 'M d, Y') }}</strong>
                    </div>
                </div>
                <div class="quant-invest-card__actions">
                    <a href="{{ route('user.invest.details', encrypt($invest->id)) }}" class="quant-invest-card__btn quant-invest-card__btn--ghost">@lang('Details')</a>
                </div>
            </div>
        @else
            <div class="plan-item-two">
                <div class="plan-info plan-inner-div">
                    <div class="d-flex align-items-center gap-3">
                        @if ($invest->status == 1)
                            <svg class="custom-progress">
                                <circle class="progress-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: calc(100 - (({{ diffDatePercent($start, $invest->next_time) }} * 100)/100))" ; />
                                <circle class="bg-circle" cx="20" cy="22" r="16" style="stroke-dasharray: 100; stroke-dashoffset: 0"; />
                            </svg>
                        @endif
                        <div class="plan-name-data">
                            <div class="plan-name fw-bold">{{ __($invest->plan->name) }} - @lang('Every') {{ __($invest->time_name) }} {{ $invest->plan->interest_type != 1 ? $general->cur_sym : '' }}{{ showAmount($invest->plan->interest) }}{{ $invest->plan->interest_type == 1 ? '%' : '' }}
                                @lang('for') @if ($invest->plan->lifetime == 0)
                                    {{ __($invest->plan->repeat_time) }} {{ __($invest->plan->timeSetting->name ?? '') }}
                                @else
                                    @lang('LIFETIME')
                                @endif
                            </div>
                            <div class="plan-desc">@lang('Invested'): <span class="fw-bold">{{ showAmount($invest->amount) }} {{ $general->cur_text }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="plan-start plan-inner-div">
                    <p class="plan-label">@lang('Start Date')</p>
                    <p class="plan-value date">{{ showDateTime($invest->created_at, 'M d, Y h:i A') }}</p>
                </div>
                <div class="plan-inner-div">
                    <p class="plan-label">@lang('Next Return')</p>
                    <p class="plan-value">{{ showDateTime($invest->next_time, 'M d, Y h:i A') }}</p>
                </div>
                <div class="plan-inner-div text-end">
                    <p class="plan-label">@lang('Total Return')</p>
                    <p class="plan-value amount">{{ showAmount($invest->paid) }} {{ $general->cur_text }}</p>
                </div>
                <div class="plan-inner-div text-end justify-content-end">
                    <a href="{{ route('user.invest.details', encrypt($invest->id)) }}" class="invest-details-link"><i class="las la-angle-right"></i></a>
                </div>
            </div>
        @endif
    @empty
        <div class="quant-invest-empty">
            <i class="las la-folder-open"></i>
            <h5>@lang('No investments yet')</h5>
            <p>@lang('Deploy capital into a strategy to start tracking performance here.')</p>
            <a href="{{ route('plan') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">@lang('Browse Strategies')</a>
        </div>
    @endforelse
</div>

@if(isset($invests) && method_exists($invests, 'hasPages') && $invests->hasPages())
    <div class="quant-invest-pagination">
        {{ paginateLinks($invests) }}
    </div>
@endif

<div class="modal fade" id="capitalModal">
    <div class="modal-dialog modal-dialog-centered modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Manage Invest Capital')</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <form action="{{ route('user.invest.capital.manage') }}" method="post">
                @csrf
                <input type="hidden" name="invest_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Capital')</label>
                        <select name="capital" class="form-control form--control" required>
                            <option value="reinvest">@lang('Reinvest')</option>
                            <option value="capital_back">@lang('Capital Back')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--base">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    (function($) {
        "use strict";
        $('.manageCapital').on('click', function() {
            var modal = $('#capitalModal');
            modal.find('[name=invest_id]').val($(this).data('id'));
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
