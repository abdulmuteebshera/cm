@if(($campaign->failed_count ?? 0) > 0)
<section class="crm-card ec-send-errors" style="margin-top:16px;border-color:rgba(248,113,113,.35)">
    <h2 class="crm-card__title" style="color:#fca5a5">Send errors ({{ $campaign->failed_count }})</h2>
    <p class="ec-hint">Fix SMTP under mail settings, then pause the campaign, correct issues, and resume. Failed rows stay failed until you remove and re-add those addresses if needed.</p>
    @php
        $failures = $failures ?? $campaign->recipients()->where('status', 'failed')->orderByDesc('id')->limit(15)->get();
        $latest = $campaign->latestSendFailure();
    @endphp
    @if($latest && $latest->error_message)
        <div class="ec-send-errors__latest">
            <strong>Latest error</strong>
            <p>{{ $latest->error_message }}</p>
            <span>{{ $latest->email }} · {{ optional($latest->updated_at)->format('Y-m-d H:i') }}</span>
        </div>
    @endif
    @forelse($failures as $f)
        <div class="crm-list-row ec-send-errors__row">
            <div>
                <strong>{{ $f->email }}</strong>
                <span>{{ $f->error_message ?: 'Unknown error' }}</span>
            </div>
        </div>
    @empty
        <p class="ec-hint">No failure details stored yet.</p>
    @endforelse
</section>
@endif
