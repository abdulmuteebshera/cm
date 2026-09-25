<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcGroup;
use App\Models\EmailCampaign\EcGroupMember;
use App\Support\EmailCampaign\EcRecipientImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('ec_admin')->user();
        $pageTitle = 'Email Groups';
        $groups = EcGroup::query()->where('ec_admin_id', $admin->id)->withCount('members')->latest('id')->paginate(20);

        return view('emailcampaign.admin.groups.index', compact('pageTitle', 'groups'));
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('ec_admin')->user();

        $data = $request->validate([
            'name'        => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'is_private'  => 'nullable|in:0,1',
        ]);

        $group = EcGroup::query()->create([
            'ec_admin_id' => $admin->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_private'  => (int) ($data['is_private'] ?? 0),
        ]);

        EcActivityLog::record('admin', $admin->id, 'group.created', 'Created group: ' . $group->name, 'group', $group->id);

        return back()->withNotify([['success', 'Group created.']]);
    }

    public function show(int $id)
    {
        $admin = Auth::guard('ec_admin')->user();
        $group = EcGroup::query()->where('ec_admin_id', $admin->id)->with('members')->findOrFail($id);
        $pageTitle = 'Group: ' . $group->name;

        return view('emailcampaign.admin.groups.show', compact('pageTitle', 'group'));
    }

    public function addMember(Request $request, int $id)
    {
        $admin = Auth::guard('ec_admin')->user();
        $group = EcGroup::query()->where('ec_admin_id', $admin->id)->findOrFail($id);

        $data = $request->validate([
            'email' => 'required|email',
            'name'  => 'nullable|string|max:120',
        ]);

        EcGroupMember::query()->firstOrCreate(
            ['ec_group_id' => $group->id, 'email' => strtolower($data['email'])],
            ['name' => $data['name'] ?? null, 'merge_data' => []]
        );

        EcActivityLog::record('admin', $admin->id, 'group.member_added', 'Added ' . $data['email'] . ' to ' . $group->name, 'group', $group->id);

        return back()->withNotify([['success', 'Recipient added to group.']]);
    }

    public function importMembers(Request $request, int $id, EcRecipientImport $import)
    {
        $admin = Auth::guard('ec_admin')->user();
        $group = EcGroup::query()->where('ec_admin_id', $admin->id)->findOrFail($id);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            $rows = $import->parseUploadedFile($request->file('file'));
        } catch (\Throwable $e) {
            return back()->withNotify([['error', $e->getMessage()]]);
        }

        $added = 0;
        foreach ($rows as $row) {
            EcGroupMember::query()->firstOrCreate(
                ['ec_group_id' => $group->id, 'email' => $row['email']],
                ['name' => $row['name'], 'merge_data' => $row['merge_data']]
            );
            $added++;
        }

        EcActivityLog::record('admin', $admin->id, 'group.import', "Imported {$added} rows into {$group->name}", 'group', $group->id);

        return back()->withNotify([['success', "Imported {$added} recipients (duplicates skipped)."]]);
    }

    public function destroyMember(int $groupId, int $memberId)
    {
        $admin = Auth::guard('ec_admin')->user();
        $group = EcGroup::query()->where('ec_admin_id', $admin->id)->findOrFail($groupId);

        EcGroupMember::query()->where('ec_group_id', $group->id)->where('id', $memberId)->delete();

        return back()->withNotify([['success', 'Member removed.']]);
    }

    public function update(Request $request, int $id)
    {
        $admin = Auth::guard('ec_admin')->user();
        $group = EcGroup::query()->where('ec_admin_id', $admin->id)->findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'is_private'  => 'required|in:0,1',
        ]);

        $group->fill($data);
        $group->save();

        EcActivityLog::record('admin', $admin->id, 'group.updated', 'Updated group: ' . $group->name, 'group', $group->id);

        return back()->withNotify([['success', 'Group updated.']]);
    }
}
