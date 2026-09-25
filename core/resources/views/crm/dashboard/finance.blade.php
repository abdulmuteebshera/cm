@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Income</span><strong>${{ number_format($stats['income'], 2) }}</strong></div>
    <div class="crm-stat"><span>Expense</span><strong>${{ number_format($stats['expense'], 2) }}</strong></div>
    <div class="crm-stat"><span>Net P&amp;L</span><strong>${{ number_format($stats['net'], 2) }}</strong></div>
    <div class="crm-stat"><span>Entries</span><strong>{{ $stats['entries'] }}</strong></div>
</div>
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Recent ledger</h2>
        <a href="{{ route('crm.finance.ledger.index') }}" class="crm-btn crm-btn--sm crm-btn--accent">Open ledger</a>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Date</th><th>Type</th><th>Title</th><th>Amount</th></tr></thead>
            <tbody>
                @forelse($entries as $e)
                    <tr>
                        <td>{{ $e->entry_date->format('d M Y') }}</td>
                        <td>{{ $e->type }}</td>
                        <td>{{ $e->title }}</td>
                        <td>${{ number_format($e->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No finance entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
