@extends('emailcampaign.layouts.master')
@section('body-class', 'crm-app')
@section('content')
<div class="crm-shell">
    <aside class="crm-sidebar">
        <div class="crm-sidebar__brand">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="Crownmaire">
            <div>
                <strong>Email Campaign</strong>
                <span>Administrator</span>
            </div>
        </div>
        @include('emailcampaign.partials.admin_nav')
        <div class="crm-sidebar__foot">
            <div class="crm-user">
                <strong>{{ auth('ec_admin')->user()->name }}</strong>
                <span>{{ auth('ec_admin')->user()->username }}</span>
            </div>
            <form action="{{ route('ec.admin.logout') }}" method="post">
                @csrf
                <button type="submit" class="crm-btn crm-btn--ghost crm-btn--sm"><i class="las la-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </aside>
    <div class="crm-main">
        <header class="crm-topbar">
            <div>
                <h1>{{ $pageTitle ?? 'Dashboard' }}</h1>
                <p>Outbound mail, templates, users, and campaign activity</p>
            </div>
        </header>
        <main class="crm-content">
            @yield('panel')
        </main>
    </div>
</div>
@endsection
