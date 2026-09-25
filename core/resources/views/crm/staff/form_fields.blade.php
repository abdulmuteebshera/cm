<label>Name<input name="name" required></label>
<label>Email<input type="email" name="email" required></label>
<label>Password<input type="password" name="password" {{ empty($edit) ? 'required' : '' }} placeholder="{{ !empty($edit) ? 'Leave blank to keep' : '' }}"></label>
<label>Phone<input name="phone"></label>
<label>Department<input name="department"></label>
<label>Title<input name="title"></label>
<label>Employee code<input name="employee_code"></label>
<label>Role
    <select name="crm_role_id" required>
        <option value="">Select role</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}" data-portal="{{ $role->portal }}">{{ $role->name }} ({{ $role->portal }})</option>
        @endforeach
    </select>
</label>
<label>Status
    <select name="status">
        <option value="1">Active</option>
        <option value="0">Disabled</option>
    </select>
</label>

{{-- Only for Managers: pick which admin tasks THIS person handles (e.g. Allocation vs Announcements). --}}
<div class="manager-perms" style="display:none;grid-column:1/-1">
    <div class="crm-card" style="margin:8px 0 0;box-shadow:none;border:1px solid var(--crm-border, #d8dee8)">
        <h4 style="margin:0 0 6px">This manager’s admin tasks</h4>
        <p class="crm-muted" style="margin:0 0 12px">
            Choose what <strong>this</strong> manager can do in live Admin.
            Example: Manager 1 → Portfolio Allocation only; Manager 2 → Announcements only.
            Investment Officers are not affected — they all share the same officer tools.
        </p>
        <div class="crm-perm-grid">
            @foreach($adminPerms as $perm)
                <label class="crm-check">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}">
                    <span>{{ $perm->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
