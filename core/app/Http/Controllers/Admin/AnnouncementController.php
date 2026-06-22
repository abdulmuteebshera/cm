<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $pageTitle     = 'Manage Announcements';
        $announcements = Announcement::orderByDesc('id')->paginate(getPaginate());
        return view('admin.announcement.index', compact('pageTitle', 'announcements'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $announcement = new Announcement();
        $this->submitData($announcement, $request);

        $notify[] = ['success', 'Announcement created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);

        $announcement = Announcement::findOrFail($id);
        $this->submitData($announcement, $request);

        $notify[] = ['success', 'Announcement updated successfully'];
        return back()->withNotify($notify);
    }

    private function submitData(Announcement $announcement, Request $request): void
    {
        $announcement->title   = $request->title;
        $announcement->content = $request->content;
        $announcement->status  = $request->status ? 1 : 0;
        $announcement->save();
    }

    private function validation(Request $request): void
    {
        $this->validate($request, [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'nullable|in:0,1',
        ]);
    }

    public function status($id)
    {
        return Announcement::changeStatus($id);
    }

    public function delete($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        $notify[] = ['success', 'Announcement deleted successfully'];
        return back()->withNotify($notify);
    }
}
