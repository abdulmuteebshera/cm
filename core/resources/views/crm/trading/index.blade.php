@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Allocated</span><strong>${{ number_format($summary['allocated'], 2) }}</strong></div>
    <div class="crm-stat"><span>Market value</span><strong>${{ number_format($summary['market'], 2) }}</strong></div>
    <div class="crm-stat"><span>P&amp;L</span><strong>${{ number_format($summary['pnl'], 2) }}</strong></div>
</div>
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Fund positions</h2>
        <button class="crm-btn crm-btn--accent" onclick="document.getElementById('addPos').showModal()">Add position</button>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Name</th><th>Class / Venue</th><th>Allocated</th><th>Market</th><th>P&amp;L</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($positions as $p)
                    <tr>
                        <td>{{ $p->name }}<br><small>{{ $p->symbol }}</small></td>
                        <td>{{ $p->asset_class }} / {{ $p->venue }}</td>
                        <td>${{ number_format($p->allocated_amount, 2) }}</td>
                        <td>${{ number_format($p->market_value, 2) }}</td>
                        <td>${{ number_format($p->pnl, 2) }} ({{ $p->pnl_percent }}%)</td>
                        <td>{{ $p->status }}</td>
                        <td>
                            <button class="crm-btn crm-btn--sm edit-pos"
                                data-route="{{ route('crm.trading.update', $p->id) }}"
                                data-name="{{ $p->name }}" data-asset_class="{{ $p->asset_class }}" data-venue="{{ $p->venue }}"
                                data-symbol="{{ $p->symbol }}" data-currency="{{ $p->currency }}"
                                data-allocated_amount="{{ $p->allocated_amount }}" data-market_value="{{ $p->market_value }}"
                                data-pnl="{{ $p->pnl }}" data-pnl_percent="{{ $p->pnl_percent }}" data-status="{{ $p->status }}"
                                data-opened_on="{{ optional($p->opened_on)->format('Y-m-d') }}" data-notes="{{ $p->notes }}">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No positions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($positions->hasPages())<div class="crm-pagination">{{ $positions->links() }}</div>@endif
</div>
<dialog id="addPos" class="crm-dialog">
    <form method="post" action="{{ route('crm.trading.store') }}">@csrf
        <h3>New position</h3>
        @include('crm.trading.form_fields')
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Save</button>
        </div>
    </form>
</dialog>
<dialog id="editPos" class="crm-dialog">
    <form method="post" action="">@csrf
        <h3>Edit position</h3>
        @include('crm.trading.form_fields')
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Update</button>
        </div>
    </form>
</dialog>
@endsection
@push('script')
<script>
document.querySelectorAll('.edit-pos').forEach(btn => {
  btn.addEventListener('click', () => {
    const d = document.getElementById('editPos');
    const f = d.querySelector('form');
    f.action = btn.dataset.route;
    ['name','asset_class','venue','symbol','currency','allocated_amount','market_value','pnl','pnl_percent','status','opened_on','notes'].forEach(k => {
      const el = f.querySelector(`[name=${k}]`); if (el) el.value = btn.dataset[k] || '';
    });
    d.showModal();
  });
});
</script>
@endpush
