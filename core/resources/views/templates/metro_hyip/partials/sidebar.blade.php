@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp

<div class="sidebar-menu">
    <span class="sidebar-menu__close d-xl-none d-block"><i class="las la-times"></i></span>
    <!-- Sidebar Logo Start -->
    <div class="sidebar-logo">
        <a href="{{ route('home') }}" class="sidebar-logo__link"> <img
                src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="site-logo"></a>
    </div>
    <!-- Sidebar Logo End -->
    <!--==== account start ==== -->
    <div class="balance">
        <h5 class="balance__title"> @lang('Account Balance') </h5>
        <div class="balance__item">
            <span class="balance__item-wallet"> @lang('Deposit Wallet') </span>
            <h5 class="balance__item-number"> {{ showAmount(auth()->user()->deposit_wallet) }} <span
                    class="balance__item-currency">{{ __($general->cur_text) }}</span> </h5>
        </div>
        <div class="balance__item">
            <span class="balance__item-wallet"> @lang('Interest Wallet') </span>
            <h5 class="balance__item-number"> {{ showAmount(auth()->user()->interest_wallet) }} <span
                    class="balance__item-currency">{{ __($general->cur_text) }}</span> </h5>
        </div>
        <div class="balance__button">
            <a href="{{ route('user.deposit.index') }}" class="balance__button-one style-one"> @lang('Deposit') </a>
            <a href="{{ route('user.withdraw') }}" class="balance__button-one style-two"> @lang('Withdraw') </a>
        </div>
    </div>
    <!--===== account end ===== -->

    <!-- ========= Sidebar Menu Start ================ -->
    <ul class="sidebar-menu-list mt-5">
        <li class="sidebar-menu-list__item {{ menuActive('user.home') }} ">
            <a href="{{ route('user.home') }}" class="sidebar-menu-list__link ">
                <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive(['plan', 'user.invest.statistics', 'user.invest.log', 'user.invest.details']) }} ">
            <a href="{{ route('plan') }}" class="sidebar-menu-list__link ">
                <span class="icon"><i class="fas fa-funnel-dollar"></i></span>
                <span class="text">@lang('Investment')</span>
            </a>
        </li>
        <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.deposit.*') }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-wallet"></i></span>
                <span class="text">@lang('Deposit')</span>
            </a>
            <div class="sidebar-submenu">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.index') }}">
                        <a href="{{ route('user.deposit.index') }}"
                            class="sidebar-submenu-list__link">@lang('Deposit Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.history') }}">
                        <a href="{{ route('user.deposit.history') }}"
                            class="sidebar-submenu-list__link">@lang('Deposit History')</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="sidebar-menu-list__item has-dropdown {{ menuActive(['user.withdraw', 'user.withdraw.history']) }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-hand-holding-usd"></i></span>
                <span class="text">@lang('Withdraw')</span>
            </a>
            <div class="sidebar-submenu {{ menuActive(['user.withdraw', 'user.withdraw.history']) }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw') }}">
                        <a href="{{ route('user.withdraw') }}"
                            class="sidebar-submenu-list__link">@lang('Withdraw Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw.history') }}">
                        <a href="{{ route('user.withdraw.history') }}"
                            class="sidebar-submenu-list__link">@lang('Withdraw History')</a>
                    </li>
                </ul>
            </div>
        </li>

        @if ($general->b_transfer)
            <li class="sidebar-menu-list__item {{ menuActive('user.transfer.balance') }}">
                <a href="{{ route('user.transfer.balance') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="fas fa-handshake"></i></span>
                    <span class="text"> @lang('Transfer Balance') </span>
                </a>
            </li>
        @endif
        <li class="sidebar-menu-list__item {{ menuActive('user.transactions') }}">
            <a href="{{ route('user.transactions') }}" class="sidebar-menu-list__link">
                <span class="icon"> <i class="fas fa-exchange-alt"></i> </span>
                <span class="text"> @lang('Transactions') </span>
            </a>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.referrals') }}">
            <a href="{{ route('user.referrals') }}" class="sidebar-menu-list__link">
                <span class="icon"> <i class="fas fa-users"></i> </span>
                <span class="text"> @lang('Referrals') </span>
            </a>
        </li>

        @if ($general->promotional_tool && $promotionCount)
            <li class="sidebar-menu-list__item {{ menuActive('user.promotional.banner') }}">
                <a href="{{ route('user.promotional.banner') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-ad"></i></span>
                    <span class="text"> @lang('Promotional Tool')</span>
                </a>
            </li>
        @endif

        @if ($general->user_ranking)
            <li class="sidebar-menu-list__item {{ menuActive('user.invest.ranking') }}">
                <a href="{{ route('user.invest.ranking') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-crown"></i></span>
                    <span class="text"> @lang('Ranking')</span>
                </a>
            </li>
        @endif
        
        <li class="sidebar-menu-list__item has-dropdown {{ menuActive(['ticket.index', 'ticket.open']) }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-ticket-alt"></i></span>
                <span class="text">@lang('Support Ticket')</span>
            </a>
            <div class="sidebar-submenu {{ menuActive(['ticket.index', 'ticket.open']) }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item active">
                        <a href="{{ route('ticket.open') }}"
                            class="sidebar-submenu-list__link">@lang('Open Ticket')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket.index') }}"
                            class="sidebar-submenu-list__link">@lang('My Tickets')</a>
                    </li>

                </ul>
            </div>
        </li>
        <li class="sidebar-menu-list__item {{ menuActive('user.twofactor') }}">
            <a href="{{ route('user.twofactor') }}" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <span class="text"> @lang('2FA Security') </span>
            </a>
        </li>
        <li class="sidebar-menu-list__item">
            <a href="{{ route('user.logout') }}" class="sidebar-menu-list__link">
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="text">@lang('Logout')</span>
            </a>
        </li>
    </ul>

    <div class="bottom-footer py-3">
        <div class="container">
            <div class="row gy-3">
                <div class="col-md-12 text-center">
                    <div class="bottom-footer-text text-white"> &copy; {{ date('Y') }}
                        <a href="{{ route('home') }}">{{ __($general->site_name) }}</a> @lang('All Rights Reserved').
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========= Sidebar Menu End ================ -->
</div>
