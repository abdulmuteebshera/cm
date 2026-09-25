@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Portal investments <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>ID</th><th>User</th><th>Plan</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($investments as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ optional($i->user)->username }}</td>
                        <td>{{ optional($i->plan)->name }}</td>
                        <td>{{ showAmount($i->amount) }}</td>
                        <td>{{ $i->status }}</td>
                        <td>{{ showDateTime($i->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No investments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(is_object($investments) && method_exists($investments,'hasPages') && $investments->hasPages())
        <div class="crm-pagination">{{ $investments->links() }}</div>
    @endif
</div>
@endsection
