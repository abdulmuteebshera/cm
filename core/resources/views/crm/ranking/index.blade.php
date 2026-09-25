@extends('crm.layouts.app')
@section('panel')
<div class="io-hero">
    <div>
        <p class="io-hero__eyebrow">Goals & Achievements</p>
        <h2>Officer ranking ladder</h2>
        <p>Ranks unlock at $500k → $1M → $2.5M → $5M → $10M → $25M → $50M → $100M funded AUM.</p>
    </div>
    @if($mine)
        <div class="io-hero__rank">
            <span>Your rank</span>
            <strong>{{ $mine['current']['label'] }}</strong>
            <small>${{ number_format($mine['aum'], 0) }} AUM</small>
        </div>
    @endif
</div>

@if($mine)
<div class="crm-card">
    <h3>Your progress</h3>
    <div class="io-rank-bar"><div class="io-rank-bar__fill" style="width:{{ $mine['progress'] }}%"></div></div>
    <p class="crm-muted">
        @if($mine['next'])
            Next unlock: <strong>{{ $mine['next']['label'] }}</strong> at ${{ number_format($mine['next']['aum'], 0) }}
        @else
            You have reached Managing Director ($100M).
        @endif
    </p>
</div>
@endif

<div class="crm-card">
    <h2>Achievement tiers</h2>
    <div class="io-tiers">
        @foreach($tiers as $tier)
            @php
                $unlocked = $mine && $mine['aum'] >= $tier['aum'];
            @endphp
            <div class="io-tier {{ $unlocked ? 'is-unlocked' : '' }}">
                <i class="las {{ $unlocked ? 'la-trophy' : 'la-lock' }}"></i>
                <strong>{{ $tier['label'] }}</strong>
                <span>${{ number_format($tier['aum'], 0) }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="crm-card">
    <h2>Leaderboard</h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>#</th><th>Officer</th><th>Lifetime AUM</th><th>Rank</th><th>Progress to next</th></tr></thead>
            <tbody>
                @forelse($officers as $i => $o)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $o->name }}</td>
                        <td>${{ number_format($o->lifetime_aum, 0) }}</td>
                        <td><span class="crm-pill">{{ $o->ranking['current']['label'] }}</span></td>
                        <td>
                            <div class="io-rank-bar io-rank-bar--sm"><div class="io-rank-bar__fill" style="width:{{ $o->ranking['progress'] }}%"></div></div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No officers ranked yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
