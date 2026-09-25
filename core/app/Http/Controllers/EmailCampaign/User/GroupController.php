<?php

namespace App\Http\Controllers\EmailCampaign\User;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcGroup;
use App\Models\EmailCampaign\EcGroupMember;
use App\Support\EmailCampaign\EcGroupAccess;
use App\Support\EmailCampaign\EcRecipientImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $user = Auth::guard('ec_user')->user();
        $pageTitle = 'Email Groups';
        $groups = EcGroupAccess::queryForUser($user->id)->withCount('members')->latest('id')->paginate(20);

        return view('emailcampaign.user.groups.index', compact('pageTitle', 'groups'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('ec_user')->user();

        $data = $request->validate([
            'name'        => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
        ]);

        $group = EcGroup::query()->create([
            'ec_user_id'  => $user->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        EcActivityLog::record('user', $user->id, 'group.created', 'Created group: ' . $group->name, 'group', $group->id);

        return back()->withNotify([['success', 'Group created.']]);
    }

    public function show(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $group = EcGroupAccess::queryForUser($user->id)->with('members')->findOrFail($id);
        $pageTitle = 'Group: ' . $group->name;
        $readOnly = !EcGroupAccess::userCanManage($user->id, $group);

        return view('emailcampaign.user.groups.show', compact('pageTitle', 'group', 'readOnly'));
    }

    public function addMember(Request $request, int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $group = EcGroup::query()->where('ec_user_id', $user->id)->findOrFail($id);

        $data = $request->validate([
            'email' => 'required|email',
            'name'  => 'nullable|string|max:120',
        ]);

        EcGroupMember::query()->firstOrCreate(
            ['ec_group_id' => $group->id, 'email' => strtolower($data['email'])],
            ['name' => $data['name'] ?? null, 'merge_data' => []]
        );

        EcActivityLog::record('user', $user->id, 'group.member_added', 'Added ' . $data['email'] . ' to ' . $group->name, 'group', $group->id);

        return back()->withNotify([['success', 'Recipient added to group.']]);
    }

    public function importMembers(Request $request, int $id, EcRecipientImport $import)
    {
        $user = Auth::guard('ec_user')->user();
        $group = EcGroup::query()->where('ec_user_id', $user->id)->findOrFail($id);

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

        EcActivityLog::record(
            'user',
            $user->id,
            'group.import',
            "Imported {$added} rows into {$group->name}",
            'group',
            $group->id
        );

        return back()->withNotify([['success', "Imported {$added} recipients (duplicates skipped)."]]);
    }

    public function destroyMember(int $groupId, int $memberId)
    {
        $user = Auth::guard('ec_user')->user();
        $group = EcGroup::query()->where('ec_user_id', $user->id)->findOrFail($groupId);

        EcGroupMember::query()->where('ec_group_id', $group->id)->where('id', $memberId)->delete();

        return back()->withNotify([['success', 'Member removed.']]);
    }
}
