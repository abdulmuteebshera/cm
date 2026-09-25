@extends('crm.layouts.app')
@section('body-class', 'crm-app crm-portal-manager')
@section('panel')
<div class="io-hero io-hero--manager">
    <div>
        <p class="io-hero__eyebrow">Manager Portal</p>
        <h2>{{ $staff->name }}</h2>
        <p>Oversee officers and pipeline. Your Admin tasks (Allocation, Announcements, etc.) are assigned to you personally — only those appear when you enter Admin.</p>
    </div>
    <div class="io-hero__rank">
        <span>Your admin tasks</span>
        <strong>{{ $stats['modules'] }}</strong>
        <small>Assigned to you only</small>
    </div>
</div>

<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Total leads</span><strong>{{ $stats['clients'] }}</strong></div>
    <div class="crm-stat"><span>Active pipeline</span><strong>{{ $stats['active'] }}</strong></div>
    <div class="crm-stat"><span>Investment officers</span><strong>{{ $stats['agents'] }}</strong></div>
    <div class="crm-stat"><span>Funded AUM</span><strong>${{ number_format($stats['funded_aum'], 0) }}</strong></div>
</div>

<div class="crm-card">
    <h2>Company pipeline</h2>
    <div class="io-pipeline">
        @foreach($pipeline as $key => $col)
            <a href="{{ route('crm.clients.index', ['stage' => $key]) }}" class="io-pipeline__col">
                <em>{{ $col['label'] }}</em>
                <strong>{{ $col['count'] }}</strong>
            </a>
        @endforeach
    </div>
</div>

<div class="crm-grid crm-grid--2">
    <div class="crm-card">
        <h3>Top investment officers</h3>
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead><tr><th>Officer</th><th>Leads</th><th>Lifetime AUM</th><th>Rank</th></tr></thead>
                <tbody>
                    @forelse($agents as $a)
                        @php $r = $a->rankingSnapshot(); @endphp
                        <tr>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->clients_count }}</td>
                            <td>${{ number_format($a->lifetime_aum, 0) }}</td>
                            <td><span class="crm-pill">{{ $r['current']['label'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No officers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="crm-card">
        <h3>Recent lead activity</h3>
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead><tr><th>Lead</th><th>Officer</th><th>Stage</th></tr></thead>
                <tbody>
                    @forelse($recent as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ optional($c->owner)->name ?: '—' }}</td>
                            <td><span class="crm-pill">{{ $c->stageLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No activity.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
