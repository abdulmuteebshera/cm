@extends('emailcampaign.layouts.app_user')
@section('panel')
<div class="ec-stat-grid">
    <div class="ec-stat"><strong>{{ $stats['total'] }}</strong><span>Campaigns</span></div>
    <div class="ec-stat"><strong>{{ $stats['running'] }}</strong><span>Running</span></div>
    <div class="ec-stat"><strong>{{ $stats['paused'] }}</strong><span>Paused</span></div>
    <div class="ec-stat"><strong>{{ $stats['completed'] }}</strong><span>Completed</span></div>
</div>
<section class="crm-card">
    <div class="crm-card__head">
        <h2 class="crm-card__title">Recent campaigns</h2>
        <a href="{{ route('ec.user.campaigns.create') }}" class="crm-btn crm-btn--accent crm-btn--sm">New campaign</a>
    </div>
    @forelse($campaigns as $c)
        <div class="crm-list-row">
            <div>
                <strong>{{ $c->name }}</strong> @include('emailcampaign.partials.status_tag', ['status' => $c->status])
                <span>Sent {{ $c->sent_count }}/{{ $c->total_recipients }}</span>
            </div>
            <a href="{{ route('ec.user.campaigns.show', $c->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">Open</a>
        </div>
    @empty
        <p class="ec-hint">Create a campaign, add recipients (manual, CSV, or Excel), then start sending.</p>
    @endforelse
</section>
@endsection
