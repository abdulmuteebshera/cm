@extends('emailcampaign.layouts.app_user')
@section('panel')
@php
    $pct = $campaign->total_recipients > 0 ? round(($campaign->sent_count / $campaign->total_recipients) * 100) : 0;
@endphp
<section class="crm-card" id="ec-campaign-live" data-status-url="{{ route('ec.user.campaigns.status', $campaign->id) }}">
    <div class="crm-card__head">
        <div>
            @include('emailcampaign.partials.status_tag', ['status' => $campaign->status])
            <span id="ec-live-status">{{ ucfirst($campaign->status) }}</span>
        </div>
        <div class="crm-btn-row">
            @if(in_array($campaign->status, ['draft', 'paused']))
                <form method="post" action="{{ route('ec.user.campaigns.start', $campaign->id) }}">@csrf<button class="crm-btn crm-btn--accent crm-btn--sm">Start</button></form>
            @endif
            @if($campaign->status === 'running')
                <form method="post" action="{{ route('ec.user.campaigns.pause', $campaign->id) }}">@csrf<button class="crm-btn crm-btn--ghost crm-btn--sm">Pause</button></form>
            @endif
            @if($campaign->status === 'paused')
                <form method="post" action="{{ route('ec.user.campaigns.resume', $campaign->id) }}">@csrf<button class="crm-btn crm-btn--accent crm-btn--sm">Resume</button></form>
            @endif
        </div>
    </div>
    <div class="ec-stat-grid">
        <div class="ec-stat"><strong id="ec-sent">{{ $campaign->sent_count }}</strong><span>Sent</span></div>
        <div class="ec-stat"><strong id="ec-remaining">{{ $remaining }}</strong><span>Remaining</span></div>
        <div class="ec-stat"><strong id="ec-failed">{{ $campaign->failed_count }}</strong><span>Failed</span></div>
        <div class="ec-stat"><strong id="ec-eta">—</strong><span>Est. completion (10s between emails)</span></div>
    </div>
    <div class="ec-progress"><span id="ec-progress-bar" style="width:{{ $pct }}%"></span></div>
    <p class="ec-hint">Delivery uses the admin SMTP account. Pause anytime; resume continues with the next pending recipient.</p>
</section>

<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Campaign content</h2>
    @if($campaign->status === 'running')
        <p class="ec-hint">Pause the campaign to edit content.</p>
    @else
        <form method="post" action="{{ route('ec.user.campaigns.load_template', $campaign->id) }}" class="crm-form crm-form__grid" style="margin-bottom:12px">
            @csrf
            <label><span>Reload from template</span>
                <select name="ec_template_id">
                    @foreach(\App\Models\EmailCampaign\EcTemplate::where('status',1)->orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}" @selected($t->id == $campaign->ec_template_id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="crm-btn crm-btn--ghost">Load template</button>
        </form>
        <form method="post" action="{{ route('ec.user.campaigns.content', $campaign->id) }}" class="crm-form crm-form--stack">
            @csrf
            <label><span>Name</span><input type="text" name="name" value="{{ $campaign->name }}" required></label>
            <label><span>Subject</span><input type="text" name="subject" value="{{ $campaign->subject }}" required></label>
            <label><span>HTML body</span><textarea name="body_html" rows="8" required>{{ $campaign->body_html }}</textarea></label>
            <label><span>Plain text</span><textarea name="body_text" rows="5">{{ $campaign->body_text }}</textarea></label>
            <button type="submit" class="crm-btn crm-btn--accent">Save content</button>
        </form>
    @endif
</section>

<section class="crm-card" style="margin-top:16px">
    <h2 class="crm-card__title">Recipients</h2>
    @if($campaign->status !== 'running')
        <form method="post" action="{{ route('ec.user.campaigns.recipients.add', $campaign->id) }}" class="crm-form crm-form__grid">
            @csrf
            <label><span>Email</span><input type="email" name="email" required></label>
            <label><span>Name</span><input type="text" name="name"></label>
            <button type="submit" class="crm-btn crm-btn--accent">Add</button>
        </form>
        <form method="post" action="{{ route('ec.user.campaigns.recipients.import', $campaign->id) }}" enctype="multipart/form-data" class="crm-form crm-form--stack" style="margin-top:12px">
            @csrf
            <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required>
            <button type="submit" class="crm-btn crm-btn--ghost">Import CSV / Excel</button>
        </form>
        @if($campaign->ec_group_id)
            <form method="post" action="{{ route('ec.user.campaigns.sync_group', $campaign->id) }}" style="margin-top:8px">@csrf<button class="crm-btn crm-btn--ghost crm-btn--sm">Sync from linked group</button></form>
        @endif
    @endif
    @foreach($recipients as $r)
        <div class="crm-list-row">
            <div><strong>{{ $r->email }}</strong><span>{{ $r->name }} · {{ ucfirst($r->status) }}</span></div>
        </div>
    @endforeach
    {{ $recipients->links() }}
</section>
@push('script')
<script>
(function () {
    const root = document.getElementById('ec-campaign-live');
    if (!root) return;
    const url = root.dataset.statusUrl;
    const refresh = () => {
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                document.getElementById('ec-sent').textContent = d.sent;
                document.getElementById('ec-remaining').textContent = d.remaining;
                document.getElementById('ec-failed').textContent = d.failed;
                document.getElementById('ec-eta').textContent = d.eta_human || '—';
                const pct = d.total > 0 ? Math.round((d.sent / d.total) * 100) : 0;
                document.getElementById('ec-progress-bar').style.width = pct + '%';
            }).catch(() => {});
    };
    refresh();
    setInterval(refresh, 8000);
})();
</script>
@endpush
@endsection
