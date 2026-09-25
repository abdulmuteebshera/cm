@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Positions</span><strong>{{ $stats['positions'] }}</strong></div>
    <div class="crm-stat"><span>Allocated</span><strong>${{ number_format($stats['allocated'], 2) }}</strong></div>
    <div class="crm-stat"><span>Market Value</span><strong>${{ number_format($stats['market'], 2) }}</strong></div>
    <div class="crm-stat"><span>P&amp;L</span><strong>${{ number_format($stats['pnl'], 2) }}</strong></div>
</div>
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Open positions</h2>
        <a href="{{ route('crm.trading.index') }}" class="crm-btn crm-btn--sm crm-btn--accent">Manage funds</a>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Name</th><th>Class</th><th>Allocated</th><th>Market</th><th>P&amp;L</th></tr></thead>
            <tbody>
                @forelse($positions as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->asset_class ?: '—' }}</td>
                        <td>${{ number_format($p->allocated_amount, 2) }}</td>
                        <td>${{ number_format($p->market_value, 2) }}</td>
                        <td>${{ number_format($p->pnl, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No positions recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
