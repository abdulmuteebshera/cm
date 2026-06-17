@extends($activeTemplate . 'layouts.master')
@section('content')
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


    <div class="row gy-4">
        <div class="col-md-5">
            <div class="card custom--card h-100">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <p class="mb-2 fw-bold">@lang('Total Invest')</p>
                            <h4 class="text--base"><sup
                                    class="top-0 fw-light me-1">{{ $general->cur_sym }}</sup>{{ showAmount(auth()->user()->invests->sum('amount')) }}
                            </h4>
                        </div>
                        <div>
                            <p class="mb-2 fw-bold">@lang('Total Profit')</p>
                            <h4 class="text--base"><sup
                                    class="top-0 fw-light me-1">{{ $general->cur_sym }}</sup>{{ showAmount(auth()->user()->transactions()->where('remark', 'interest')->sum('amount')) }}
                            </h4>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between mt-3 mt-sm-4 gap-2">
                        <a href="{{ route('plan') }}" class="btn btn--sm btn--base">@lang('Invest Now') <i
                                class="las la-arrow-right fs--12px ms-1"></i></a>
                        <a href="{{ route('user.withdraw') }}" class="btn btn--sm btn--white">@lang('Withdraw Now') <i
                                class="las la-arrow-right fs--12px ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card custom--card h-100">
                <div class="card-body">
                    @if ($investChart->count())
                        <div class="invest-statistics d-flex flex-wrap justify-content-between align-items-center">
                            <div class="flex-shrink-0">
                                @foreach ($investChart as $chart)
                                    <p class="my-2"><i
                                            class="fas fa-plane planPoint me-2"></i>{{ showAmount(($chart->investAmount / $investChart->sum('investAmount')) * 100) }}%
                                        - {{ __($chart->plan->name) }}</p>
                                @endforeach
                            </div>
                            <div class="invest-statistics__chart">
                                <canvas height="150" id="chartjs-pie-chart" style="width: 150px;"></canvas>
                            </div>
                        </div>
                    @else
                        <h3 class="text-center">@lang('No Investment Found Yet')</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="text-end mb-4">
                <a href="{{ route('user.invest.log') }}" class="btn btn--base btn-sm">
                    @lang('Investment Log')
                </a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="dashboard-table">
                <table class="table transection__table table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Plan')</th>
                            <th>@lang('Return')</th>
                            <th>@lang('Received')</th>
                            <th>@lang('Next payment')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invests as $invest)
                            <tr>
                                <td>{{ __($invest->plan->name) }} <br> {{ showAmount($invest->amount) }}
                                    {{ __($general->cur_text) }} </td>
                                <td>
                                    {{ showAmount($invest->interest) }} {{ __($general->cur_text) }}
                                    @lang('every') {{ $invest->time_name }}
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
                                    {{ $invest->return_rec_time * $invest->interest }}
                                    {{ __($general->cur_text) }} </td>

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
                                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                                style="width: {{ diffDatePercent($start, $invest->next_time) }}%"
                                                aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-success">@lang('Completed')</span>
                                    @endif
                                </td>
                                @php
                                    $nextTime = \Carbon\Carbon::parse($invest->next_time);
                                @endphp
                            </tr>
                            @if ($nextTime > now())
                                <script>
                                    createCountDown('counter<?php echo $invest->id; ?>', {{ $nextTime->diffInSeconds() }});
                                </script>
                            @endif
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $invests->links() }}
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script src="{{ asset('assets/global/js/chart.min.js') }}"></script>


    <script>
        /* -- Chartjs - Pie Chart -- */
        var pieChartID = document.getElementById("chartjs-pie-chart").getContext('2d');
        var pieChart = new Chart(pieChartID, {
            type: 'pie',
            data: {
                datasets: [{
                    data: [
                        @foreach ($investChart as $chart)
                            {{ $chart->investAmount }},
                        @endforeach
                    ],
                    borderColor: 'transparent',
                    backgroundColor: planColors(),
                    label: 'Dataset 1'
                }],
                labels: [
                    @foreach ($investChart as $chart)
                        '{{ $chart->plan->name }}',
                    @endforeach
                ]
            },
            options: {
                responsive: true,
                legend: {
                    display: false
                }
            }
        });

        var planPoints = $('.planPoint');
        planPoints.each(function(key, planPoint) {
            var planPoint = $(planPoint)
            planPoint.css('color', planColors()[key])
        })

        function planColors() {
            return [
                '#ff7675',
                '#6c5ce7',
                '#ffa62b',
                '#ffeaa7',
                '#D980FA',
                '#fccbcb',
                '#45aaf2',
                '#05dfd7',
                '#FF00F6',
                '#1e90ff',
                '#2ed573',
                '#eccc68',
                '#ff5200',
                '#cd84f1',
                '#7efff5',
                '#7158e2',
                '#fff200',
                '#ff9ff3',
                '#08ffc8',
                '#3742fa',
                '#1089ff',
                '#70FF61',
                '#bf9fee',
                '#574b90'
            ]
        }
    </script>

    <script>
        let animationCircle = $('.animation-circle');
        animationCircle.css('animation-duration', function() {
            let duration = ($(this).data('duration'));
            return duration;
        });
    </script>
@endpush
