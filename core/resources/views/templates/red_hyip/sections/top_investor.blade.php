@php
    $investorContent = getContent('top_investor.content', true);
    $topInvestor = \App\Models\Invest::with('user')
        ->selectRaw('SUM(amount) as totalAmount, user_id')
        ->orderBy('totalAmount', 'desc')
        ->groupBy('user_id')
        ->limit(12)
        ->get();
@endphp

<section class="top-investors-section py-60 bg-img"
    style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/investor-bg.png') }});">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="section-heading style-two">
                    <h2 class="section-heading__title"> {{ __(@$investorContent->data_values->heading) }}</h2>
                    <p class="section-heading__desc"> {{ __(@$investorContent->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            @foreach ($topInvestor as $invest)
                <div class="col-xl-3 col-md-6">
                    <div class="investors-item">
                        <div class="investors-item__content">
                            <span class="investors-item__username"> {{ $invest->user->username }} </span>
                            <h3 class="investors-item__position">{{ ordinal($loop->iteration) }}</h3>
                            <h5 class="investors-item__name"> {{ $invest->user->fullname }} </h5>
                            <h5 class="investors-item__amount mb-0 text--base">@lang('Investment'):
                                {{ $general->cur_sym }}{{ showAmount($invest->totalAmount) }} </h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
