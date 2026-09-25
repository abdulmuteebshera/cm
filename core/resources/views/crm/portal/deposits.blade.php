@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <h2>Portal deposits <span class="crm-pill">Read-only</span></h2>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>TRX</th><th>User</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($deposits as $d)
                    <tr>
                        <td>{{ $d->trx }}</td>
                        <td>{{ optional($d->user)->username ?: optional($d->user)->email }}</td>
                        <td>{{ showAmount($d->amount) }}</td>
                        <td>{{ $d->status }}</td>
                        <td>{{ showDateTime($d->created_at, 'd M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No deposits.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deposits->hasPages())<div class="crm-pagination">{{ $deposits->links() }}</div>@endif
</div>
@endsection
