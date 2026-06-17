


@foreach ($plans as $k => $plan)
    @php
        $timeName = \App\Models\TimeSetting::where('time', $plan->time)->first();
    @endphp



    <div class="col-xl-4 col-md-6 gy-3">
        <div class="plan-item">
            <h3 class="plan-item__parcentage">@lang('Profit'):
                {{ $plan->interest_type != 1 ? $general->cur_sym : '' }}{{ showAmount($plan->interest) }}{{ $plan->interest_type == 1 ? '%' : '' }}
            </h3>
            <div class="plan-item__shape"></div>
            <div class="plan-item__card">
                <div class="plan-item__card-inner">
                    <div class="plan-item__content">
                        <div class="plan-item__header">
                            <h3 class="plan-item__title">{{ __($plan->name) }}</h3>
                        </div>
                        <div class="plan-item__body">
                            <ul class="text-list">
                                <li class="text-list__item"> <span class="icon"><i
                                            class="fas fa-check-circle"></i></span> @lang('Every')
                                    {{ __($timeName->name) }} </li>
                                <li class="text-list__item"> <span class="icon"><i
                                            class="fas fa-check-circle"></i></span>@lang('For')

                                    @if ($plan->lifetime == 0)
                                        {{ __($plan->repeat_time) }} {{ __($timeName->name) }}
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </li>
                                <li class="text-list__item"> <span class="icon"><i
                                            class="fas fa-check-circle"></i></span>
                                    @if ($plan->lifetime == 0)
                                        @lang('Total')
                                        {{ $plan->interest_type != 1 ? $general->cur_sym : '' }}{{ __($plan->interest * $plan->repeat_time) }}{{ $plan->interest_type == 1 ? '%' : '' }}
                                        @if ($plan->capital_back == 1)
                                            +
                                            <span class="badge badge--base ">
                                                @lang('Capital')
                                            </span>
                                        @endif
                                    @else
                                        @lang('Unlimited Earning')
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="plan-item__footer">
                        <div class="plan-item__footer-inner">
                            @if ($plan->fixed_amount == 0)
                                <span class="plan-item__footer-text d-block">@lang('Min :')
                                    {{ $general->cur_sym }}{{ showAmount($plan->minimum) }}</span>
                                <span class="plan-item__footer-text d-block">@lang('Max :')
                                    {{ $general->cur_sym }}{{ showAmount($plan->maximum) }}</span>
                            @else
                                <span class="plan-item__footer-text d-block">@lang('Fixed :')
                                    {{ $general->cur_sym }}{{ showAmount($plan->fixed_amount) }}</span>
                                <div class="mb-5"></div>
                            @endif

                            <a href="javascript:void(0)" class="btn btn--white pill investModal" data-bs-toggle="modal"
                                data-plan={{ $plan }} data-bs-target="#investModal">@lang('Invest Now')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach


