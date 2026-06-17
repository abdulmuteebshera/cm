@foreach ($plans as $data)
    <div class="col-lg-3 col-sm-6 col-xsm-6">
        <div class="plan-item">
            <div class="plan-item__header">
                <p class="return-title" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Return On Invest')">
                    @lang('ROI')</p>
                <span class="plan-item__title">
                    {{ showAmount($data->interest) }}{{ $data->interest_type == 1 ? '%' : ' ' . __($general->cur_text) }}
                </span>
                <h4 class="plan-item__name"> {{ __($data->name) }} </h4>
            </div>
            <div class="plan-item__body">
                <div class="plan-item__info">
                    <h5 class="plan-item__time">
                        @if ($data->lifetime == 0)
                            {{ __($data->repeat_time) }} {{ __($data->timeSetting->name) }}
                        @else
                            @lang('Lifetime')
                        @endif
                    </h5>
                    <div>
                        @if ($data->fixed_amount == 0)
                            <p class="plan-item__amount">@lang('Min'):
                                {{ $general->cur_sym }}{{ showAmount($data->minimum) }} </p>
                            <p class="plan-item__amount">@lang('Max'):
                                {{ $general->cur_sym }}{{ showAmount($data->maximum) }} </p>
                        @else
                            <p class="plan-item__amount">@lang('Fixed'):
                                {{ $general->cur_sym }}{{ showAmount($data->fixed_amount) }} </p>
                        @endif
                    </div>
                </div>
                <ul class="plan-item__list">
                    <li class="plan-item__list-inner">@lang('Return')
                        {{ showAmount($data->interest) }}{{ $data->interest_type == 1 ? '%' : ' ' . __($general->cur_text) }}
                    </li>
                    <li class="plan-item__list-inner">
                        @if ($data->lifetime == 0)
                            {{ __($data->repeat_time) }} {{ __($data->timeSetting->name) }}
                        @else
                            @lang('Lifetime')
                        @endif
                    </li>
                    <li class="plan-item__list-inner">
                        @if ($data->lifetime == 0)
                            @lang('Total')
                            {{ $data->interest * $data->repeat_time }}{{ $data->interest_type == 1 ? '%' : ' ' . __($general->cur_text) }}
                            @if ($data->capital_back == 1)
                                +
                                <span class="badge badge--success">@lang('Capital')</span>
                            @endif
                        @else
                            @lang('Lifetime Earning')
                        @endif
                    </li>
                </ul>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#investModal"
                    data-plan="{{ $data }}"
                    class="btn btn--base pill outline outlineinvestModal">@lang('Invest Now')</a>
            </div>
        </div>
    </div>
@endforeach

<div class="modal custom--modal fade" id="investModal">
    <div class="modal-dialog modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                @if (auth()->check())
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('Confirm to invest on') <span class="planName"></span>
                    </strong>
                @else
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('At first sign in your account')
                    </strong>
                @endif
                <button type="button" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.invest.submit') }}" method="post">
                @csrf
                <input type="hidden" name="plan_id">
                @if (auth()->check())
                    <div class="modal-body">
                        <div>
                            <h6 class="text-center investAmountRange"></h6>
                            <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>
                        </div>
                        <div class="form-group">
                            <label>@lang('Select Wallet')</label>
                            <select class="form--control methodWallet" name="wallet_type" required>
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
                                =
                                <span class="rate"></span> <span class="method_currency"></span></code>
                        </div>

                        <div class="form-group">
                            <label>@lang('Invest Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control form--control investAmount"
                                    name="amount" required>
                                <div class="input-group-text bg--base">{{ $general->cur_text }}</div>
                            </div>
                            <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                {{ $general->cur_text }}. @lang('Total amount'): <span class="total"></span>
                                {{ $general->cur_text }}</code>
                        </div>

                    </div>
                @endif
                <div class="modal-footer">
                    @if (auth()->check())
                        <button type="button" class="btn btn--danger btn--sm pill"
                            data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base btn--sm pill">@lang('Yes')</button>
                    @else
                        <a href="{{ route('user.login') }}"
                            class="btn btn--base pill w-100 text-center">@lang('At first sign in your account')</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict"

            $(document).ready(function() {
                $('.outlineinvestModal').click(function() {
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
                        modal.find('.interestDetails').html(
                            `<strong> Interest: ${interestAmount}% </strong>`);
                    } else {
                        modal.find('.interestDetails').html(
                            `<strong> Interest: ${interestAmount} ${currency}  </strong>`);
                    }

                    if (plan.lifetime == '0') {
                        modal.find('.interestValidity').html(
                            `<strong>  Per ${plan.time_setting.time} hours ,  ${plan.repeat_time} times</strong>`
                        );
                    } else {
                        modal.find('.interestValidity').html(
                            `<strong>  Per ${plan.time_setting.time} hours,  life time </strong>`);
                    }
                });


                $('.investAmount').on('input', function() {
                    $('.methodWallet').trigger('change');
                })

                $('.methodWallet').change(function() {

                    var amount = $('.investAmount').val();
                    if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' &&
                        amount) {
                        var resource = $('.methodWallet option:selected').data('gateway');
                        var fixed_charge = parseFloat(resource.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(
                            2);
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
            })
        })(jQuery);
    </script>
@endpush
