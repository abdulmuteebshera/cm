@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Portal Investors</span><strong>{{ number_format($stats['investors']) }}</strong></div>
    <div class="crm-stat"><span>Approved Deposits</span><strong>{{ number_format($stats['deposits']) }}</strong></div>
    <div class="crm-stat"><span>Pending Tickets</span><strong>{{ number_format($stats['tickets']) }}</strong></div>
    <div class="crm-stat"><span>CRM Staff</span><strong>{{ number_format($stats['staff']) }}</strong></div>
    <div class="crm-stat"><span>CRM Clients</span><strong>{{ number_format($stats['clients']) }}</strong></div>
    <div class="crm-stat"><span>Fund Market Value</span><strong>${{ number_format($stats['fund_aum'], 2) }}</strong></div>
    <div class="crm-stat"><span>Finance Income</span><strong>${{ number_format($stats['income'], 2) }}</strong></div>
    <div class="crm-stat"><span>Finance Expense</span><strong>${{ number_format($stats['expense'], 2) }}</strong></div>
</div>

<div class="crm-card">
    <h2>Command center</h2>
    <p>Assign managers any <strong>Admin Desk Modules</strong> (Portfolio Allocation, Job Applications, Deposits, etc.) under Roles &amp; Access. They then enter the live admin with only those menus — no separate admin password.</p>
    <div class="crm-actions">
        <a class="crm-btn crm-btn--accent" href="{{ route('crm.staff.index') }}"><i class="las la-user-plus"></i> Add Staff</a>
        <a class="crm-btn" href="{{ route('crm.roles.index') }}"><i class="las la-key"></i> Roles & Permissions</a>
        <a class="crm-btn" href="{{ route('crm.pitch.index') }}"><i class="las la-upload"></i> Pitch Decks</a>
        <form action="{{ route('crm.admin.desk.enter') }}" method="post" style="display:inline">
            @csrf
            <button type="submit" class="crm-btn crm-btn--accent"><i class="las la-external-link-alt"></i> Enter Live Admin (no login)</button>
        </form>
    </div>
</div>

<div class="crm-grid crm-grid--2">
    <div class="crm-card">
        <h3>Portal logins</h3>
        <ul class="crm-list">
            <li><code>/internalportal/crm</code> — Super Admin</li>
            <li><code>/internalportal/manager</code> — Managers</li>
            <li><code>/internalportal/agent</code> — Investment Officers</li>
            <li><code>/internalportal/trader</code> — Traders</li>
            <li><code>/internalportal/finance</code> — Finance</li>
        </ul>
    </div>
    <div class="crm-card">
        <h3>Quick modules</h3>
        <div class="crm-actions">
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.clients.index') }}">Clients</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.trading.index') }}">Trading</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.finance.ledger.index') }}">Finance</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.portal.investors') }}">Investors</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.portal.deposits') }}">Deposits</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.portal.withdrawals') }}">Withdrawals</a>
        </div>
    </div>
</div>
@endsection
