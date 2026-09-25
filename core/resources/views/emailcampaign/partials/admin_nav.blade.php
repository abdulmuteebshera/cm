<nav class="crm-nav">
    <a href="{{ route('ec.admin.dashboard') }}" class="{{ request()->routeIs('ec.admin.dashboard') ? 'is-active' : '' }}"><i class="las la-tachometer-alt"></i> Dashboard</a>
    <div class="crm-nav__label">Configuration</div>
    <a href="{{ route('ec.admin.mail.edit') }}" class="{{ request()->routeIs('ec.admin.mail.*') ? 'is-active' : '' }}"><i class="las la-envelope"></i> SMTP / Sender</a>
    <a href="{{ route('ec.admin.templates.index') }}" class="{{ request()->routeIs('ec.admin.templates.*') ? 'is-active' : '' }}"><i class="las la-file-alt"></i> Templates</a>
    <a href="{{ route('ec.admin.users.index') }}" class="{{ request()->routeIs('ec.admin.users.*') ? 'is-active' : '' }}"><i class="las la-users"></i> Users</a>
    <div class="crm-nav__label">Operations</div>
    <a href="{{ route('ec.admin.groups.index') }}" class="{{ request()->routeIs('ec.admin.groups.*') ? 'is-active' : '' }}"><i class="las la-object-group"></i> Email groups</a>
    <a href="{{ route('ec.admin.campaigns.create') }}" class="{{ request()->routeIs('ec.admin.campaigns.create') ? 'is-active' : '' }}"><i class="las la-plus-circle"></i> New campaign</a>
    <a href="{{ route('ec.admin.campaigns.index') }}" class="{{ request()->routeIs('ec.admin.campaigns.index') || request()->routeIs('ec.admin.campaigns.show') ? 'is-active' : '' }}"><i class="las la-paper-plane"></i> All campaigns</a>
    <a href="{{ route('ec.admin.activity') }}" class="{{ request()->routeIs('ec.admin.activity') ? 'is-active' : '' }}"><i class="las la-history"></i> Activity log</a>
</nav>
