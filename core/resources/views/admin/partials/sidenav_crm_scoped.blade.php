@php
    $bridge = session('crm_admin_bridge');
@endphp
<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo"><img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
        </div>

        @if($bridge)
            <div class="p-3">
                <div class="alert alert-warning mb-0 py-2 px-3" style="font-size:12px;">
                    <strong>CRM Desk</strong><br>
                    {{ $bridge['staff_name'] ?? 'Staff' }}
                    @if(empty($bridge['full_access']))
                        <br>Scoped modules only
                    @endif
                    <div class="mt-2">
                        <a href="{{ route(auth('crm')->check() ? auth('crm')->user()->homeRoute() : 'crm.login') }}" class="btn btn-sm btn--primary">Back to CRM</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                @if(crmAdminMenuAllows('plans'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive(['admin.time.index*', 'admin.plan.index*', 'admin.strategy.*'], 3) }}">
                        <i class="menu-icon las la-clipboard-check"></i>
                        <span class="menu-title">@lang('Plan Manage')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.time.index*', 'admin.plan.index*', 'admin.strategy.*'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.time.index') }}">
                                <a href="{{ route('admin.time.index') }}" class="nav-link"><i class="menu-icon las la-dot-circle"></i><span class="menu-title">@lang('Time Manage')</span></a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.plan.index') }}">
                                <a href="{{ route('admin.plan.index') }}" class="nav-link"><i class="menu-icon las la-dot-circle"></i><span class="menu-title">@lang('Plan Manage')</span></a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.strategy.payouts') }}">
                                <a href="{{ route('admin.strategy.payouts') }}" class="nav-link"><i class="menu-icon las la-dot-circle"></i><span class="menu-title">@lang('Strategy Payout Approvals')</span></a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.strategy.reports.index') }}">
                    <a href="{{ route('admin.strategy.reports.index') }}" class="nav-link "><i class="menu-icon las la-file-pdf"></i><span class="menu-title">@lang('Strategy Reports')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('referrals'))
                <li class="sidebar-menu-item {{ menuActive('admin.referrals.index') }}">
                    <a href="{{ route('admin.referrals.index') }}" class="nav-link "><i class="menu-icon las la-user-friends"></i><span class="menu-title">@lang('Manage Referral')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('reports'))
                <li class="sidebar-menu-item {{ menuActive('admin.invest.report.dashboard') }}">
                    <a href="{{ route('admin.invest.report.dashboard') }}" class="nav-link "><i class="menu-icon las la-chart-bar"></i><span class="menu-title">@lang('Investment Report')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('ranking'))
                <li class="sidebar-menu-item {{ menuActive('admin.ranking.list') }}">
                    <a href="{{ route('admin.ranking.list') }}" class="nav-link "><i class="menu-icon las la-medal"></i><span class="menu-title">@lang('User Ranking')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('users'))
                <li class="sidebar-menu-item {{ menuActive('admin.users.all') }}">
                    <a href="{{ route('admin.users.all') }}" class="nav-link "><i class="menu-icon las la-users"></i><span class="menu-title">@lang('Manage Users')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('announcements'))
                <li class="sidebar-menu-item {{ menuActive('admin.announcement.index') }}">
                    <a href="{{ route('admin.announcement.index') }}" class="nav-link "><i class="menu-icon las la-bullhorn"></i><span class="menu-title">@lang('Announcements')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('job_posts'))
                <li class="sidebar-menu-item {{ menuActive('admin.job.post.index') }}">
                    <a href="{{ route('admin.job.post.index') }}" class="nav-link "><i class="menu-icon las la-briefcase"></i><span class="menu-title">@lang('Job Opportunities')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('job_applications'))
                <li class="sidebar-menu-item {{ menuActive('admin.job.application.index') }}">
                    <a href="{{ route('admin.job.application.index') }}" class="nav-link "><i class="menu-icon las la-user-tie"></i><span class="menu-title">@lang('Job Applications')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('leaderboard'))
                <li class="sidebar-menu-item {{ menuActive('admin.leaderboard.index') }}">
                    <a href="{{ route('admin.leaderboard.index') }}" class="nav-link "><i class="menu-icon las la-trophy"></i><span class="menu-title">@lang('Leaderboard')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('portfolio_allocation'))
                <li class="sidebar-menu-item {{ menuActive('admin.portfolio.allocation.index') }}">
                    <a href="{{ route('admin.portfolio.allocation.index') }}" class="nav-link "><i class="menu-icon las la-chart-pie"></i><span class="menu-title">@lang('Portfolio Allocation')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('promotions'))
                <li class="sidebar-menu-item {{ menuActive('admin.promotional.tool.index') }}">
                    <a href="{{ route('admin.promotional.tool.index') }}" class="nav-link "><i class="menu-icon las la-ad"></i><span class="menu-title">@lang('Promotional Tool')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('gateways'))
                <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.index') }}">
                    <a href="{{ route('admin.gateway.automatic.index') }}" class="nav-link "><i class="menu-icon las la-credit-card"></i><span class="menu-title">@lang('Payment Gateways')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('deposits'))
                <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }}">
                    <a href="{{ route('admin.deposit.pending') }}" class="nav-link "><i class="menu-icon las la-file-invoice-dollar"></i><span class="menu-title">@lang('Deposits')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('withdrawals'))
                <li class="sidebar-menu-item {{ menuActive('admin.withdraw.pending') }}">
                    <a href="{{ route('admin.withdraw.pending') }}" class="nav-link "><i class="menu-icon las la-hand-holding-usd"></i><span class="menu-title">@lang('Withdrawals')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('tickets'))
                <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }}">
                    <a href="{{ route('admin.ticket.pending') }}" class="nav-link "><i class="menu-icon las la-ticket-alt"></i><span class="menu-title">@lang('Support Ticket')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('reports'))
                <li class="sidebar-menu-item {{ menuActive('admin.report.transaction') }}">
                    <a href="{{ route('admin.report.transaction') }}" class="nav-link "><i class="menu-icon las la-list-alt"></i><span class="menu-title">@lang('Reports')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('subscribers'))
                <li class="sidebar-menu-item {{ menuActive('admin.subscriber.index') }}">
                    <a href="{{ route('admin.subscriber.index') }}" class="nav-link "><i class="menu-icon las la-envelope-open-text"></i><span class="menu-title">@lang('Subscribers')</span></a>
                </li>
                @endif

                @if(crmAdminMenuAllows('settings'))
                <li class="sidebar-menu-item {{ menuActive('admin.setting.index') }}">
                    <a href="{{ route('admin.setting.index') }}" class="nav-link "><i class="menu-icon las la-cog"></i><span class="menu-title">@lang('General Setting')</span></a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
