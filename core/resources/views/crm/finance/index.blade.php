@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Income</span><strong>${{ number_format($summary['income'], 2) }}</strong></div>
    <div class="crm-stat"><span>Expense</span><strong>${{ number_format($summary['expense'], 2) }}</strong></div>
    <div class="crm-stat"><span>Net P&amp;L</span><strong>${{ number_format($summary['net'], 2) }}</strong></div>
</div>
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Finance ledger</h2>
        <button class="crm-btn crm-btn--accent" onclick="document.getElementById('addFin').showModal()">Add entry</button>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Date</th><th>Type</th><th>Category</th><th>Title</th><th>Amount</th><th></th></tr></thead>
            <tbody>
                @forelse($entries as $e)
                    <tr>
                        <td>{{ $e->entry_date->format('d M Y') }}</td>
                        <td>{{ $e->type }}</td>
                        <td>{{ $e->category ?: '—' }}</td>
                        <td>{{ $e->title }}</td>
                        <td>${{ number_format($e->amount, 2) }} {{ $e->currency }}</td>
                        <td>
                            <form method="post" action="{{ route('crm.finance.ledger.delete', $e->id) }}" onsubmit="return confirm('Delete entry?')">@csrf
                                <button class="crm-btn crm-btn--sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No entries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())<div class="crm-pagination">{{ $entries->links() }}</div>@endif
</div>
<dialog id="addFin" class="crm-dialog">
    <form method="post" action="{{ route('crm.finance.ledger.store') }}">@csrf
        <h3>Finance entry</h3>
        <label>Date<input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required></label>
        <label>Type
            <select name="type" required>
                @foreach(['income','expense','asset','liability','equity'] as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </label>
        <label>Category<input name="category" placeholder="Management fee / OpEx / ..."></label>
        <label>Title<input name="title" required></label>
        <label>Amount<input type="number" step="0.01" name="amount" required></label>
        <label>Currency<input name="currency" value="USD"></label>
        <label>Notes<textarea name="notes" rows="2"></textarea></label>
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Save</button>
        </div>
    </form>
</dialog>
@endsection
