<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcTemplate;
use App\Support\EmailCampaign\EcTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function index()
    {
        $pageTitle = 'Email Templates';
        $templates = EcTemplate::query()->latest('id')->paginate(20);

        return view('emailcampaign.admin.templates.index', compact('pageTitle', 'templates'));
    }

    public function preview(int $id)
    {
        $template = EcTemplate::query()->findOrFail($id);
        $pageTitle = 'Preview: ' . $template->name;

        $sample = EcTemplateRenderer::varsForRecipient(
            'Jonathan A. Mercer',
            'j.mercer@example.com',
            ['company' => 'Mercer Family Office']
        );

        $renderedSubject = EcTemplateRenderer::render($template->subject, $sample);
        $renderedText    = $template->body_text
            ? EcTemplateRenderer::render($template->body_text, $sample)
            : null;

        return view('emailcampaign.admin.templates.preview', compact(
            'pageTitle',
            'template',
            'renderedSubject',
            'renderedText',
            'sample'
        ));
    }

    public function previewFrame(int $id)
    {
        $template = EcTemplate::query()->findOrFail($id);

        $sample = EcTemplateRenderer::varsForRecipient(
            'Jonathan A. Mercer',
            'j.mercer@example.com',
            ['company' => 'Mercer Family Office']
        );

        $html = EcTemplateRenderer::render($template->body_html, $sample);

        return response($html, 200, [
            'Content-Type'  => 'text/html; charset=UTF-8',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'subject'   => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
        ]);

        $template = EcTemplate::query()->create($data + ['status' => 1]);

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'template.created',
            'Created template: ' . $template->name,
            'template',
            $template->id
        );

        return back()->withNotify([['success', 'Template created.']]);
    }

    public function update(Request $request, int $id)
    {
        $template = EcTemplate::query()->findOrFail($id);

        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'subject'   => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'status'    => 'nullable|in:0,1',
        ]);

        $template->fill($data);
        if ($request->has('status')) {
            $template->status = (int) $request->status;
        }
        $template->save();

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'template.updated',
            'Updated template: ' . $template->name,
            'template',
            $template->id
        );

        return back()->withNotify([['success', 'Template updated.']]);
    }

    public function destroy(int $id)
    {
        $template = EcTemplate::query()->findOrFail($id);
        if ($template->campaigns()->exists()) {
            return back()->withNotify([['error', 'Template is in use by campaigns and cannot be deleted.']]);
        }

        $name = $template->name;
        $template->delete();

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'template.deleted',
            'Deleted template: ' . $name,
            'template',
            $id
        );

        return back()->withNotify([['success', 'Template removed.']]);
    }
}
