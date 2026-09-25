@php
    $s = auth('crm')->user();
    $adminDeskPerms = collect(\App\Support\CrmAdminBridge::permissionMap())
        ->filter(fn ($meta, $slug) => $s->hasPermission($slug))
        ->keys();
@endphp
<nav class="crm-nav">
    <a href="{{ route($s->homeRoute()) }}" class="{{ request()->routeIs($s->homeRoute()) ? 'is-active' : '' }}">
        <i class="las la-th-large"></i> Dashboard
    </a>

    @if($s->hasPermission('crm.staff'))
        <a href="{{ route('crm.staff.index') }}" class="{{ request()->routeIs('crm.staff.*') ? 'is-active' : '' }}">
            <i class="las la-user-tie"></i> Staff
        </a>
    @endif

    @if($s->hasPermission('crm.roles'))
        <a href="{{ route('crm.roles.index') }}" class="{{ request()->routeIs('crm.roles.*') ? 'is-active' : '' }}">
            <i class="las la-key"></i> Roles & Access
        </a>
    @endif

    {{-- Ranking / commissions: Investment Officers only (same for every IO). --}}
    @if($s->portal() === 'agent' && $s->hasPermission('agent.commissions'))
        <a href="{{ route('crm.commissions.index') }}" class="{{ request()->routeIs('crm.commissions.*') ? 'is-active' : '' }}">
            <i class="las la-percentage"></i> Commissions
        </a>
        <a href="{{ route('crm.ranking.index') }}" class="{{ request()->routeIs('crm.ranking.*') ? 'is-active' : '' }}">
            <i class="las la-trophy"></i> Goals & Ranking
        </a>
    @endif

    @if($s->hasPermission('agent.clients') || $s->hasPermission('manager.pipeline'))
        <a href="{{ route('crm.clients.index') }}" class="{{ request()->routeIs('crm.clients.*') ? 'is-active' : '' }}">
            <i class="las la-stream"></i> Lead Pipeline
        </a>
    @endif

    @if($s->hasPermission('agent.referrals'))
        <a href="{{ route('crm.referrals.index') }}" class="{{ request()->routeIs('crm.referrals.*') ? 'is-active' : '' }}">
            <i class="las la-share-alt"></i> Referrals
        </a>
    @endif

    @if($s->hasPermission('agent.pitch_decks'))
        <a href="{{ route('crm.pitch.index') }}" class="{{ request()->routeIs('crm.pitch.*') ? 'is-active' : '' }}">
            <i class="las la-file-powerpoint"></i> Pitch Decks
        </a>
    @endif

    @if($s->hasPermission('trader.positions'))
        <a href="{{ route('crm.trading.index') }}" class="{{ request()->routeIs('crm.trading.*') ? 'is-active' : '' }}">
            <i class="las la-chart-line"></i> Fund Positions
        </a>
    @endif

    @if($s->hasPermission('finance.entries'))
        <a href="{{ route('crm.finance.ledger.index') }}" class="{{ request()->routeIs('crm.finance.ledger.*') ? 'is-active' : '' }}">
            <i class="las la-balance-scale"></i> Finance Ledger
        </a>
    @endif

    @if($s->hasPermission('portal.investors') || $s->hasPermission('portal.deposits') || $s->hasPermission('portal.withdrawals'))
        <div class="crm-nav__label">Portal Oversight</div>
    @endif

    @if($s->hasPermission('portal.investors'))
        <a href="{{ route('crm.portal.investors') }}" class="{{ request()->routeIs('crm.portal.investors') ? 'is-active' : '' }}">
            <i class="las la-user-friends"></i> Investors
        </a>
    @endif
    @if($s->hasPermission('portal.deposits'))
        <a href="{{ route('crm.portal.deposits') }}" class="{{ request()->routeIs('crm.portal.deposits') ? 'is-active' : '' }}">
            <i class="las la-arrow-down"></i> Deposits
        </a>
    @endif
    @if($s->hasPermission('portal.withdrawals'))
        <a href="{{ route('crm.portal.withdrawals') }}" class="{{ request()->routeIs('crm.portal.withdrawals') ? 'is-active' : '' }}">
            <i class="las la-arrow-up"></i> Withdrawals
        </a>
    @endif
    @if($s->hasPermission('portal.tickets'))
        <a href="{{ route('crm.portal.tickets') }}" class="{{ request()->routeIs('crm.portal.tickets') ? 'is-active' : '' }}">
            <i class="las la-ticket-alt"></i> Tickets
        </a>
    @endif
    @if($s->hasPermission('portal.investments'))
        <a href="{{ route('crm.portal.investments') }}" class="{{ request()->routeIs('crm.portal.investments') ? 'is-active' : '' }}">
            <i class="las la-briefcase"></i> Investments
        </a>
    @endif
    @if($s->hasPermission('portal.jobs'))
        <a href="{{ route('crm.portal.jobs') }}" class="{{ request()->routeIs('crm.portal.jobs') ? 'is-active' : '' }}">
            <i class="las la-id-badge"></i> Careers
        </a>
    @endif

    @if($s->isSuper() || $adminDeskPerms->isNotEmpty() || (!$s->isManager() && $s->hasPermission('crm.admin_bridge')))
        <div class="crm-nav__label">Live Admin Desk</div>
        @if($s->isSuper() || $adminDeskPerms->isNotEmpty() || $s->hasPermission('crm.admin_bridge'))
            <form action="{{ route('crm.admin.desk.enter') }}" method="post" class="crm-nav-form">
                @csrf
                <button type="submit" class="crm-nav-enter">
                    <i class="las la-external-link-alt"></i>
                    {{ $s->isSuper() ? 'Enter Admin (no login)' : 'Enter My Admin Tasks' }}
                </button>
            </form>
        @endif
        @foreach($adminDeskPerms as $slug)
            @php $meta = \App\Support\CrmAdminBridge::permissionMap()[$slug] ?? null; @endphp
            @if($meta)
                <span class="crm-nav-hint"><i class="las la-check"></i> {{ $meta['name'] }}</span>
            @endif
        @endforeach
    @endif
</nav>
