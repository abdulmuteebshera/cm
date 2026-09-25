<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmPermission;
use App\Models\Crm\CrmRole;
use App\Models\Crm\CrmStaff;
use App\Support\CrmAdminBridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $pageTitle = 'CRM Staff';
        $staffList = CrmStaff::with(['role', 'permissions'])->orderByDesc('id')->paginate(getPaginate());
        $roles     = CrmRole::active()->orderBy('name')->get();
        $adminPerms = CrmPermission::whereIn('slug', array_keys(CrmAdminBridge::permissionMap()))
            ->orderBy('name')
            ->get();

        return view('crm.staff.index', compact('pageTitle', 'staffList', 'roles', 'adminPerms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:120',
            'email'           => 'required|email|max:120|unique:crm_staff,email',
            'password'        => 'required|string|min:8',
            'crm_role_id'     => 'required|exists:crm_roles,id',
            'phone'           => 'nullable|string|max:50',
            'department'      => 'nullable|string|max:80',
            'title'           => 'nullable|string|max:120',
            'status'          => 'nullable|in:0,1',
            'permissions'     => 'nullable|array',
            'permissions.*'   => 'integer|exists:crm_permissions,id',
        ]);

        $role = CrmRole::findOrFail($request->crm_role_id);

        $staff = CrmStaff::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'crm_role_id'   => $role->id,
            'phone'         => $request->phone,
            'department'    => $request->department,
            'title'         => $request->title,
            'status'        => $request->status ? 1 : 0,
            'is_super'      => $role->slug === 'super-admin' ? 1 : 0,
            'employee_code' => $request->employee_code,
        ]);

        $this->syncManagerPermissions($staff, $role, $request->permissions ?? []);

        $notify[] = ['success', 'Staff member created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $staff = CrmStaff::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:120',
            'email'         => 'required|email|max:120|unique:crm_staff,email,' . $staff->id,
            'password'      => 'nullable|string|min:8',
            'crm_role_id'   => 'required|exists:crm_roles,id',
            'phone'         => 'nullable|string|max:50',
            'department'    => 'nullable|string|max:80',
            'title'         => 'nullable|string|max:120',
            'status'        => 'nullable|in:0,1',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:crm_permissions,id',
        ]);

        $role = CrmRole::findOrFail($request->crm_role_id);

        $staff->name          = $request->name;
        $staff->email         = $request->email;
        $staff->crm_role_id   = $role->id;
        $staff->phone         = $request->phone;
        $staff->department    = $request->department;
        $staff->title         = $request->title;
        $staff->status        = $request->status ? 1 : 0;
        $staff->is_super      = $role->slug === 'super-admin' ? 1 : 0;
        $staff->employee_code = $request->employee_code;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();
        $this->syncManagerPermissions($staff, $role, $request->permissions ?? []);

        $notify[] = ['success', 'Staff member updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $staff = CrmStaff::findOrFail($id);

        if ($staff->is_super && $staff->status) {
            $notify[] = ['error', 'Cannot disable the super admin account.'];
            return back()->withNotify($notify);
        }

        $staff->status = $staff->status ? 0 : 1;
        $staff->save();

        $notify[] = ['success', 'Staff status updated'];
        return back()->withNotify($notify);
    }

    protected function syncManagerPermissions(CrmStaff $staff, CrmRole $role, array $permissionIds): void
    {
        $isManager = $role->portal === 'manager' || $role->slug === 'manager';

        if (!$isManager) {
            $staff->permissions()->detach();
            return;
        }

        $allowedIds = CrmPermission::whereIn('slug', array_keys(CrmAdminBridge::permissionMap()))
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->all();

        // Always include bridge permission so manager can enter admin with their modules
        $bridgeId = CrmPermission::where('slug', 'crm.admin_bridge')->value('id');
        if ($bridgeId && !empty($allowedIds)) {
            $allowedIds[] = $bridgeId;
            $allowedIds = array_unique($allowedIds);
        }

        $staff->permissions()->sync($allowedIds);
    }
}
