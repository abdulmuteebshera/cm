@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard quant-invest-page">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <h3 class="quant-invest-title">@lang('Investment History')</h3>
            <p class="quant-invest-sub">@lang('Complete log of your strategy investments and payout cycles.')</p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            <a href="{{ route('user.invest.statistics') }}" class="quant-invest-link-btn">
                <i class="las la-arrow-left"></i> @lang('Portfolio')
            </a>
            <a href="{{ route('plan') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">
                <i class="las la-plus"></i> @lang('Invest')
            </a>
        </div>
    </div>

    <div class="quant-panel quant-panel--portfolio">
        <div class="quant-panel__body quant-panel__body--portfolio">
            @include($activeTemplate.'partials.invest_history',['invests'=>$invests])
        </div>
    </div>
</div>
@endsection
