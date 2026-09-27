@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Contact form submissions <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Subject</th><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($tickets as $t)
                    <tr>
                        <td>{{ $t->subject }}</td>
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->email }}</td>
                        <td>{{ $t->status }}</td>
                        <td>{{ showDateTime($t->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No contact submissions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())<div class="crm-pagination">{{ $tickets->links() }}</div>@endif
</div>
@endsection
