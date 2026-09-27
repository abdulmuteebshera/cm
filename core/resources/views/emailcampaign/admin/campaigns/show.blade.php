@extends('emailcampaign.layouts.app_admin')
@section('panel')
@php
    $pct = $campaign->total_recipients > 0 ? round(($campaign->sent_count / $campaign->total_recipients) * 100) : 0;
@endphp
<section class="crm-card">
    @include('emailcampaign.partials.status_tag', ['status' => $campaign->status])
    <p class="ec-hint">Operator: @include('emailcampaign.partials.campaign_operator', ['campaign' => $campaign])</p>
    @if($campaign->ec_admin_id)
        <a href="{{ route('ec.admin.campaigns.manage', $campaign->id) }}" class="crm-btn crm-btn--accent crm-btn--sm">Manage &amp; send</a>
    @endif
    <div class="ec-stat-grid">
        <div class="ec-stat"><strong>{{ $campaign->sent_count }}</strong><span>Sent</span></div>
        <div class="ec-stat"><strong>{{ $remaining }}</strong><span>Remaining</span></div>
        <div class="ec-stat"><strong>{{ $campaign->failed_count }}</strong><span>Failed</span></div>
        <div class="ec-stat"><strong>~{{ gmdate($etaSeconds >= 3600 ? 'H:i:s' : 'i:s', max(0, $etaSeconds)) }}</strong><span>Est. time left (10s gap)</span></div>
    </div>
    <div class="ec-progress"><span style="width:{{ $pct }}%"></span></div>
    <p><strong>Subject:</strong> {{ $campaign->subject }}</p>
</section>
@include('emailcampaign.partials.send_errors', ['campaign' => $campaign, 'failures' => $failures ?? null])
@endsection
