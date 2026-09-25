@extends('emailcampaign.layouts.app_user')
@section('panel')
<div class="crm-card__head" style="margin-bottom:12px">
    <a href="{{ route('ec.user.campaigns.create') }}" class="crm-btn crm-btn--accent">New campaign</a>
</div>
<section class="crm-card">
    @foreach($campaigns as $c)
        @php $pct = $c->total_recipients > 0 ? round(($c->sent_count / $c->total_recipients) * 100) : 0; @endphp
        <div class="crm-list-row">
            <div style="flex:1">
                <strong>{{ $c->name }}</strong> @include('emailcampaign.partials.status_tag', ['status' => $c->status])
                <div class="ec-progress"><span style="width:{{ $pct }}%"></span></div>
                <span>Sent {{ $c->sent_count }} · Remaining {{ max(0, $c->total_recipients - $c->sent_count - $c->failed_count) }} · Total {{ $c->total_recipients }}</span>
            </div>
            <a href="{{ route('ec.user.campaigns.show', $c->id) }}" class="crm-btn crm-btn--ghost crm-btn--sm">Manage</a>
        </div>
    @endforeach
    {{ $campaigns->links() }}
</section>
@endsection
