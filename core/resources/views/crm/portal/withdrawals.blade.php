@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Portal withdrawals <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>TRX</th><th>User</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($withdrawals as $w)
                    <tr>
                        <td>{{ $w->trx }}</td>
                        <td>{{ optional($w->user)->username ?: optional($w->user)->email }}</td>
                        <td>{{ showAmount($w->amount) }}</td>
                        <td>{{ $w->status }}</td>
                        <td>{{ showDateTime($w->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No withdrawals.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())<div class="crm-pagination">{{ $withdrawals->links() }}</div>@endif
</div>
@endsection
