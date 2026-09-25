@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Lead pipeline</h2>
        <button class="crm-btn crm-btn--accent" onclick="document.getElementById('addClient').showModal()">Add lead</button>
    </div>

    <div class="io-pipeline io-pipeline--filter">
        <a href="{{ route('crm.clients.index') }}" class="io-pipeline__col {{ !request('stage') ? 'is-active' : '' }}">
            <em>All</em>
            <strong>{{ array_sum($pipelineCounts) }}</strong>
        </a>
        @foreach($stages as $key => $label)
            <a href="{{ route('crm.clients.index', ['stage' => $key]) }}" class="io-pipeline__col {{ request('stage')===$key ? 'is-active' : '' }}">
                <em>{{ $label }}</em>
                <strong>{{ $pipelineCounts[$key] ?? 0 }}</strong>
            </a>
        @endforeach
    </div>

    <div class="crm-table-wrap" style="margin-top:18px">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Lead</th><th>Stage</th><th>Officer</th><th>Expected</th><th>Committed</th>
                    <th>Upfront 2%</th><th>Follow-up</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>
                            <strong>{{ $client->name }}</strong>
                            <br><small>{{ $client->email ?: $client->phone }}</small>
                        </td>
                        <td><span class="crm-pill">{{ $client->stageLabel() }}</span></td>
                        <td>{{ optional($client->owner)->name ?: '—' }}</td>
                        <td>${{ number_format($client->expected_aum, 0) }}</td>
                        <td>${{ number_format($client->committed_aum, 0) }}</td>
                        <td>${{ number_format($client->upfront_commission, 2) }}</td>
                        <td>{{ optional($client->next_follow_up)->format('d M Y') ?: '—' }}</td>
                        <td>
                            <button class="crm-btn crm-btn--sm edit-client"
                                data-route="{{ route('crm.clients.update', $client->id) }}"
                                data-name="{{ $client->name }}"
                                data-email="{{ $client->email }}"
                                data-phone="{{ $client->phone }}"
                                data-company="{{ $client->company }}"
                                data-country="{{ $client->country }}"
                                data-city="{{ $client->city }}"
                                data-source="{{ $client->source }}"
                                data-stage="{{ $client->stage }}"
                                data-expected_aum="{{ $client->expected_aum }}"
                                data-committed_aum="{{ $client->committed_aum }}"
                                data-notes="{{ $client->notes }}"
                                data-next_follow_up="{{ optional($client->next_follow_up)->format('Y-m-d') }}"
                                data-owner="{{ $client->owner_staff_id }}">Update</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No leads in this stage.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($clients, 'hasPages') && $clients->hasPages())
        <div class="crm-pagination">{{ $clients->links() }}</div>
    @endif
</div>

<dialog id="addClient" class="crm-dialog">
    <form method="post" action="{{ route('crm.clients.store') }}">@csrf
        <h3>New lead</h3>
        @include('crm.clients.form_fields', ['stages' => $stages, 'agents' => $agents])
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Save</button>
        </div>
    </form>
</dialog>
<dialog id="editClient" class="crm-dialog">
    <form method="post" action="">@csrf
        <h3>Update lead</h3>
        @include('crm.clients.form_fields', ['stages' => $stages, 'agents' => $agents])
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Update</button>
        </div>
    </form>
</dialog>
@endsection
@push('script')
<script>
document.querySelectorAll('.edit-client').forEach(btn => {
  btn.addEventListener('click', () => {
    const d = document.getElementById('editClient');
    const f = d.querySelector('form');
    f.action = btn.dataset.route;
    f.querySelector('[name=name]').value = btn.dataset.name || '';
    f.querySelector('[name=email]').value = btn.dataset.email || '';
    f.querySelector('[name=phone]').value = btn.dataset.phone || '';
    f.querySelector('[name=company]').value = btn.dataset.company || '';
    f.querySelector('[name=country]').value = btn.dataset.country || '';
    f.querySelector('[name=city]').value = btn.dataset.city || '';
    f.querySelector('[name=source]').value = btn.dataset.source || '';
    f.querySelector('[name=stage]').value = btn.dataset.stage || 'lead';
    f.querySelector('[name=expected_aum]').value = btn.dataset.expected_aum || '';
    f.querySelector('[name=committed_aum]').value = btn.dataset.committed_aum || '';
    f.querySelector('[name=notes]').value = btn.dataset.notes || '';
    f.querySelector('[name=next_follow_up]').value = btn.dataset.next_follow_up || '';
    const owner = f.querySelector('[name=owner_staff_id]'); if (owner) owner.value = btn.dataset.owner || '';
    d.showModal();
  });
});
</script>
@endpush
