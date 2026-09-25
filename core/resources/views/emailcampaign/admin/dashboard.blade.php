@extends('emailcampaign.layouts.app_admin')
@section('panel')
<div class="ec-stat-grid">
    <div class="ec-stat"><strong>{{ $stats['users'] }}</strong><span>Campaign users</span></div>
    <div class="ec-stat"><strong>{{ $stats['templates'] }}</strong><span>Active templates</span></div>
    <div class="ec-stat"><strong>{{ $stats['campaigns'] }}</strong><span>Total campaigns</span></div>
    <div class="ec-stat"><strong>{{ $stats['running'] }}</strong><span>Running now</span></div>
</div>

<div class="crm-grid crm-grid--2">
    <section class="crm-card">
        <h2 class="crm-card__title">Running campaigns</h2>
        @forelse($runningCampaigns as $c)
            <div class="crm-list-row">
                <div>
                    <strong>{{ $c->name }}</strong>
                    <span>{{ $c->user->username ?? '—' }} · Sent {{ $c->sent_count }}/{{ $c->total_recipients }}</span>
                </div>
                <a href="{{ route('ec.admin.campaigns.show', $c->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">View</a>
            </div>
        @empty
            <p class="ec-hint">No campaigns are sending right now.</p>
        @endforelse
    </section>
    <section class="crm-card">
        <h2 class="crm-card__title">Recent activity</h2>
        @foreach($recentActivity as $log)
            <div class="crm-list-row">
                <div>
                    <strong>{{ $log->action }}</strong>
                    <span>{{ $log->description }} · {{ $log->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @endforeach
        <a href="{{ route('ec.admin.activity') }}" class="crm-btn crm-btn--ghost crm-btn--sm">Full log</a>
    </section>
</div>
@endsection
