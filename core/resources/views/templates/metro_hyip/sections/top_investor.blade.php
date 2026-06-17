@php
    $topInvestor = \App\Models\Invest::with('user')
        ->selectRaw('SUM(amount) as totalAmount, user_id')
        ->orderBy('totalAmount', 'desc')
        ->groupBy('user_id')
        ->limit(8)
        ->get();
    
    $topInvestorContent = getContent('top_investor.content', true);
    
@endphp

<section class="investor-section pt-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <span class="section-heading__subtitle">{{ __(@$topInvestorContent->data_values->sub_heading) }}
                    </span>
                    <h2 class="section-heading__title">{{ __(@$topInvestorContent->data_values->heading) }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-4 align-items-center justify-content-center">
            @foreach ($topInvestor as $k => $data)
                <div class="col-lg-3 col-md-4 col-sm-6 col-xsm-6">
                    <div class="investor-item">
                        <span class="investor-item__number">{{ ordinal($loop->iteration) }}</span>
                        <h4 class="investor-item__title"> {{ $data->user->fullname }} </h4>
                        <span class="investor-item__usd"> {{ $general->cur_sym }}{{ showAmount($data->totalAmount) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
