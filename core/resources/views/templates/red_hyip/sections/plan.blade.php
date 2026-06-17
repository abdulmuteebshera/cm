  @php
      $planContent = getContent('plan.content', true);

      $plans = App\Models\Plan::where('status', 1)
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
  <section class="plan-section">
      <div class="plan-section__inner py-120">
          <div class="container">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="section-heading">
                          <h2 class="section-heading__title">{{ __(@$planContent->data_values->heading) }}</h2>
                          <p class="section-heading__desc">{{ __(@$planContent->data_values->sub_heading) }}</p>
                      </div>
                  </div>
              </div>
              <div class="row gy-4 justify-content-center">
                  @include($activeTemplate . 'partials.plan', ['plans' => $plans])
              </div>
          </div>
      </div>
  </section>
