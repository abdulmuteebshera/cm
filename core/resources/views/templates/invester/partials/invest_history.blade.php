<div class="quant-portfolio-list">
    @forelse($invests as $invest)
        @php
            $isStrategy = $invest->plan && $invest->plan->isStrategy();
            $start = $invest->last_time ?: $invest->created_at;
            $principal = (float) $invest->initial_amount;
            $returns = (float) $invest->paid;
            $returnPct = $principal > 0 ? round(($returns / $principal) * 100, 2) : 0;
            $progress = $invest->status == 1 ? min(100, max(0, diffDatePercent($start, $invest->next_time))) : 100;
        @endphp

        @if($isStrategy)
            <article class="quant-portfolio-item {{ $invest->status == 1 ? 'is-active' : 'is-complete' }}">
                <div class="quant-portfolio-item__header">
                    <div class="quant-portfolio-item__brand">
                        <div class="quant-portfolio-item__icon" aria-hidden="true">
                            <i class="las la-microchip"></i>
                        </div>
                        <div class="quant-portfolio-item__titles">
                            <h6 class="quant-portfolio-item__name">{{ __($invest->plan->name) }}</h6>
                            <p class="quant-portfolio-item__meta">
                                <span class="quant-portfolio-item__freq">{{ $invest->plan->payoutFrequencyLabel() }}</span>
                                <span class="quant-portfolio-item__sep">&middot;</span>
                                <span>@lang('Started') {{ showDateTime($invest->created_at, 'M d, Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="quant-portfolio-item__chips">
                        @if($returns > 0)
                            <span class="quant-portfolio-chip quant-portfolio-chip--gain">+{{ showAmount($returnPct) }}%</span>
                        @endif
                        <span class="quant-portfolio-chip {{ $invest->status == 1 ? 'quant-portfolio-chip--live' : '' }}">
                            @if($invest->status == 1)
                                <span class="quant-live-dot quant-live-dot--active"></span>
                            @endif
                            {{ $invest->status == 1 ? __('Active') : __('Completed') }}
                        </span>
                    </div>
                </div>

                <div class="quant-portfolio-item__metrics">
                    <div class="quant-portfolio-metric">
                        <span class="quant-portfolio-metric__label">@lang('Invested')</span>
                        <strong class="quant-portfolio-metric__value">{{ $general->cur_sym }}{{ showAmount($invest->initial_amount) }}</strong>
                    </div>
                    <div class="quant-portfolio-metric quant-portfolio-metric--gain">
                        <span class="quant-portfolio-metric__label">@lang('Returns')</span>
                        <strong class="quant-portfolio-metric__value">{{ $general->cur_sym }}{{ showAmount($invest->paid) }}</strong>
                    </div>
                    <div class="quant-portfolio-metric">
                        <span class="quant-portfolio-metric__label">@lang('Current Value')</span>
                        <strong class="quant-portfolio-metric__value">{{ $general->cur_sym }}{{ showAmount($invest->amount) }}</strong>
                    </div>
                    <div class="quant-portfolio-metric quant-portfolio-metric--accent">
                        <span class="quant-portfolio-metric__label">@lang('Next Payout')</span>
                        <strong class="quant-portfolio-metric__value">{{ showDateTime($invest->next_time, 'M d, Y') }}</strong>
                    </div>
                </div>

                @if($invest->status == 1)
                    <div class="quant-portfolio-item__cycle">
                        <div class="quant-portfolio-item__cycle-head">
                            <span>@lang('Current cycle')</span>
                            <span>{{ showAmount($progress) }}%</span>
                        </div>
                        <div class="quant-portfolio-item__cycle-track">
                            <div class="quant-portfolio-item__cycle-bar" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @endif

                <div class="quant-portfolio-item__footer">
                    <a href="{{ route('user.invest.details', encrypt($invest->id)) }}" class="quant-portfolio-item__link quant-portfolio-item__link--primary">
                        <i class="las la-file-alt"></i>
                        <span>@lang('View Details')</span>
                        <i class="las la-arrow-right quant-portfolio-item__link-arrow"></i>
                    </a>
                    <a href="{{ route('user.strategy.performance') }}" class="quant-portfolio-item__link">
                        <i class="las la-chart-area"></i>
                        <span>@lang('Strategy Performance')</span>
                    </a>
                </div>
            </article>
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
                            <div class="plan-name fw-bold">{{ __($invest->plan->name) }}</div>
                            <div class="plan-desc">@lang('Invested'): <span class="fw-bold">{{ showAmount($invest->amount) }} {{ $general->cur_text }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="plan-start plan-inner-div">
                    <p class="plan-label">@lang('Start Date')</p>
                    <p class="plan-value date">{{ showDateTime($invest->created_at, 'M d, Y') }}</p>
                </div>
                <div class="plan-inner-div">
                    <p class="plan-label">@lang('Next Return')</p>
                    <p class="plan-value">{{ showDateTime($invest->next_time, 'M d, Y') }}</p>
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
        <div class="quant-portfolio-empty">
            <div class="quant-portfolio-empty__icon"><i class="las la-chart-area"></i></div>
            <h5>@lang('No investments yet')</h5>
            <p>@lang('Deploy capital into a Crownmaire strategy to track returns, payout cycles, and portfolio value here.')</p>
            <a href="{{ route('plan') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">
                <i class="las la-plus"></i> @lang('Browse Strategies')
            </a>
        </div>
    @endforelse
</div>

@if(isset($invests) && method_exists($invests, 'hasPages') && $invests->hasPages())
    <div class="quant-portfolio-pagination">
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
