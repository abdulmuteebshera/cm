@extends('crm.layouts.app')
@section('panel')
<div class="crm-grid crm-grid--stats">
    <div class="crm-stat"><span>Upfront ({{ $summary['upfront_rate'] }}%)</span><strong>${{ number_format($summary['upfront'], 2) }}</strong></div>
    <div class="crm-stat"><span>Retention ({{ $summary['retention_rate'] }}%/yr)</span><strong>${{ number_format($summary['retention'], 2) }}</strong></div>
    <div class="crm-stat"><span>Pending</span><strong>${{ number_format($summary['pending'], 2) }}</strong></div>
    <div class="crm-stat"><span>Paid</span><strong>${{ number_format($summary['paid'], 2) }}</strong></div>
</div>

<div class="crm-card">
    <div class="crm-card__head">
        <div>
            <h2>Commission dashboard</h2>
            <p class="crm-muted" style="margin:6px 0 0">Program: <strong>2% upfront</strong> on funding + <strong>1% yearly retention</strong> while the investor remains invested.</p>
        </div>
        <div class="crm-actions" style="margin:0">
            @if($staff->isSuper() || $staff->portal() !== 'agent')
                <form method="post" action="{{ route('crm.commissions.retention') }}">@csrf
                    <button class="crm-btn crm-btn--sm">Generate {{ date('Y') }} retention</button>
                </form>
                <button class="crm-btn crm-btn--accent crm-btn--sm" onclick="document.getElementById('addCom').showModal()">Add manual</button>
            @endif
        </div>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Title</th><th>Officer</th><th>Base</th><th>Rate</th><th>Amount</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($commissions as $c)
                    <tr>
                        <td>{{ $c->title }}<br><small>{{ $c->period }}</small></td>
                        <td>{{ optional($c->staff)->name }}</td>
                        <td>${{ number_format($c->base_amount, 2) }}</td>
                        <td>{{ $c->rate_percent }}%</td>
                        <td>${{ number_format($c->commission_amount, 2) }}</td>
                        <td><span class="crm-pill">{{ $c->status }}</span></td>
                        <td>
                            @if($staff->isSuper() || $staff->portal() !== 'agent')
                            <form method="post" action="{{ route('crm.commissions.update', $c->id) }}" class="crm-inline-form">@csrf
                                <select name="status">
                                    @foreach(['pending','approved','paid','rejected'] as $st)
                                        <option value="{{ $st }}" @selected($c->status===$st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="crm-btn crm-btn--sm">Update</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No commissions yet. Fund a lead to auto-create 2% upfront.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())<div class="crm-pagination">{{ $commissions->links() }}</div>@endif
</div>

<dialog id="addCom" class="crm-dialog">
    <form method="post" action="{{ route('crm.commissions.store') }}">@csrf
        <h3>Manual commission</h3>
        <label>Officer
            <select name="staff_id" required>
                @foreach($agents as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
            </select>
        </label>
        <label>Client
            <select name="client_id">
                <option value="">Optional</option>
                @foreach($clients as $cl)<option value="{{ $cl->id }}">{{ $cl->name }}</option>@endforeach
            </select>
        </label>
        <label>Title<input name="title" required></label>
        <label>Period<input name="period" placeholder="2026 or 2026-Q1"></label>
        <label>Base amount<input type="number" step="0.01" name="base_amount" required></label>
        <label>Rate %
            <select name="rate_percent">
                <option value="2">2% Upfront</option>
                <option value="1">1% Retention</option>
            </select>
        </label>
        <label>Status
            <select name="status">
                <option value="pending">pending</option>
                <option value="approved">approved</option>
                <option value="paid">paid</option>
            </select>
        </label>
        <label>Notes<textarea name="notes" rows="2"></textarea></label>
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Save</button>
        </div>
    </form>
</dialog>
@endsection
