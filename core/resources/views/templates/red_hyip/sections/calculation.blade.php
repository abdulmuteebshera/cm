@php
    $calculationContent = getContent('calculation.content', true);
    $planList = \App\Models\Plan::where('status', 1)
        ->orderBy('id', 'desc')
        ->get();
@endphp

<div class="calculator-section py-60">
    <div class="calculator-section__images">
        <img src="{{ getImage('assets/images/frontend/calculation/' . @$calculationContent->data_values->image_left,'380x235') }}" alt="" class="calculator-section__img one">
        <img src="{{ getImage('assets/images/frontend/calculation/' . @$calculationContent->data_values->image_right,'415x300') }}" alt="" class="calculator-section__img two">
    </div>
    <div class="container">
        <div class="calculator-form">
            <form action="#">
                <div class="row gy-lg-4 gy-3 justify-content-center">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form--label">@lang('Select Plan')</label>
                            <select class="select" aria-label="Default select example" id="changePlan">
                                @foreach ($planList as $plan)
                                <option value="{{ $plan->id }}" data-fixed_amount="{{ $plan->fixed_amount }}"
                                    data-minimum_amount="{{ $plan->minimum }}"
                                    data-maximum_amount="{{ $plan->maximum }}"> {{ $plan->name }}</option>
                            @endforeach
                            </select>
                            <div class="button-list">
                                <span class="button-list__item profit-input text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form--label" for="amount">@lang('Enter The Amount')</label>
                         <input type="text" class="form--control invest-input" placeholder="0.00"
                         onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="amount">
                         <div class="button-list">
                             <span class="button-list__item text--danger period"></span>
                         </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                var curSym = '{{ $general->cur_sym }}';
                $("#changePlan").on('change', function() {
                    var selectedPlan  = $('#changePlan').find(':selected');
                    var planId        = selectedPlan.val();
                    var data          = selectedPlan.data();
                    var fixedAmount   = parseFloat(data.fixed_amount).toFixed(2);
                    var minimumAmount = parseFloat(data.minimum_amount).toFixed(2);
                    var maximumAmount = parseFloat(data.maximum_amount).toFixed(2);

                    if (fixedAmount > 0) {
                        $('.invest-input').val(fixedAmount);
                        $('.invest-input').attr('readonly', true);
                        $('.invest-range').text('');
                    } else {
                        $('.invest-input').val(minimumAmount);
                        $('.invest-input').attr('readonly', false);
                        $('.invest-range').text('(' + curSym + minimumAmount + ' - ' + curSym +
                            maximumAmount + ')');
                    }

                    var investAmount = $('.invest-input').val();
                    var profitInput = $('.profit-input').text('');

                    $('.period').text('');

                    if (investAmount != '' && planId != null) {
                        ajaxPlanCalc(planId, investAmount)
                    }
                }).change();

                $(".invest-input").on('change', function() {
                    var planId       = $("#changePlan option:selected").val();
                    var investAmount = $(this).val();
                    var profitInput  = $('.profit-input').text('');
                    $('.period').text('');
                    if (investAmount != '' && planId != null) {
                        ajaxPlanCalc(planId, investAmount)
                    }
                });
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
                        } else {
                            var msg = `${response.description}`
                            $('.profit-input').text(msg);
                            if (response.netProfit) {
                                var periodSym = `{{__($general->cur_text)}}`;
                                $('.period').text('Net Profit ' + response.netProfit +' '+ periodSym);
                            }
                        }
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
