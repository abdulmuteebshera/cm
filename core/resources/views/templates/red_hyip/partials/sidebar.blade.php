@php
    $promotionCount = App\Models\PromotionTool::count();
@endphp

<div class="dashboard__left">
    <div class="sidebar-menu">
        <span class="sidebar-menu__close d-lg-none d-block"><i class="las la-times"></i></span>
        <div class="sidebar-logo">
            <a href="{{ route('home') }}" class="sidebar-logo__link"> <img
                    src="{{ getImage('assets/images/logoIcon/logo_2.png') }}" alt=" @lang('Logo')"></a>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-user__balance">
                <span class="sidebar-user__balance-icon"><i class="las la-wallet"></i></span>
                <span class="sidebar-user__balance-text">@lang('Account Blance')</span>
            </div>
            <h5 class="sidebar-user__amount"> {{ $general->cur_sym }}{{ showAmount(auth()->user()->deposit_wallet) }} <span
                    class="sidebar-user__wallet"> (@lang('Deposit Wallet'))</span> </h5>
            <h5 class="sidebar-user__amount"> {{ $general->cur_sym }}{{ showAmount(auth()->user()->interest_wallet) }} <span
                    class="sidebar-user__wallet">(@lang('Interest Wallet'))</span> </h5>
        </div>

        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.home') }}" class="sidebar-menu-list__link {{ menuActive('user.home') }}">
                    <span class="icon"><i class="fas fa-home"></i></span>
                    <span class="text">@lang('Dashboard')</span>
                </a>

            </li>
            <li class="sidebar-menu-list__item ">
                <a href="{{ route('user.invest.statistics') }}"
                    class="sidebar-menu-list__link {{ menuActive('user.invest.statistics') }}">
                    <span class="icon"><i class="las la-coins"></i></span>
                    <span class="text">@lang('Investments')</span>
                </a>
            </li>

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.deposit*', 3) }}">
                <a href="javascript:void(0)" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-layer-group"></i></span>
                    <span class="text">@lang('Manage Deposits')</span>
                </a>
                <div class="sidebar-submenu  {{ menuActive('user.deposit*', 2) }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.index') }}">
                            <a href="{{ route('user.deposit.index') }} "
                                class="sidebar-submenu-list__link">@lang('Add Deposit') </a>
                        </li>
                        <li class="sidebar-submenu-list__item {{ menuActive('user.deposit.history') }}">
                            <a href="{{ route('user.deposit.history') }}"
                                class="sidebar-submenu-list__link">@lang('Deposit History')</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.withdraw*', 3) }}">
                <a href="javascript:void(0)" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-hand-holding-usd"></i></span>
                    <span class="text">@lang('Manage Withdraw')</span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.withdraw*', 2) }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw') }}">
                            <a href="{{ route('user.withdraw') }} "
                                class="sidebar-submenu-list__link">@lang('Withdraw') </a>
                        </li>
                        <li class="sidebar-submenu-list__item {{ menuActive('user.withdraw.history') }}">
                            <a href="{{ route('user.withdraw.history') }}"
                                class="sidebar-submenu-list__link">@lang('Withdraw History')</a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.transfer.balance') }}"
                    class="sidebar-menu-list__link {{ menuActive('user.transfer.balance') }}">
                    <span class="icon"><i class="las la-dollar-sign"></i></span>
                    <span class="text">@lang('Transfer Balanace')</span>
                </a>
            </li>
            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.transactions') }}"
                    class="sidebar-menu-list__link {{ menuActive('user.transactions') }}">
                    <span class="icon"><i class="las la-cubes"></i></span>
                    <span class="text">@lang('Transaction')</span>
                </a>
            </li>

            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.referrals') }}"
                    class="sidebar-menu-list__link {{ menuActive('user.referrals') }}">
                    <span class="icon"> <i class="las la-handshake"></i></span>
                    <span class="text">@lang('Referrals')</span>
                </a>
            </li>

            @if ($general->promotional_tool && @$promotionCount)
                <li class="sidebar-menu-list__item">
                    <a href="{{ route('user.promotional.banner') }}"
                        class="sidebar-menu-list__link {{ menuActive('user.promotional.banner') }}">
                        <span class="icon"> <i class="las la-ad"></i></span>
                        <span class="text">@lang('Promotional Banner')</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('ticket.*', 3) }}">
                <a href="javascript:void(0)" class="sidebar-menu-list__link">
                    <span class="icon"><i class="las la-ticket-alt"></i></span>
                    <span class="text">@lang('Tickets')</span>
                </a>
                <div class="sidebar-submenu {{ menuActive('ticket.*', 2) }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item {{ menuActive('ticket.open') }}">
                            <a href="{{ route('ticket.open') }}"
                                class="sidebar-submenu-list__link">@lang('Ticket Open')</a>
                        </li>
                        <li class="sidebar-submenu-list__item {{ menuActive('ticket.index') }}">
                            <a href="{{ route('ticket.index') }} "
                                class="sidebar-submenu-list__link">@lang('All Support Tickets') </a>
                        </li>

                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item ">
                <a href="{{ route('user.twofactor') }}"
                    class="sidebar-menu-list__link {{ menuActive('user.twofactor') }}">
                    <span class="icon"><i class="las la-user-lock"></i></span>
                    <span class="text">@lang('2FA Security')</span>
                </a>
            </li>

            <li class="sidebar-menu-list__item">
                <a href="{{ route('user.logout') }}" class="sidebar-menu-list__link">
                    <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span class="text">@lang('Logout')</span>
                </a>
            </li>
        </ul>
    </div>
</div>
