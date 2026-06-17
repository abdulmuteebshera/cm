@php
    $calculationContent = getContent('calculation.content', true);
    $planList = \App\Models\Plan::whereHas('timeSetting', function ($time) {
        $time->where('status', 1);
    })
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->with('timeSetting')
        ->get();

    $gatewayCurrency = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
        $gate->where('status', 1);
    })
        ->with('method')
        ->orderby('name')
        ->get();

@endphp

<section class="investment-section bg-img pt-120"
    style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/invest.png') }});">
    <div class="shape-one">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest01.png') }}" alt="">
    </div>
    <div class="shape-two">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest02.png') }}" alt="">
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center flex-wrap">
            <div class="col-lg-10">
                <div class="investment-inner">
                    <div class="plan-card">
                        <div class="plan-card__shape"></div>
                        <div class="plan-card__shape-one"></div>

                        <h4 class="calculator__title">{{ __($calculationContent->data_values->heading) }}</h4>
                        <div class="plan-card-select">
                            <select class="select" id="changePlan">
                                <option value="" selected disabled>@lang('Select plan')</option>
                                @foreach ($planList as $k => $data)
                                    <option value="{{ $data->id }}" data-interest-yes="{{ $data->interest_type }}"
                                        data-interest="{{ $data->interest }}" data-lifetime="{{ $data->lifetime }}"
                                        data-repeat="{{ $data->lifetime }}" data-name="{{ $data->name }}"
                                        data-time-name="{{ $data->timeSetting->name }}"
                                        data-fixed_amount="{{ $data->fixed_amount }}"
                                        data-minimum_amount="{{ $data->minimum }}"
                                        data-maximum_amount="{{ $data->maximum }}"> {{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <h3 class="title">
                            <span class="interest">00.00% @lang('Hourly')</span>
                        </h3>
                        <h5 class="subtitle"> @lang('For Lifetime') </h5>
                        <ul class="plan-list">
                            <input type="hidden" step="any" class="form-control form--control invest-input"
                                name="amount" required>

                            <li class="plan-list__item range"> @lang('Invest'): <span class="invest-range">
                                    {{ $general->cur_sym }}00
                                </span></li>
                            <li class="plan-list__item"> @lang('Profit Info'): <br> <span
                                    class="profit-input">{{ $general->cur_sym }}00</span>
                            </li>
                            <li class="plan-list__item net-pro"> @lang('Net profit'): <br> <span
                                    class="net-profit">{{ $general->cur_sym }}00</span>
                            </li>
                        </ul>
                    </div>

                    <div class="calculator">
                        <div class="calculator__shape"></div>
                        <h4 class="calculator__title">@lang('Invest a Best Plan')</h4>
                        <form action="{{ route('user.invest.submit') }}" method="post">
                            @csrf
                            <input type="hidden" name="plan_id">
                            <div class="cal-area">
                                <div class="input-wrap">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <select class="user-wallets form--control" name="wallet_type" required>
                                            <option value=""> @lang('Select Method') </option>
                                            @guest
                                                <option value="1"> @lang('Deposit Wallet') </option>
                                                <option value="2"> @lang('Interest Wallet') </option>
                                                <option value="3"> @lang('Checkout') </option>
                                            @endguest
                                            @if (auth()->user()?->deposit_wallet > 0)
                                                <option value="deposit_wallet">@lang('Deposit Wallet - ' . $general->cur_sym . showAmount(auth()->user()->deposit_wallet))</option>
                                            @endif
                                            @if (auth()->user()?->interest_wallet > 0)
                                                <option value="interest_wallet">@lang('Interest Wallet -' . $general->cur_sym . showAmount(auth()->user()->interest_wallet))</option>
                                            @endif
                                            @if (auth()->user())
                                                @foreach ($gatewayCurrency as $data)
                                                    <option value="{{ $data->id }}" @selected(old('wallet_type') == $data->method_code)
                                                        data-gateway="{{ $data }}">{{ $data->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="position-relative">
                                        <label for="number" id="number" class="form-label"></label>
                                        <input type="number" id="number" name="amount"
                                            class="form--control invest-amount" required>
                                        <span class="cal-area__icon">
                                            {{ __($general->cur_text) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="profit-result">
                                    <span class="profit">@lang('Net Profit')</span>
                                    <h4 class="plan-net-profit"> 00 {{ $general->cur_text }}</h4>
                                </div>
                            </div>
                            <code class="method-charge d-none"></code>
                            <div class="cal-bottom-area">
                                <div class="profit-cal">
                                    <div class="profit-cal__button">
                                        @auth
                                            <button type="submit" class=" btn btn--base pill investNow"> @lang('Invest Now')
                                            </button>
                                        @else
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#investModalCheck" data-plan="{{ $data }}"
                                                class="btn btn--base pill outline outlineinvestModal">@lang('Invest Now')</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal custom--modal fade" id="investModalCheck">
        <div class="modal-dialog modal-dialog-centered modal-content-bg">
            <div class="modal-content">
                <div class="modal-header text-end">
                    <h5 class="text--danger"> @lang('Required')</h5>
                    <button type="button" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('user.login') }}"
                        class="btn btn--base pill w-100 text-center">@lang('At first sign in your account')</a>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        (function($) {
            "use strict";
            var curSym = `{{ $general->cur_sym }}`;
            $(document).ready(function() {
                $('.net-pro').hide();
                $(document).on('change', '#changePlan', function() {
                    var selectedPlan = $('#changePlan').find(':selected');
                    var planId = selectedPlan.val();
                    var data = selectedPlan.data();

                    $('[name=plan_id]').val(planId);

                    var fixedAmount = parseFloat(data.fixed_amount).toFixed(2);
                    var minimumAmount = parseFloat(data.minimum_amount).toFixed(2);
                    var maximumAmount = parseFloat(data.maximum_amount).toFixed(2);

                    var interest = parseFloat(data.interest).toFixed(2);
                    if (data.interestYes == 1) {
                        var interestAmount = interest + '%';
                    } else {
                        var interestAmount = interest + ' ' + `{{ $general->cur_text }}`;
                    }
                    $('.interest').text(interestAmount);

                    var timeName = data.timeName;
                    if (data.lifetime == 0) {
                        $('.subtitle').text(`@lang('For ')` + timeName);
                    } else {
                        $('.subtitle').text(timeName + ' ' + data.repeat + ' ' + `@lang('for Lifetime')`);
                        $('.plan-net-profit').text(`@lang('Unlimited')`);

                    }
                    if (fixedAmount > 0) {
                        $('.net-pro').hide();
                        $('.invest-input').val(fixedAmount);
                        $('.invest-range').text(curSym + fixedAmount);
                        $('.invest-amount').val(fixedAmount);
                        $('.invest-amount').attr('readonly', true);
                    } else {
                        $('.net-pro').show();
                        $('.invest-input').val(minimumAmount);
                        $('.invest-range').text('(' + curSym + minimumAmount + ' - ' + curSym +
                            maximumAmount + ')');
                        $('.invest-amount').val(minimumAmount);
                        $('.invest-amount').attr('readonly', false);
                        if (data.lifetime == 1) {
                            $('.net-pro').hide();
                        }
                    }
                    var investAmount = $('.invest-input').val();
                    var profitInput = $('.profit-input').text('');

                    $('.period').text('');

                    if (investAmount != '' && planId != null) {
                        ajaxPlanCalc(planId, investAmount)
                    }
                    $('.plan-name').text(data.name);
                    $('.method-charge').addClass('d-none');
                }).change();
            });


            $(".invest-amount").on('input', function() {
                var selectedPlan = $('#changePlan').find(':selected');
                var planId = selectedPlan.val();
                var data = selectedPlan.data();
                var rangeAmount = $(this).val();
                if (rangeAmount > data.maximum_amount || rangeAmount < data.minimum_amount) {
                    $('.investNow').prop('disabled', true);
                } else {
                    $('.investNow').prop('disabled', false);
                }


                if (!planId) {
                    iziToast.error({
                        message: 'Please seleact a plan',
                        position: "topRight"
                    });
                    return;
                }

                var gatVal = $('select[name=wallet_type] option:selected').val();
                var resource = $('select[name=wallet_type] option:selected').data('gateway');
                if (resource == null && !gatVal) {
                    iziToast.error({
                        message: 'Please seleact a payment method',
                        position: "topRight"
                    });
                } else {
                    var fixed_charge = parseFloat(resource?.fixed_charge);
                    var percent_charge = parseFloat(resource.percent_charge);
                    var charge = parseFloat(fixed_charge + (rangeAmount * percent_charge / 100)).toFixed(2);
                    $('.method-charge').removeClass('d-none');
                    var total = parseFloat(charge) + parseFloat(rangeAmount);

                    $('.method-charge').text('@lang('Charge'):' + ' ' + curSym + parseFloat(charge) + ' ' +
                        '@lang('Total:')' + ' ' + curSym + parseFloat(rangeAmount ? total : 0).toFixed(2)
                    );
                }
            })


            $('.user-wallets').on('change', function() {
                var amount = $('[name=amount]').val();
                if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' && amount) {

                    var gatVal = $('select[name=wallet_type] option:selected').val();
                    var resource = $('select[name=wallet_type] option:selected').data('gateway');
                    if (resource == null && !gatVal) {
                        iziToast.error({
                            message: 'Please seleact a payment method',
                            position: "topRight"
                        });
                    } else {
                        var fixed_charge = parseFloat(resource?.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                        $('.method-charge').removeClass('d-none');
                        var total = parseFloat(charge) + parseFloat(amount ? amount : 0);

                        $('.method-charge').text('@lang('Charge'):' + ' ' + curSym + charge + ' ' +
                            '@lang('Total:')' + ' ' + curSym + parseFloat(total ? total : 0).toFixed(2));
                    }
                } else {
                    $('.method-charge').addClass('d-none');
                }
            });

            $(".invest-amount").on('change', function() {
                var planId = $("#changePlan option:selected").val();
                var investAmount = $(this).val();
                var profitInput = $('.profit-input').text('');
                $('.period').text('');
                if (investAmount != '' && planId != null) {
                    ajaxPlanCalc(planId, investAmount)
                }
            });


            function ajaxPlanCalc(planId, investAmount) {
                $.ajax({
                    url: "{{ route('planCalculator') }}",
                    type: "post",
                    data: {
                        planId,
                        _token: '{{ csrf_token() }}',
                        investAmount
                    },
                    success: function(response) {
                        var alertStatus = "{{ $general->alert }}";
                        if (response.errors) {
                            iziToast.error({
                                message: response.errors,
                                position: "topRight"
                            });
                            $('.investNow').prop('disabled', true);
                        } else {
                            $('.investNow').prop('disabled', false);
                            var msg = `${response.description}`
                            var curText = `{{ $general->cur_text }}`
                            $('.profit-input').text(msg);
                            if (response.netProfit) {
                                $('.net-profit').text(parseFloat(response.netProfit).toFixed(2) + ' ' +
                                    curText);
                                $('.plan-net-profit').text(parseFloat(response.netProfit).toFixed(2) + ' ' +
                                    curText);
                            }
                        }
                    }
                });
            }

        })(jQuery);
    </script>
@endpush
