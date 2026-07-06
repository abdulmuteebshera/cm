<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Job Opportunities';
        $jobPosts  = JobPost::withCount('applications')->orderByDesc('id')->paginate(getPaginate());

        return view('admin.job_post.index', compact('pageTitle', 'jobPosts'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $jobPost = new JobPost();
        $this->submitData($jobPost, $request);

        $notify[] = ['success', 'Job opportunity created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);

        $jobPost = JobPost::findOrFail($id);
        $this->submitData($jobPost, $request);

        $notify[] = ['success', 'Job opportunity updated successfully'];
        return back()->withNotify($notify);
    }

    private function submitData(JobPost $jobPost, Request $request): void
    {
        $jobPost->title            = $request->title;
        $jobPost->department       = $request->department;
        $jobPost->location         = $request->location;
        $jobPost->employment_type  = $request->employment_type;
        $jobPost->summary          = $request->summary;
        $jobPost->description      = $request->description;
        $jobPost->requirements     = $request->requirements;
        $jobPost->status           = $request->status ? 1 : 0;
        $jobPost->save();
    }

    private function validation(Request $request): void
    {
        $this->validate($request, [
            'title'           => 'required|string|max:255',
            'department'      => 'nullable|string|max:120',
            'location'        => 'nullable|string|max:120',
            'employment_type' => 'nullable|string|max:80',
            'summary'         => 'nullable|string|max:500',
            'description'     => 'required|string',
            'requirements'    => 'nullable|string',
            'status'          => 'nullable|in:0,1',
        ]);
    }

    public function status($id)
    {
        return JobPost::changeStatus($id);
    }

    public function delete($id)
    {
        $jobPost = JobPost::findOrFail($id);
        $jobPost->delete();

        $notify[] = ['success', 'Job opportunity deleted successfully'];
        return back()->withNotify($notify);
    }
}
