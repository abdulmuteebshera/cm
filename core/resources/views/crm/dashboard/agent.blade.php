@extends('crm.layouts.app')
@section('body-class', 'crm-app crm-portal-agent')
@section('panel')
<div class="io-hero">
    <div>
        <p class="io-hero__eyebrow">Investment Officer</p>
        <h2>Welcome back, {{ $staff->name }}</h2>
        <p>Lead pipeline · 2% upfront · 1% annual retention · ranking goals</p>
    </div>
    <div class="io-hero__rank">
        <span>Current rank</span>
        <strong>{{ $ranking['current']['label'] }}</strong>
        <small>${{ number_format($ranking['aum'], 0) }} lifetime AUM</small>
    </div>
</div>

<div class="crm-grid crm-grid--stats io-stats">
    <div class="crm-stat"><span>My leads</span><strong>{{ $stats['leads'] }}</strong></div>
    <div class="crm-stat"><span>Funded clients</span><strong>{{ $stats['funded'] }}</strong></div>
    <div class="crm-stat"><span>Funded AUM</span><strong>${{ number_format($stats['funded_aum'], 0) }}</strong></div>
    <div class="crm-stat"><span>Upfront 2%</span><strong>${{ number_format($stats['upfront'], 2) }}</strong></div>
    <div class="crm-stat"><span>Retention 1%</span><strong>${{ number_format($stats['retention'], 2) }}</strong></div>
    <div class="crm-stat"><span>Pending payout</span><strong>${{ number_format($stats['pending_pay'], 2) }}</strong></div>
</div>

<div class="crm-card">
    <div class="crm-card__head">
        <h2>Lead pipeline</h2>
        <a href="{{ route('crm.clients.index') }}" class="crm-btn crm-btn--sm crm-btn--accent">Open pipeline</a>
    </div>
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
        <div class="crm-card__head">
            <h3>Rank progress</h3>
            <a href="{{ route('crm.ranking.index') }}" class="crm-btn crm-btn--sm">Goals</a>
        </div>
        <div class="io-rank-bar">
            <div class="io-rank-bar__fill" style="width: {{ $ranking['progress'] }}%"></div>
        </div>
        <p class="crm-muted">
            {{ $ranking['current']['label'] }}
            @if($ranking['next'])
                → next: {{ $ranking['next']['label'] }} at ${{ number_format($ranking['next']['aum'], 0) }}
            @else
                — top tier achieved
            @endif
        </p>
        <ul class="io-tier-list">
            @foreach(\App\Support\CrmOfficerProgram::rankingTiers() as $tier)
                <li class="{{ $ranking['aum'] >= $tier['aum'] ? 'is-done' : '' }}">
                    <i class="las {{ $ranking['aum'] >= $tier['aum'] ? 'la-trophy' : 'la-lock' }}"></i>
                    {{ $tier['label'] }} — ${{ number_format($tier['aum'], 0) }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="crm-card">
        <div class="crm-card__head">
            <h3>Follow-ups (7 days)</h3>
            <a href="{{ route('crm.commissions.index') }}" class="crm-btn crm-btn--sm">Commissions</a>
        </div>
        <div class="crm-table-wrap">
            <table class="crm-table">
                <thead><tr><th>Lead</th><th>Stage</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($followUps as $f)
                        <tr>
                            <td>{{ $f->name }}</td>
                            <td><span class="crm-pill">{{ $f->stageLabel() }}</span></td>
                            <td>{{ optional($f->next_follow_up)->format('d M') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No upcoming follow-ups.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="crm-card">
    <div class="crm-card__head">
        <h2>Recent leads</h2>
        <div class="crm-actions" style="margin:0">
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.clients.index') }}">All leads</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.referrals.index') }}">Referrals</a>
            <a class="crm-btn crm-btn--sm" href="{{ route('crm.pitch.index') }}">Pitch decks</a>
        </div>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Name</th><th>Stage</th><th>Expected</th><th>Committed</th><th>Updated</th></tr></thead>
            <tbody>
                @forelse($clients as $c)
                    <tr>
                        <td>{{ $c->name }}</td>
                        <td><span class="crm-pill">{{ $c->stageLabel() }}</span></td>
                        <td>${{ number_format($c->expected_aum, 0) }}</td>
                        <td>${{ number_format($c->committed_aum, 0) }}</td>
                        <td>{{ optional($c->updated_at)->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No leads yet — start onboarding from Lead Pipeline.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
