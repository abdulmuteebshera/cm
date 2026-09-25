<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Crm\CrmStaff;
use App\Support\CrmAdminBridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminBridgeController extends Controller
{
    public function enter(Request $request)
    {
        /** @var CrmStaff $staff */
        $staff = Auth::guard('crm')->user();

        $adminPermissions = $staff?->adminDeskPermissionSlugs() ?? [];
        $canEnter = $staff && ($staff->isSuper() || $staff->hasPermission('crm.admin_bridge') || !empty($adminPermissions));

        if (!$canEnter) {
            $notify[] = ['error', 'You are not allowed to enter the live admin desk.'];
            return back()->withNotify($notify);
        }

        if (!$staff->isSuper() && empty($adminPermissions)) {
            $notify[] = ['error', 'No Admin Desk modules are assigned to this manager. Open Staff → Edit and select modules for this manager.'];
            return back()->withNotify($notify);
        }

        $admin = Admin::orderBy('id')->first();
        if (!$admin) {
            $notify[] = ['error', 'No live admin account exists to bridge into.'];
            return back()->withNotify($notify);
        }

        $token = Str::random(64);
        Cache::put('crm_admin_sso_' . $token, [
            'staff_id'    => $staff->id,
            'staff_name'  => $staff->name,
            'staff_email' => $staff->email,
            'admin_id'    => $admin->id,
            'full_access' => $staff->isSuper(),
            'permissions' => $adminPermissions,
        ], now()->addMinutes(3));

        return redirect()->route('crm.admin.bridge.consume', ['token' => $token]);
    }

    public function consume(Request $request, string $token)
    {
        $payload = Cache::pull('crm_admin_sso_' . $token);

        if (!$payload || empty($payload['admin_id'])) {
            $notify[] = ['error', 'Admin bridge link expired. Open Admin Desk again from CRM.'];
            return to_route('crm.login')->withNotify($notify);
        }

        $admin = Admin::find($payload['admin_id']);
        if (!$admin) {
            $notify[] = ['error', 'Admin account not found.'];
            return to_route('crm.login')->withNotify($notify);
        }

        Auth::guard('admin')->login($admin, false);
        $request->session()->regenerate();

        $request->session()->put('crm_admin_bridge', [
            'staff_id'    => $payload['staff_id'],
            'staff_name'  => $payload['staff_name'],
            'staff_email' => $payload['staff_email'],
            'full_access' => (bool) ($payload['full_access'] ?? false),
            'permissions' => $payload['permissions'] ?? [],
            'entered_at'  => now()->toDateTimeString(),
        ]);

        $notify[] = ['success', 'Entered live admin desk from CRM' . (!empty($payload['full_access']) ? ' (full access).' : ' (scoped modules only).')];
        return to_route('admin.dashboard')->withNotify($notify);
    }

    protected function resolveAdminPermissions(CrmStaff $staff): array
    {
        if ($staff->isSuper()) {
            return array_keys(CrmAdminBridge::permissionMap());
        }

        if (!$staff->relationLoaded('role')) {
            $staff->load('role.permissions');
        } elseif ($staff->role && !$staff->role->relationLoaded('permissions')) {
            $staff->role->load('permissions');
        }

        $slugs = optional($staff->role)->permissions?->pluck('slug')->all() ?? [];

        return array_values(array_intersect($slugs, array_keys(CrmAdminBridge::permissionMap())));
    }
}
