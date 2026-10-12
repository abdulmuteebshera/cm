@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Investment inquiries <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Submitted</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Reason</th>
                    <th>Capital</th>
                    <th>Message</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                    @php $lead = $t->leadFields(); @endphp
                    <tr>
                        <td>{{ showDateTime($t->created_at, 'd M Y') }}</td>
                        <td>{{ $t->name }}</td>
                        <td>{{ $lead['company'] }}</td>
                        <td>{{ $t->email }}</td>
                        <td>{{ $lead['phone'] }}</td>
                        <td>{{ $t->subject }}</td>
                        <td>{{ $lead['capital'] }}</td>
                        <td>{{ $lead['message'] !== '' ? strLimit($lead['message'], 80) : '—' }}</td>
                        <td>{{ [0 => 'New', 1 => 'Contacted', 2 => 'Follow-up', 3 => 'Closed'][(int) $t->status] ?? 'New' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9">No investment inquiries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())<div class="crm-pagination">{{ $tickets->links() }}</div>@endif
</div>
@endsection
