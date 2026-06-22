@extends($activeTemplate.'layouts.master')
@section('content')
@php
    $rate = rtrim(rtrim(number_format($commissionRate, 2, '.', ''), '0'), '.');
@endphp
<div class="dashboard-inner quant-dashboard">

    <div class="quant-header">
        <div class="quant-header__main">
            <h4 class="mb-1">@lang('Referral Program')</h4>
            <p class="text-muted mb-0">@lang('Earn a straight :rate% every time someone you refer invests', ['rate' => $rate])</p>
        </div>
        <div class="quant-header__aside">
            <div class="quant-header__meta">
                <a href="{{ route('user.home') }}" class="quant-panel__link">@lang('Back to Dashboard') <i class="las la-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="ref-stats mb-4">
        <div class="ref-stat">
            <span class="ref-stat__icon ref-stat__icon--blue"><i class="las la-users"></i></span>
            <div class="ref-stat__body">
                <span class="ref-stat__value">{{ $totalReferrals }}</span>
                <span class="ref-stat__label">@lang('Total Referrals')</span>
            </div>
        </div>
        <div class="ref-stat">
            <span class="ref-stat__icon ref-stat__icon--green"><i class="las la-user-check"></i></span>
            <div class="ref-stat__body">
                <span class="ref-stat__value">{{ $activeReferrals }}</span>
                <span class="ref-stat__label">@lang('Active Investors')</span>
            </div>
        </div>
        <div class="ref-stat">
            <span class="ref-stat__icon ref-stat__icon--gold"><i class="las la-coins"></i></span>
            <div class="ref-stat__body">
                <span class="ref-stat__value">{{ showAmount($commissionEarned) }} <small>{{ $general->cur_text }}</small></span>
                <span class="ref-stat__label">@lang('Commission Earned')</span>
            </div>
        </div>
        <div class="ref-stat">
            <span class="ref-stat__icon ref-stat__icon--purple"><i class="las la-percentage"></i></span>
            <div class="ref-stat__body">
                <span class="ref-stat__value">{{ $rate }}%</span>
                <span class="ref-stat__label">@lang('Commission Rate')</span>
            </div>
        </div>
    </div>

    {{-- Hero / pitch --}}
    <div class="quant-panel ref-hero mb-4">
        <div class="quant-panel__body">
            <div class="ref-hero__inner">
                <div class="ref-hero__content">
                    <span class="ref-hero__badge"><i class="las la-gift"></i> @lang('Invite & Earn')</span>
                    <h3 class="ref-hero__title">@lang('Get :rate% commission — for life', ['rate' => $rate])</h3>
                    <p class="ref-hero__text">@lang('Share your unique link with friends and fellow investors. The moment they invest in any Crownmaire Capital strategy, you instantly earn a straight :rate% commission on their investment — credited directly to your wallet. No limits on how many people you can refer, and no cap on what you can earn.', ['rate' => $rate])</p>

                    <div class="ref-link-label">@lang('Your Referral Link')</div>
                    <div class="ref-copy">
                        <input type="text" class="copyURL" value="{{ route('home') }}?reference={{ auth()->user()->username }}" readonly>
                        <button type="button" class="ref-copy__btn copyBoard" id="copyBoard"><i class="las la-copy"></i> <span class="copyText">@lang('Copy')</span></button>
                    </div>
                </div>
                <div class="ref-hero__visual">
                    <div class="ref-hero__circle">
                        <span class="ref-hero__circle-rate">{{ $rate }}%</span>
                        <span class="ref-hero__circle-text">@lang('Instant Commission')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <div class="quant-panel mb-4">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('How It Works')</h5>
                <p class="quant-panel__desc">@lang('Three simple steps to start earning')</p>
            </div>
        </div>
        <div class="quant-panel__body">
            <div class="ref-steps">
                <div class="ref-step">
                    <span class="ref-step__num">1</span>
                    <h6 class="ref-step__title">@lang('Share Your Link')</h6>
                    <p class="ref-step__text">@lang('Send your personal referral link to friends, family and your investor network.')</p>
                </div>
                <div class="ref-step">
                    <span class="ref-step__num">2</span>
                    <h6 class="ref-step__title">@lang('They Invest')</h6>
                    <p class="ref-step__text">@lang('When they sign up and invest in any strategy, your referral is activated.')</p>
                </div>
                <div class="ref-step">
                    <span class="ref-step__num">3</span>
                    <h6 class="ref-step__title">@lang('You Earn :rate%', ['rate' => $rate])</h6>
                    <p class="ref-step__text">@lang('You instantly receive :rate% of their investment as commission in your wallet.', ['rate' => $rate])</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Referral network --}}
    @if($user->allReferrals->count() > 0 && $maxLevel > 0)
    <div class="quant-panel">
        <div class="quant-panel__head quant-panel__head--aligned">
            <div>
                <h5 class="quant-panel__title">@lang('Your Referral Network')</h5>
                <p class="quant-panel__desc">@lang('Everyone you have invited')</p>
            </div>
        </div>
        <div class="quant-panel__body">
            <div class="treeview-container">
                <ul class="treeview">
                    <li class="items-expanded"> {{ $user->fullname }} ( {{ $user->username }} )
                        @include($activeTemplate.'partials.under_tree',['user'=>$user,'layer'=>0,'isFirst'=>true])
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @else
    <div class="quant-panel">
        <div class="quant-panel__body">
            <div class="ref-empty">
                <i class="las la-user-plus"></i>
                <div>
                    <strong>@lang('No referrals yet')</strong>
                    <p class="mb-0">@lang('Share your link above and start earning :rate% on every investment your referrals make.', ['rate' => $rate])</p>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('style')
    <link href="{{ asset('assets/global/css/jquery.treeView.css') }}" rel="stylesheet" type="text/css">
