@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Portal tickets <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Ticket</th><th>Subject</th><th>User</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>{{ $t->ticket }}</td>
                        <td>{{ $t->subject }}</td>
                        <td>{{ $t->name ?: optional($t->user)->username }}</td>
                        <td>{{ $t->status }}</td>
                        <td>{{ showDateTime($t->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No tickets.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())<div class="crm-pagination">{{ $tickets->links() }}</div>@endif
</div>
@endsection
