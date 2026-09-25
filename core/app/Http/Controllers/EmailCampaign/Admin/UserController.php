<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $pageTitle = 'Campaign Users';
        $users = EcUser::query()->withCount('campaigns')->latest('id')->paginate(20);

        return view('emailcampaign.admin.users.index', compact('pageTitle', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'username' => 'required|string|max:80|unique:ec_users,username',
            'email'    => 'nullable|email|max:120',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = EcUser::query()->create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'status'   => 1,
        ]);

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'user.created',
            'Created campaign user: ' . $user->username,
            'user',
            $user->id
        );

        return back()->withNotify([['success', 'User account created.']]);
    }

    public function update(Request $request, int $id)
    {
        $user = EcUser::query()->findOrFail($id);

        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'username' => 'required|string|max:80|unique:ec_users,username,' . $user->id,
            'email'    => 'nullable|email|max:120',
            'password' => 'nullable|string|min:8|confirmed',
            'status'   => 'required|in:0,1',
        ]);

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'] ?? null;
        $user->status = (int) $data['status'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'user.updated',
            'Updated campaign user: ' . $user->username,
            'user',
            $user->id
        );

        return back()->withNotify([['success', 'User updated.']]);
    }
}