<style>
    .ref-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .ref-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 14px;
        padding: 16px;
    }
    .ref-stat__icon {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #fff;
    }
    .ref-stat__icon--blue { background: #1989BE; }
    .ref-stat__icon--green { background: #16a34a; }
    .ref-stat__icon--gold { background: #d4a017; }
    .ref-stat__icon--purple { background: #8b5cf6; }
    .ref-stat__body { display: flex; flex-direction: column; min-width: 0; }
    .ref-stat__value {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--quant-text, #0f172a);
        line-height: 1.2;
    }
    .ref-stat__value small { font-size: 0.7rem; font-weight: 500; color: var(--quant-text-muted, #94a3b8); }
    .ref-stat__label {
        font-size: 0.78rem;
        color: var(--quant-text-muted, #64748b);
    }
    .ref-hero {
        background: linear-gradient(135deg, #0f3d57 0%, #1989BE 100%);
        border: none;
        color: #fff;
        overflow: hidden;
    }
    .ref-hero .quant-panel__body { position: relative; }
    .ref-hero__inner {
        display: flex;
        align-items: center;
        gap: 28px;
    }
    .ref-hero__content { flex: 1 1 auto; min-width: 0; }
    .ref-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        margin-bottom: 12px;
    }
    .ref-hero__title {
        color: #fff;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .ref-hero__text {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.92rem;
        line-height: 1.7;
        margin-bottom: 18px;
        max-width: 640px;
    }
    .ref-link-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: rgba(255, 255, 255, 0.75);
        margin-bottom: 8px;
        font-weight: 600;
    }
    .ref-copy {
        display: flex;
        gap: 10px;
        max-width: 560px;
    }
    .ref-copy input {
        flex: 1 1 auto;
        min-width: 0;
        border: none;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 0.88rem;
        color: #0f172a;
        background: #fff;
    }
    .ref-copy__btn {
        flex-shrink: 0;
        border: none;
        border-radius: 10px;
        padding: 0 18px;
        font-weight: 600;
        font-size: 0.88rem;
        color: #0f3d57;
        background: #fde6b8;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .ref-copy__btn:hover { background: #f5d68f; }
    .ref-hero__visual { flex-shrink: 0; }
    .ref-hero__circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: rgba(255, 255, 255, 0.12);
        border: 2px dashed rgba(255, 255, 255, 0.4);
    }
    .ref-hero__circle-rate {
        font-size: 2.4rem;
        font-weight: 800;
        line-height: 1;
        color: #fff;
    }
    .ref-hero__circle-text {
        margin-top: 6px;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.85);
    }
    .ref-steps {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .ref-step {
        padding: 18px;
        border-radius: 14px;
        background: rgba(25, 137, 190, 0.05);
        border: 1px solid rgba(25, 137, 190, 0.12);
    }
    .ref-step__num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--quant-primary, #1989BE);
        color: #fff;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .ref-step__title {
        font-weight: 700;
        font-size: 0.98rem;
        color: var(--quant-text, #0f172a);
        margin-bottom: 4px;
    }
    .ref-step__text {
        margin: 0;
        font-size: 0.84rem;
        line-height: 1.55;
        color: var(--quant-text-muted, #64748b);
    }
    .ref-empty {
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--quant-text-muted, #64748b);
    }
    .ref-empty i {
        font-size: 1.75rem;
        color: var(--quant-primary, #1989BE);
    }
    @media (max-width: 991px) {
        .ref-stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 767px) {
        .ref-hero__inner { flex-direction: column-reverse; align-items: flex-start; }
        .ref-steps { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .ref-stats { grid-template-columns: 1fr; }
        .ref-copy { flex-direction: column; }
    }
</style>
@endpush
@push('script')
<script src="{{ asset('assets/global/js/jquery.treeView.js') }}"></script>
<script>
    (function($){
    "use strict"
        $('.treeview').treeView();
        $('.copyBoard').click(function(){
                var copyText = document.getElementsByClassName("copyURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);

                /*For mobile devices*/
                document.execCommand("copy");
                $('.copyText').text('Copied');
                setTimeout(() => {
                    $('.copyText').text('Copy');
                }, 2000);
        });
    })(jQuery);
</script>
@endpush