<div class="modal custom--modal fade" id="investModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if (auth()->check())
                    <strong class="modal-title " id="ModalLabel">
                        @lang('Confirm to invest on') <span class="planName"></span>
                    </strong>
                @else
                    <strong class="modal-title " id="ModalLabel">
                        @lang('At first login your account')
                    </strong>
                @endif
                <button class="close" class="text--base" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">

                <form action="{{ route('user.invest.submit') }}" method="post">
                    @csrf
                    <input type="hidden" name="plan_id">
                    @if (auth()->check())
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="text-center investAmountRange"></h6>
                                <p class="text-center mt-1 interestDetails"></p>
                                <p class="text-center interestValidity"></p>

                                <label>@lang('Select Wallet')</label>
                                <select class="form--control  select" name="wallet_type" required>
                                    <option value="">@lang('Select One')</option>
                                    @if (auth()->user()->deposit_wallet > 0)
                                        <option value="deposit_wallet">@lang('Deposit Wallet - ' . $general->cur_sym . showAmount(auth()->user()->deposit_wallet))</option>
                                    @endif
                                    @if (auth()->user()->interest_wallet > 0)
                                        <option value="interest_wallet">@lang('Interest Wallet -' . $general->cur_sym . showAmount(auth()->user()->interest_wallet))</option>
                                    @endif
                                    @foreach ($gatewayCurrency as $data)
                                        <option value="{{ $data->id }}" @selected(old('wallet_type') == $data->method_code)
                                            data-gateway="{{ $data }}">{{ $data->name }}</option>
                                    @endforeach
                                </select>
                                <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ $general->cur_text }}
                                    = <span class="rate"></span> <span class="method_currency"></span></code>
                            </div>
                            <div class="form-group">
                                <label>@lang('Invest Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control form--control" name="amount"
                                        required>
                                    <span class="input-group-text bg--base">{{ $general->cur_text }}</span>
                                </div>
                                <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                    {{ $general->cur_text }}. @lang('Total amount'): <span class="total"></span>
                                    {{ $general->cur_text }}</code>
                            </div>
                        </div>
                        @else
                         <p>@lang('Login Required')</p>
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                @if (auth()->check())
                    <button type="button" class="btn btn-secondary btn-sm text-black"
                        data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--base btn-sm">@lang('Yes')</button>
                    @else
                    <a href="{{ route('user.login') }}"
                        class="btn btn--base btn-sm h-45 w-100 text-center">@lang('Login Now')</a>

                @endif
            </div>
        </div>
    </div>
</div>



@push('style')
    <style>


        .form--control:disabled,
        .form--control[readonly] {
            background-color: hsl(var(--base-two-d-200));
            opacity: 1;
            border: 1px solid hsl(var(--white)/0.15);
        }

        .custom--modal .modal-body .select {
            margin-bottom: 10px;
        }

        .custom--modal .modal-body label {
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            $('.investModal').click(function() {
                var symbol = '{{ $general->cur_sym }}';
                var currency = '{{ $general->cur_text }}';
                $('.gateway-info').addClass('d-none');
                var modal = $('#investModal');
                var plan = $(this).data('plan');
                modal.find('.planName').text(plan.name)
                modal.find('[name=plan_id]').val(plan.id);
                let fixedAmount = parseFloat(plan.fixed_amount).toFixed(2);
                let minimumAmount = parseFloat(plan.minimum).toFixed(2);
                let maximumAmount = parseFloat(plan.maximum).toFixed(2);
                let interestAmount = parseFloat(plan.interest);

                if (plan.fixed_amount > 0) {
                    modal.find('.investAmountRange').text(`Invest: ${symbol}${fixedAmount}`);
                    modal.find('[name=amount]').val(fixedAmount);
                    modal.find('[name=amount]').attr('readonly', true);
                } else {
                    modal.find('.investAmountRange').text(
                        `Invest: ${symbol}${minimumAmount} - ${symbol}${maximumAmount}`);
                    modal.find('[name=amount]').val('');
                    modal.find('[name=amount]').removeAttr('readonly');
                }

                if (plan.interest_type == '1') {
                    modal.find('.interestDetails').html(`<strong> Interest: ${interestAmount}% </strong>`);
                } else {
                    modal.find('.interestDetails').html(
                        `<strong> Interest: ${interestAmount} ${currency}  </strong>`);
                }
                if (plan.lifetime_status == '0') {
                    modal.find('.interestValidity').html(
                        `<strong>  Per ${plan.time} hours ,  ${plan.repeat_time} times</strong>`);
                } else {
                    modal.find('.interestValidity').html(
                        `<strong>  Per ${plan.time} hours,  life time </strong>`);
                }

            });

            $('[name=amount]').on('input', function() {
                $('[name=wallet_type]').trigger('change');
            })

            $('[name=wallet_type]').change(function() {
                var amount = $('[name=amount]').val();
                if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' && amount) {
                    var resource = $('select[name=wallet_type] option:selected').data('gateway');
                    var fixed_charge = parseFloat(resource.fixed_charge);
                    var percent_charge = parseFloat(resource.percent_charge);
                    var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                    $('.charge').text(charge);
                    $('.rate').text(parseFloat(resource.rate));
                    $('.gateway-info').removeClass('d-none');
                    if (resource.currency == '{{ $general->cur_text }}') {
                        $('.rate-info').addClass('d-none');
                    } else {
                        $('.rate-info').removeClass('d-none');
                    }
                    $('.method_currency').text(resource.currency);
                    $('.total').text(parseFloat(charge) + parseFloat(amount));
                } else {
                    $('.gateway-info').addClass('d-none');
                }
            });
        })(jQuery);
    </script>
@endpush
