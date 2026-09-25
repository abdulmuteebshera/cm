@extends('emailcampaign.layouts.app_admin')
@section('panel')
<section class="crm-card">
    @foreach($logs as $log)
        <div class="crm-list-row">
            <div>
                <strong>{{ $log->actor_type }} #{{ $log->actor_id }} · {{ $log->action }}</strong>
                <span>{{ $log->description }} · {{ $log->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    @endforeach
    {{ $logs->links() }}
</section>
@endsection
