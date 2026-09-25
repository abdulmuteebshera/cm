<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmPermission;
use App\Models\Crm\CrmRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $pageTitle   = 'Roles & Permissions';
        $roles       = CrmRole::withCount('staff')->with('permissions')->orderBy('id')->get();
        // Admin Desk modules (admin.*) are assigned per Manager on Staff — not on the shared Manager role.
        $permissions = CrmPermission::where('slug', 'not like', 'admin.%')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        return view('crm.roles.index', compact('pageTitle', 'roles', 'permissions'));
    }

    protected function syncRolePermissions(CrmRole $role, ?array $permissionIds): void
    {
        $ids = collect($permissionIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        // Never store per-person admin desk modules on a role.
        $safeIds = CrmPermission::whereIn('id', $ids)
            ->where('slug', 'not like', 'admin.%')
            ->pluck('id')
            ->all();

        $role->permissions()->sync($safeIds);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:80',
            'portal'        => 'required|in:crm,manager,agent,trader,finance',
            'description'   => 'nullable|string|max:500',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:crm_permissions,id',
        ]);

        $role = CrmRole::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name) . '-' . Str::random(4),
            'portal'      => $request->portal,
            'description' => $request->description,
            'is_system'   => 0,
            'status'      => 1,
        ]);

        $this->syncRolePermissions($role, $request->permissions);

        $notify[] = ['success', 'Role created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $role = CrmRole::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:80',
            'portal'        => 'required|in:crm,manager,agent,trader,finance',
            'description'   => 'nullable|string|max:500',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:crm_permissions,id',
            'status'        => 'nullable|in:0,1',
        ]);

        $role->name        = $request->name;
        $role->portal      = $request->portal;
        $role->description = $request->description;
        if (!$role->is_system) {
            $role->status = $request->status ? 1 : 0;
        }
        $role->save();

        $this->syncRolePermissions($role, $request->permissions);

        $notify[] = ['success', 'Role permissions updated'];
        return back()->withNotify($notify);
    }
}
