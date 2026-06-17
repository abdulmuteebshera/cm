@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="dashboard-inner quant-dashboard quant-invest-page">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <h3 class="quant-invest-title">@lang('Investment History')</h3>
            <p class="quant-invest-sub">@lang('Complete log of your strategy investments and weekly return cycles.')</p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            <a href="{{ route('user.invest.statistics') }}" class="quant-invest-link-btn">
                <i class="las la-arrow-left"></i> @lang('Portfolio')
            </a>
        </div>
    </div>

    <div class="quant-panel">
        <div class="quant-panel__body">
            @include($activeTemplate.'partials.invest_history',['invests'=>$invests])
        </div>
        @if($invests->hasPages())
            <div class="quant-invest-pagination">{{ $invests->links() }}</div>
        @endif
    </div>
</div>
@endsection
