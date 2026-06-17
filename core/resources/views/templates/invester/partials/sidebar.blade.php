@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp
<div class="dashboard-sidebar dashboard-sidebar--modern" id="dashboard-sidebar">
    <button class="btn-close dash-sidebar-close d-xl-none" aria-label="Close menu"></button>

    <div class="sidebar-brand">
        <a href="{{ route('user.home') }}" class="sidebar-brand__link">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="{{ $general->site_name }}">
        </a>
    </div>

    <div class="sidebar-balance-card">
        <span class="sidebar-balance-card__label">@lang('Account Balance')</span>
        <div class="sidebar-balance-card__row">
            <span class="sidebar-balance-card__key">@lang('Deposit Wallet')</span>
            <span class="sidebar-balance-card__value">{{ showAmount(auth()->user()->deposit_wallet) }} <small>{{ $general->cur_text }}</small></span>
        </div>
        <div class="sidebar-balance-card__row">
            <span class="sidebar-balance-card__key">@lang('Interest Wallet')</span>
            <span class="sidebar-balance-card__value">{{ showAmount(auth()->user()->interest_wallet) }} <small>{{ $general->cur_text }}</small></span>
        </div>
        <div class="sidebar-balance-card__actions">
            <a href="{{ route('user.deposit.index') }}" class="sidebar-btn sidebar-btn--primary">@lang('Deposit')</a>
            <a href="{{ route('user.withdraw') }}" class="sidebar-btn sidebar-btn--ghost">@lang('Withdraw')</a>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Dashboard navigation">
        <p class="sidebar-nav__label">@lang('Overview')</p>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}">
                    <span class="sidebar-menu__icon"><i class="las la-th-large"></i></span>
                    <span class="sidebar-menu__text">@lang('Dashboard')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.invest.statistics') }}" class="{{ menuActive(['user.invest.statistics', 'user.invest.log', 'plan', 'user.invest.details']) }}">
                    <span class="sidebar-menu__icon"><i class="las la-chart-line"></i></span>
                    <span class="sidebar-menu__text">@lang('Investments')</span>
                </a>
            </li>
            @if($general->schedule_invest)
            <li>
                <a href="{{ route('user.invest.schedule') }}" class="{{ menuActive('user.invest.schedule') }}">
                    <span class="sidebar-menu__icon"><i class="las la-calendar-check"></i></span>
                    <span class="sidebar-menu__text">@lang('Schedule Investments')</span>
                </a>
            </li>
            @endif
        </ul>

        <p class="sidebar-nav__label">@lang('Wallet')</p>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('user.deposit.index') }}" class="{{ menuActive('user.deposit*') }}">
                    <span class="sidebar-menu__icon"><i class="las la-wallet"></i></span>
                    <span class="sidebar-menu__text">@lang('Deposit')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.withdraw') }}" class="{{ menuActive('user.withdraw*') }}">
                    <span class="sidebar-menu__icon"><i class="las la-money-bill-wave"></i></span>
                    <span class="sidebar-menu__text">@lang('Withdraw')</span>
                </a>
            </li>
            @if ($general->b_transfer)
            <li>
                <a href="{{ route('user.transfer.balance') }}" class="{{ menuActive('user.transfer.balance') }}">
                    <span class="sidebar-menu__icon"><i class="las la-exchange-alt"></i></span>
                    <span class="sidebar-menu__text">@lang('Transfer Balance')</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}">
                    <span class="sidebar-menu__icon"><i class="las la-list-alt"></i></span>
                    <span class="sidebar-menu__text">@lang('Transactions')</span>
                </a>
            </li>
        </ul>

        <p class="sidebar-nav__label">@lang('Insights')</p>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('user.strategy.performance') }}" class="{{ menuActive('user.strategy.performance') }}">
                    <span class="sidebar-menu__icon"><i class="las la-chart-area"></i></span>
                    <span class="sidebar-menu__text">@lang('Strategy Performance')</span>
                </a>
            </li>
            @if ($general->user_ranking)
            <li>
                <a href="{{ route('user.invest.ranking') }}" class="{{ menuActive('user.invest.ranking') }}">
                    <span class="sidebar-menu__icon"><i class="las la-trophy"></i></span>
                    <span class="sidebar-menu__text">@lang('Ranking')</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('user.referrals') }}" class="{{ menuActive('user.referrals') }}">
                    <span class="sidebar-menu__icon"><i class="las la-user-friends"></i></span>
                    <span class="sidebar-menu__text">@lang('Referrals')</span>
                </a>
            </li>
            @if ($general->promotional_tool && $promotionCount)
            <li>
                <a href="{{ route('user.promotional.banner') }}" class="{{ menuActive('user.promotional.banner') }}">
                    <span class="sidebar-menu__icon"><i class="las la-bullhorn"></i></span>
                    <span class="sidebar-menu__text">@lang('Promotional Banner')</span>
                </a>
            </li>
            @endif
        </ul>

        <p class="sidebar-nav__label">@lang('Account')</p>
        <ul class="sidebar-menu sidebar-menu--account">
            <li>
                <a href="{{ route('ticket.index') }}" class="{{ menuActive(['ticket.index', 'ticket.view', 'ticket.open']) }}">
                    <span class="sidebar-menu__icon"><i class="las la-headset"></i></span>
                    <span class="sidebar-menu__text">@lang('Support Ticket')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.twofactor') }}" class="{{ menuActive('user.twofactor') }}">
                    <span class="sidebar-menu__icon"><i class="las la-shield-alt"></i></span>
                    <span class="sidebar-menu__text">@lang('2FA')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}">
                    <span class="sidebar-menu__icon"><i class="las la-user-circle"></i></span>
                    <span class="sidebar-menu__text">@lang('Profile')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.change.password') }}" class="{{ menuActive('user.change.password') }}">
                    <span class="sidebar-menu__icon"><i class="las la-lock"></i></span>
                    <span class="sidebar-menu__text">@lang('Change Password')</span>
                </a>
            </li>
            <li>
                <a href="{{ route('user.logout') }}" class="sidebar-menu__logout">
                    <span class="sidebar-menu__icon"><i class="las la-sign-out-alt"></i></span>
                    <span class="sidebar-menu__text">@lang('Logout')</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
