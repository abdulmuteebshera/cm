@extends("$activeTemplate.layouts.$layout")
@section('content')
@php
    $depositBalance  = auth()->check() ? auth()->user()->deposit_wallet : 0;
    $interestBalance = auth()->check() ? auth()->user()->interest_wallet : 0;
@endphp
<div class="dashboard-inner quant-dashboard quant-invest-page {{ $layout == 'frontend' ? 'container pt-120 pb-120' : '' }}">
    <div class="quant-header quant-invest-page__header">
        <div class="quant-header__main">
            <h3 class="quant-invest-title">@lang('Investment Strategies')</h3>
            <p class="quant-invest-sub">@lang('Deploy capital from your deposit wallet. Returns are calculated from approved weekly performance and paid each period after admin review.')</p>
        </div>
        <div class="quant-header__meta quant-invest-page__actions">
            @auth
                <a href="{{ route('user.invest.statistics') }}" class="quant-invest-link-btn">@lang('My Portfolio') <i class="las la-arrow-right"></i></a>
            @endauth
        </div>
    </div>

    @auth
        <div class="quant-invest-wallet-bar">
            <div class="quant-invest-wallet-bar__item">
                <span class="quant-invest-wallet-bar__label">@lang('Deposit Wallet')</span>
                <strong>{{ $general->cur_sym }}{{ showAmount($depositBalance) }}</strong>
                <small>@lang('Available to invest')</small>
            </div>
            <div class="quant-invest-wallet-bar__divider"></div>
            <div class="quant-invest-wallet-bar__item">
                <span class="quant-invest-wallet-bar__label">@lang('Interest Wallet')</span>
                <strong>{{ $general->cur_sym }}{{ showAmount($interestBalance) }}</strong>
                <small>@lang('Approved returns')</small>
            </div>
            <div class="quant-invest-wallet-bar__divider"></div>
            <div class="quant-invest-wallet-bar__cta">
                <a href="{{ route('user.deposit.index') }}" class="quant-strategy-card__btn quant-strategy-card__btn--sm">
                    <i class="las la-plus"></i> @lang('Add Funds')
                </a>
            </div>
        </div>
    @endauth

    <div class="row g-4 quant-strategy-grid">
        @include($activeTemplate.'partials.plan', ['plans' => $plans])
    </div>
</div>
@endsection
