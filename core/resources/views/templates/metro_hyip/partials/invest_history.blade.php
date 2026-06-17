<script>
    "use strict"

    function createCountDown(elementId, sec) {
        var tms = sec;
        var x = setInterval(function() {
            var distance = tms * 1000;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById(elementId).innerHTML = days + "d: " + hours + "h " + minutes + "m " +
                seconds + "s ";
            if (distance < 0) {
                clearInterval(x);
                document.getElementById(elementId).innerHTML = "COMPLETE";
            }
            tms--;
        }, 1000);
    }
</script>

<div class="col-md-12">
    <div class="table-section">
        <table class="table style-two table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('Plan')</th>
                    <th>@lang('Return')</th>
                    <th>@lang('Received')</th>
                    <th>@lang('Next payment')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invests as $invest)
                    <tr>
                        <td>{{ __($invest->plan->name) }} <br> {{ showAmount($invest->amount) }}
                            {{ __($general->cur_text) }} </td>
                        <td>
                            {{ showAmount($invest->interest) }} {{ __($general->cur_text) }} @lang('every')
                            {{ $invest->time_name }}
                            <br>
                            @lang('for')
                            @if ($invest->period == '-1')
                                @lang('Lifetime')
                            @else
                                {{ $invest->period }}
                                {{ $invest->time_name }}
                            @endif
                            @if ($invest->capital_status == '1')
                                + @lang('Capital')
                            @endif
                        </td>
                        <td> {{ $invest->return_rec_time }}x{{ showAmount($invest->interest) }} =
                            {{ $invest->return_rec_time * $invest->interest }} {{ __($general->cur_text) }} </td>

                        <td scope="row" class="fw-bold">
                            @if ($invest->status == '1')
                                <p id="counter{{ $invest->id }}" class="demo countdown timess2 "></p>

                                @php
                                    if ($invest->last_time) {
                                        $start = $invest->last_time;
                                    } else {
                                        $start = $invest->created_at;
                                    }
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped bg--base" role="progressbar"
                                        style="width: {{ diffDatePercent($start, $invest->next_time) }}%"
                                        aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            @else
                                <span class="badge badge--success">@lang('Completed')</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('user.invest.details', encrypt($invest->id)) }}"
                                class="btn btn--base btn--sm">
                                <i class="las la-desktop"></i>
                            </a>
                        </td>

                    </tr>
                    @if (\Carbon\Carbon::parse($invest->next_time) > now())
                        <script>
                            createCountDown('counter<?php echo $invest->id; ?>', {{ \Carbon\Carbon::parse($invest->next_time)->diffInSeconds() }});
                        </script>
                    @endif
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
