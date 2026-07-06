<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Job Applications';
        $jobPosts  = JobPost::orderByDesc('id')->get(['id', 'title']);
        $jobId     = $request->integer('job_id') ?: null;

        $applications = JobApplication::with('jobPost')
            ->when($jobId, fn ($query) => $query->where('job_post_id', $jobId))
            ->orderByDesc('id')
            ->paginate(getPaginate());

        return view('admin.job_application.index', compact('pageTitle', 'applications', 'jobPosts', 'jobId'));
    }

    public function detail($id)
    {
        $application = JobApplication::with('jobPost')->findOrFail($id);
        $pageTitle   = 'Application — ' . $application->name;

        return view('admin.job_application.detail', compact('pageTitle', 'application'));
    }

    public function update(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        $request->validate([
            'application_status' => 'required|integer|in:0,1,2,3',
            'admin_notes'        => 'nullable|string|max:5000',
        ]);

        $application->application_status = (int) $request->application_status;
        $application->admin_notes        = $request->admin_notes;
        $application->save();

        $notify[] = ['success', 'Application updated successfully'];
        return back()->withNotify($notify);
    }

    public function resume($id)
    {
        $application = JobApplication::findOrFail($id);
        $path        = $application->resumePath();

        if (!File::exists($path)) {
            abort(404);
        }

        $filename = $application->resume_original_name ?: basename($application->resume);
        $mimetype = mime_content_type($path) ?: 'application/octet-stream';

        return response()->download($path, $filename, [
            'Content-Type' => $mimetype,
        ]);
    }

    public function delete($id)
    {
        $application = JobApplication::findOrFail($id);
        $path        = $application->resumePath();

        if (File::exists($path)) {
            File::delete($path);
        }

        $application->delete();

        $notify[] = ['success', 'Application deleted successfully'];
        return back()->withNotify($notify);
    }
}
