<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Leaderboard';
        $entries   = Leaderboard::with('user')->orderByDesc('amount')->paginate(getPaginate());
        return view('admin.leaderboard.index', compact('pageTitle', 'entries'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $entry = new Leaderboard();
        $this->submitData($entry, $request);

        $notify[] = ['success', 'Leaderboard entry added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);

        $entry = Leaderboard::findOrFail($id);
        $this->submitData($entry, $request);

        $notify[] = ['success', 'Leaderboard entry updated successfully'];
        return back()->withNotify($notify);
    }

    private function submitData(Leaderboard $entry, Request $request): void
    {
        if ($request->type == 'user') {
            $user = User::where('username', $request->username)->first();

            $entry->user_id = $user->id;
            $entry->name    = trim($user->fullname) ?: $user->username;
        } else {
            $entry->user_id = null;
            $entry->name    = $request->name;
        }

        $entry->amount = $request->amount;
        $entry->status = $request->status ? 1 : 0;
        $entry->save();
    }

    private function validation(Request $request): void
    {
        $this->validate($request, [
            'type'     => 'required|in:user,dummy',
            'username' => 'required_if:type,user|nullable|exists:users,username',
            'name'     => 'required_if:type,dummy|nullable|string|max:120',
            'amount'   => 'required|numeric|min:0',
            'status'   => 'nullable|in:0,1',
        ], [
            'username.exists'      => 'No user found with this username',
            'username.required_if' => 'The username field is required for an existing user',
            'name.required_if'     => 'The name field is required for a dummy entry',
        ]);
    }

    public function status($id)
    {
        return Leaderboard::changeStatus($id);
    }

    public function delete($id)
    {
        $entry = Leaderboard::findOrFail($id);
        $entry->delete();

        $notify[] = ['success', 'Leaderboard entry deleted successfully'];
        return back()->withNotify($notify);
    }
}
