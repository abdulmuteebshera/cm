@php
    $planContent = getContent('plan.content', true);
    $plans = App\Models\Plan::with('timeSetting')
        ->whereHas('timeSetting', function ($time) {
            $time->where('status', 1);
        })
        ->where('status', 1)
        ->where('featured', 1)
        ->get();
    $gatewayCurrency = null;
    if (auth()->check()) {
        $gatewayCurrency = App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })
            ->with('method')
            ->orderby('method_code')
            ->get();
    }
@endphp

<section class="plan-section pt-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading style-two">
                    <span class="section-heading__subtitle">
                       {{__(@$planContent->data_values->sub_heading)}}
                    </span>
                    <h2 class="section-heading__title">
                        {{__(@$planContent->data_values->heading)}}
                    </h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center gy-4">
            @include($activeTemplate . 'partials.plan', ['plans' => $plans])

        </div>
    </div>
</section>

