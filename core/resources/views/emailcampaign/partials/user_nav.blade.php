<nav class="crm-nav">
    <a href="{{ route('ec.user.dashboard') }}" class="{{ request()->routeIs('ec.user.dashboard') ? 'is-active' : '' }}"><i class="las la-tachometer-alt"></i> Dashboard</a>
    <a href="{{ route('ec.user.groups.index') }}" class="{{ request()->routeIs('ec.user.groups.*') ? 'is-active' : '' }}"><i class="las la-object-group"></i> Email groups</a>
    <a href="{{ route('ec.user.campaigns.index') }}" class="{{ request()->routeIs('ec.user.campaigns.*') ? 'is-active' : '' }}"><i class="las la-paper-plane"></i> Campaigns</a>
    <a href="{{ route('ec.user.campaigns.create') }}" class="{{ request()->routeIs('ec.user.campaigns.create') ? 'is-active' : '' }}"><i class="las la-plus-circle"></i> New campaign</a>
</nav>
