@extends('emailcampaign.layouts.app_admin')
@section('panel')
<div class="crm-card__head" style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap">
    <a href="{{ route('ec.admin.campaigns.create') }}" class="crm-btn crm-btn--accent">New campaign</a>
    <form method="post" action="{{ route('ec.admin.campaigns.process_queue') }}">@csrf<button type="submit" class="crm-btn crm-btn--ghost">Run send worker once</button></form>
</div>
<p class="ec-hint">Campaigns only send when SMTP is configured and cron runs <code>schedule:run</code> every minute—or click “Run send worker once” to process the next pending email(s).</p>
<section class="crm-card">
    @foreach($campaigns as $c)
        @php
            $pct = $c->total_recipients > 0 ? round(($c->sent_count / $c->total_recipients) * 100) : 0;
        @endphp
        <div class="crm-list-row">
            <div style="flex:1">
                <strong>{{ $c->name }}</strong>
                @include('emailcampaign.partials.status_tag', ['status' => $c->status])
                <span>@if($c->ec_admin_id) Admin @elseif($c->user) {{ $c->user->username }} @else — @endif · Template: {{ $c->template->name ?? '—' }}</span>
                <div class="ec-progress"><span style="width:{{ $pct }}%"></span></div>
                <span>Sent {{ $c->sent_count }} · Failed {{ $c->failed_count }} · Total {{ $c->total_recipients }}</span>
                @if($c->failed_count > 0 && ($err = $c->latestSendFailure()))
                    <span style="display:block;color:#fca5a5;font-size:.8rem;margin-top:4px">Last error: {{ Str::limit($err->error_message, 120) }}</span>
                @endif
            </div>
            @if($c->ec_admin_id)
                <a href="{{ route('ec.admin.campaigns.manage', $c->id) }}" class="crm-btn crm-btn--accent crm-btn--sm">Manage</a>
            @endif
            <a href="{{ route('ec.admin.campaigns.show', $c->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">Details</a>
        </div>
    @endforeach
    {{ $campaigns->links() }}
</section>
@endsection
