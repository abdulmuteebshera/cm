@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Referrals</h2>
        <button class="crm-btn crm-btn--accent" onclick="document.getElementById('addRef').showModal()">Log referral</button>
    </div>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead><tr><th>Prospect</th><th>Referrer</th><th>Potential</th><th>Status</th><th>Officer</th><th></th></tr></thead>
            <tbody>
                @forelse($referrals as $r)
                    <tr>
                        <td>{{ $r->prospect_name }}<br><small>{{ $r->prospect_email }}</small></td>
                        <td>{{ $r->referrer_name ?: '—' }}</td>
                        <td>${{ number_format($r->potential_amount, 2) }}</td>
                        <td>{{ $r->status }}</td>
                        <td>{{ optional($r->staff)->name }}</td>
                        <td>
                            <form method="post" action="{{ route('crm.referrals.update', $r->id) }}" class="crm-inline-form">@csrf
                                <input type="hidden" name="prospect_name" value="{{ $r->prospect_name }}">
                                <input type="hidden" name="prospect_email" value="{{ $r->prospect_email }}">
                                <input type="hidden" name="prospect_phone" value="{{ $r->prospect_phone }}">
                                <input type="hidden" name="referrer_name" value="{{ $r->referrer_name }}">
                                <input type="hidden" name="referrer_email" value="{{ $r->referrer_email }}">
                                <input type="hidden" name="potential_amount" value="{{ $r->potential_amount }}">
                                <input type="hidden" name="notes" value="{{ $r->notes }}">
                                <select name="status">
                                    @foreach(['pending','contacted','converted','closed'] as $st)
                                        <option value="{{ $st }}" @selected($r->status===$st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="crm-btn crm-btn--sm">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No referrals.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())<div class="crm-pagination">{{ $referrals->links() }}</div>@endif
</div>
<dialog id="addRef" class="crm-dialog">
    <form method="post" action="{{ route('crm.referrals.store') }}">@csrf
        <h3>New referral</h3>
        <label>Prospect name<input name="prospect_name" required></label>
        <label>Prospect email<input type="email" name="prospect_email"></label>
        <label>Prospect phone<input name="prospect_phone"></label>
        <label>Referrer name<input name="referrer_name"></label>
        <label>Referrer email<input type="email" name="referrer_email"></label>
        <label>Potential amount<input type="number" step="0.01" name="potential_amount"></label>
        <label>Notes<textarea name="notes" rows="3"></textarea></label>
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Save</button>
        </div>
    </form>
</dialog>
@endsection
