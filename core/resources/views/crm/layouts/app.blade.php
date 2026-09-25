@extends('crm.layouts.master')
@section('body-class', 'crm-app')
@section('content')
@php
    $crmStaff = auth('crm')->user();
    $portal = $crmStaff->portal();
@endphp
<div class="crm-shell">
    <aside class="crm-sidebar">
        <div class="crm-sidebar__brand">
            <img src="{{ asset(getImage(getFilePath('logoIcon') . '/logo_2.png')) }}" alt="Crownmaire">
            <div>
                <strong>Crownmaire CRM</strong>
                <span>
                    @if($portal === 'agent') Investment Officer
                    @elseif($portal === 'manager') Manager Desk
                    @else {{ ucfirst($portal) }} Portal
                    @endif
                </span>
            </div>
        </div>
        @include('crm.partials.sidenav')
        <div class="crm-sidebar__foot">
            <div class="crm-user">
                <strong>{{ $crmStaff->name }}</strong>
                <span>{{ $crmStaff->email }}</span>
            </div>
            <form action="{{ route('crm.logout') }}" method="post">
                @csrf
                <button type="submit" class="crm-btn crm-btn--ghost crm-btn--sm"><i class="las la-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </aside>
    <div class="crm-main">
        <header class="crm-topbar">
            <div>
                <h1>{{ $pageTitle ?? 'Dashboard' }}</h1>
                <p>
                    @if($portal === 'agent')
                        Pipeline · commissions · ranking · pitch materials
                    @elseif($portal === 'manager')
                        Team oversight · assigned admin modules · pipeline
                    @else
                        Crownmaire Capital — institutional operations desk
                    @endif
                </p>
            </div>
            <div class="crm-topbar__meta">
                <span class="crm-pill">{{ optional($crmStaff->role)->name ?? 'Super Admin' }}</span>
            </div>
        </header>
        <main class="crm-content">
            @yield('panel')
        </main>
    </div>
</div>
@endsection
