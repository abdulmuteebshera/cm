@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Team members</h2>
        <button type="button" class="crm-btn crm-btn--accent" onclick="document.getElementById('addStaff').showModal()">Add staff</button>
    </div>
    <p class="crm-muted">
        When you add a <strong>Manager</strong>, tick that person’s admin tasks (e.g. only Portfolio Allocation, or only Announcements).
        Investment Officers all share the same tools — ranking, commissions, and lead pipeline — with no per-person admin task list.
    </p>
    <div class="crm-table-wrap">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th><th>Manager tasks</th><th>Portal</th><th>Status</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffList as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ optional($member->role)->name ?: '—' }}</td>
                        <td>
                            @if(($member->role->portal ?? null) === 'manager')
                                @forelse($member->permissions->where(fn($p) => str_starts_with($p->slug, 'admin.')) as $perm)
                                    <span class="crm-pill">{{ $perm->name }}</span>
                                @empty
                                    <span class="crm-muted">None assigned</span>
                                @endforelse
                            @else
                                —
                            @endif
                        </td>
                        <td><code>/internalportal/{{ optional($member->role)->portal ?: 'crm' }}</code></td>
                        <td>{{ $member->status ? 'Active' : 'Disabled' }}</td>
                        <td class="crm-row-actions">
                            <button type="button" class="crm-btn crm-btn--sm edit-staff"
                                data-id="{{ $member->id }}"
                                data-name="{{ $member->name }}"
                                data-email="{{ $member->email }}"
                                data-phone="{{ $member->phone }}"
                                data-department="{{ $member->department }}"
                                data-title="{{ $member->title }}"
                                data-employee_code="{{ $member->employee_code }}"
                                data-role="{{ $member->crm_role_id }}"
                                data-status="{{ $member->status }}"
                                data-portal="{{ optional($member->role)->portal }}"
                                data-perms="{{ $member->permissions->pluck('id')->implode(',') }}"
                                data-route="{{ route('crm.staff.update', $member->id) }}">Edit</button>
                            @unless($member->is_super)
                                <form action="{{ route('crm.staff.status', $member->id) }}" method="post" style="display:inline">@csrf
                                    <button class="crm-btn crm-btn--sm">{{ $member->status ? 'Disable' : 'Enable' }}</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No staff yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($staffList->hasPages())
        <div class="crm-pagination">{{ $staffList->links() }}</div>
    @endif
</div>

<dialog id="addStaff" class="crm-dialog crm-dialog--wide">
    <form method="post" action="{{ route('crm.staff.store') }}">
        @csrf
        <h3>New staff</h3>
        @include('crm.staff.form_fields', ['roles' => $roles, 'adminPerms' => $adminPerms])
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Create</button>
        </div>
    </form>
</dialog>

<dialog id="editStaff" class="crm-dialog crm-dialog--wide">
    <form method="post" action="">
        @csrf
        <h3>Edit staff</h3>
        @include('crm.staff.form_fields', ['roles' => $roles, 'adminPerms' => $adminPerms, 'edit' => true])
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Update</button>
        </div>
    </form>
</dialog>
@endsection

@push('script')
<script>
const rolePortals = @json($roles->pluck('portal', 'id'));
function toggleManagerPerms(form) {
    const roleId = form.querySelector('[name=crm_role_id]')?.value;
    const box = form.querySelector('.manager-perms');
    if (!box) return;
    box.style.display = (rolePortals[roleId] === 'manager') ? 'block' : 'none';
}
document.querySelectorAll('dialog form').forEach(form => {
    form.querySelector('[name=crm_role_id]')?.addEventListener('change', () => toggleManagerPerms(form));
    toggleManagerPerms(form);
});
document.querySelectorAll('.edit-staff').forEach(btn => {
    btn.addEventListener('click', () => {
        const d = document.getElementById('editStaff');
        const f = d.querySelector('form');
        f.action = btn.dataset.route;
        f.querySelector('[name=name]').value = btn.dataset.name || '';
        f.querySelector('[name=email]').value = btn.dataset.email || '';
        f.querySelector('[name=phone]').value = btn.dataset.phone || '';
        f.querySelector('[name=department]').value = btn.dataset.department || '';
        f.querySelector('[name=title]').value = btn.dataset.title || '';
        f.querySelector('[name=employee_code]').value = btn.dataset.employee_code || '';
        f.querySelector('[name=crm_role_id]').value = btn.dataset.role || '';
        f.querySelector('[name=status]').value = btn.dataset.status || '1';
        f.querySelector('[name=password]').value = '';
        const selected = (btn.dataset.perms || '').split(',').filter(Boolean);
        f.querySelectorAll('[name="permissions[]"]').forEach(cb => {
            cb.checked = selected.includes(cb.value);
        });
        toggleManagerPerms(f);
        d.showModal();
    });
});
</script>
@endpush
