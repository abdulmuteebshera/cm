@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Portal investors <span class="crm-pill">Read-only</span></h2>
    <p class="crm-muted">Live data from the investor portal. Operational changes remain in <code>/admin</code>.</p>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Username</th><th>Joined</th></tr></thead>
            <tbody>
                @forelse($investors as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->fullname ?? trim(($u->firstname ?? '').' '.($u->lastname ?? '')) }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->username }}</td>
                        <td>{{ showDateTime($u->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No investors.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($investors,'hasPages') && $investors->hasPages())<div class="crm-pagination">{{ $investors->links() }}</div>@endif
</div>
@endsection
