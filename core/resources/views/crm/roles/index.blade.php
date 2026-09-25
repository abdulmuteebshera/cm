@extends('crm.layouts.app')
@section('panel')
<div class="crm-card">
    <div class="crm-card__head">
        <h2>Roles</h2>
        <button type="button" class="crm-btn crm-btn--accent" onclick="document.getElementById('addRole').showModal()">Create role</button>
    </div>
    <p class="crm-muted">
        Set shared CRM access for each role (Manager, Investment Officer, Trader, Finance).
        <strong>Do not assign Admin Desk tasks here.</strong>
        Assign Portfolio Allocation / Announcements / etc. per manager when creating or editing that staff member under Staff.
        Investment Officers all get the same officer tools (leads, commissions, ranking).
    </p>
</div>

@foreach($roles as $role)
    <div class="crm-card">
        <form method="post" action="{{ route('crm.roles.update', $role->id) }}">
            @csrf
            <div class="crm-card__head">
                <div>
                    <h3>{{ $role->name }} @if($role->is_system)<span class="crm-pill">System</span>@endif</h3>
                    <p class="crm-muted">Portal: <code>/internalportal/{{ $role->portal }}</code> · {{ $role->staff_count }} staff</p>
                </div>
                <button class="crm-btn crm-btn--accent crm-btn--sm">Save permissions</button>
            </div>
            <div class="crm-form-inline">
                <label>Name<input name="name" value="{{ $role->name }}" required></label>
                <label>Portal
                    <select name="portal" required {{ $role->is_system ? '' : '' }}>
                        @foreach(['crm','manager','agent','trader','finance'] as $p)
                            <option value="{{ $p }}" @selected($role->portal === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Description<input name="description" value="{{ $role->description }}"></label>
                @unless($role->is_system)
                    <label>Status
                        <select name="status">
                            <option value="1" @selected($role->status)>Active</option>
                            <option value="0" @selected(!$role->status)>Disabled</option>
                        </select>
                    </label>
                @else
                    <input type="hidden" name="status" value="1">
                @endunless
            </div>
            @foreach($permissions as $group => $items)
                <div class="crm-perm-group">
                    <h4>{{ $group }}</h4>
                    <div class="crm-perm-grid">
                        @foreach($items as $perm)
                            <label class="crm-check">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" @checked($role->permissions->contains('id', $perm->id))>
                                <span>{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>
    </div>
@endforeach

<dialog id="addRole" class="crm-dialog">
    <form method="post" action="{{ route('crm.roles.store') }}">
        @csrf
        <h3>New role</h3>
        <label>Name<input name="name" required></label>
        <label>Portal
            <select name="portal" required>
                <option value="manager">manager</option>
                <option value="agent">agent</option>
                <option value="trader">trader</option>
                <option value="finance">finance</option>
                <option value="crm">crm</option>
            </select>
        </label>
        <label>Description<input name="description"></label>
        @foreach($permissions as $group => $items)
            <div class="crm-perm-group">
                <h4>{{ $group }}</h4>
                <div class="crm-perm-grid">
                    @foreach($items as $perm)
                        <label class="crm-check">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}">
                            <span>{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="crm-dialog__actions">
            <button type="button" class="crm-btn" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="crm-btn crm-btn--accent">Create</button>
        </div>
    </form>
</dialog>
@endsection
