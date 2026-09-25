@extends('emailcampaign.layouts.master')
@section('body-class', 'crm-app')
@section('content')
<div class="crm-shell">
    <aside class="crm-sidebar">
        <div class="crm-sidebar__brand">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="Crownmaire">
            <div>
                <strong>Email Campaign</strong>
                <span>{{ auth('ec_user')->user()->name }}</span>
            </div>
        </div>
        @include('emailcampaign.partials.user_nav')
        <div class="crm-sidebar__foot">
            <div class="crm-user">
                <strong>{{ auth('ec_user')->user()->username }}</strong>
                <span>Campaign operator</span>
            </div>
            <form action="{{ route('ec.user.logout') }}" method="post">
                @csrf
                <button type="submit" class="crm-btn crm-btn--ghost crm-btn--sm"><i class="las la-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </aside>
    <div class="crm-main">
        <header class="crm-topbar">
            <div>
                <h1>{{ $pageTitle ?? 'Dashboard' }}</h1>
                <p>Groups, campaigns, and delivery progress</p>
            </div>
        </header>
        <main class="crm-content">
            @yield('panel')
        </main>
    </div>
</div>
@endsection
