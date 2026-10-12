<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkedinMessage;
use App\Models\LinkedinPost;
use App\Models\LinkedinProspect;
use App\Models\LinkedinReport;
use App\Models\LinkedinSetting;
use App\Services\Linkedin\LinkedinClient;
use App\Services\Linkedin\LinkedinOutreach;
use App\Services\Linkedin\LinkedinPublicSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LinkedinOutreachController extends Controller
{
    public function index()
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        $pageTitle = 'LinkedIn Outreach';
        $settings = LinkedinSetting::current();
        app(LinkedinOutreach::class)->moveApprovedToSent();
        $report = app(LinkedinOutreach::class)->snapshot(now());
        $pendingMessages = LinkedinMessage::where('status', 'pending_approval')->count();
        $readyMessages = LinkedinMessage::where('status', 'approved')->count();
        $pendingPosts = LinkedinPost::whereIn('status', ['pending_approval', 'failed'])->count();
        $queue = LinkedinMessage::with('prospect')
            ->whereIn('status', ['pending_approval', 'approved'])
            ->orderBy('id')
            ->limit(20)
            ->get();
        $redirectUrl = route('admin.linkedin.callback');

        return view('admin.linkedin.index', compact(
            'pageTitle',
            'settings',
            'report',
            'pendingMessages',
            'readyMessages',
            'pendingPosts',
            'queue',
            'redirectUrl'
        ));
    }

    public function settings(Request $request)
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        $request->validate([
            'daily_connection_limit' => 'required|integer|min:1|max:20',
            'client_id'              => 'nullable|string|max:255',
            'client_secret'          => 'nullable|string|max:500',
            'audience_titles'        => 'nullable|string|max:500',
        ]);

        $settings = LinkedinSetting::current();
        $wasApprovalRequired = $settings->require_approval;

        $settings->enabled = $request->boolean('enabled');
        $settings->require_approval = $request->boolean('require_approval');
        $settings->posting_enabled = $request->boolean('posting_enabled');
        $settings->daily_connection_limit = (int) $request->daily_connection_limit;
        $settings->post_interval_days = 3;
        $settings->client_id = trim((string) $request->client_id) ?: null;
        $settings->audience_titles = trim((string) $request->audience_titles) ?: null;
        if ($request->filled('client_secret')) {
            $settings->client_secret = $request->client_secret;
        }
        $settings->save();

        if ($wasApprovalRequired && !$settings->require_approval && $settings->enabled) {
            $outreach = app(LinkedinOutreach::class);
            $outreach->releaseOpenQueue();
            $outreach->releasePosts();
        }

        $notify[] = ['success', 'LinkedIn settings saved.'];
        return back()->withNotify($notify);
    }

    public function connect(LinkedinClient $client)
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        try {
            $state = bin2hex(random_bytes(20));
            session(['linkedin_oauth_state' => $state]);
            return redirect()->away($client->authorizationUrl(LinkedinSetting::current(), $state));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function callback(Request $request, LinkedinClient $client)
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        if ($request->filled('error')) {
            $notify[] = ['error', (string) $request->query('error_description', 'LinkedIn authorization was cancelled.')];
            return redirect()->route('admin.linkedin.index')->withNotify($notify);
        }

        $expected = (string) session()->pull('linkedin_oauth_state');
        $given = (string) $request->query('state');
        if ($expected === '' || !hash_equals($expected, $given)) {
            $notify[] = ['error', 'The LinkedIn connection attempt expired. Click Connect again.'];
            return redirect()->route('admin.linkedin.index')->withNotify($notify);
        }

        try {
            $client->exchangeCode(LinkedinSetting::current(), (string) $request->query('code'));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return redirect()->route('admin.linkedin.index')->withNotify($notify);
        }

        $notify[] = ['success', 'LinkedIn account connected.'];
        return redirect()->route('admin.linkedin.index')->withNotify($notify);
    }

    public function disconnect()
    {
        $settings = LinkedinSetting::current();
        $settings->access_token = null;
        $settings->refresh_token = null;
        $settings->token_expires_at = null;
        $settings->member_id = null;
        $settings->member_name = null;
        $settings->member_email = null;
        $settings->connected_at = null;
        $settings->save();

        $notify[] = ['success', 'LinkedIn account disconnected. Prepared messages stay in the portal.'];
        return back()->withNotify($notify);
    }

    public function runDaily()
    {
        $message = app(LinkedinOutreach::class)->run();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function prospects(Request $request)
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        $pageTitle = 'LinkedIn Prospects';
        app(LinkedinOutreach::class)->moveApprovedToSent();
        $status = (string) $request->query('status', '');
        $search = trim((string) $request->query('search', ''));

        $prospects = LinkedinProspect::query()
            ->when($status !== '', fn ($query) => $query->where('outreach_status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(getPaginate())
            ->withQueryString();

        return view('admin.linkedin.prospects', compact('pageTitle', 'prospects', 'status', 'search'));
    }

    public function prospectStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:120',
            'company'     => 'required|string|max:160',
            'title'       => 'nullable|string|max:160',
            'profile_url' => 'required|string|max:255',
            'location'    => 'nullable|string|max:120',
            'public_note' => 'nullable|string|max:1000',
        ]);

        try {
            $url = LinkedinProspect::normalizeProfileUrl($request->profile_url);
        } catch (\InvalidArgumentException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify)->withInput();
        }

        if (LinkedinProspect::where('profile_url', $url)->exists()) {
            $notify[] = ['error', 'That LinkedIn profile is already on the list.'];
            return back()->withNotify($notify)->withInput();
        }

        LinkedinProspect::create([
            'name'        => $request->name,
            'company'     => $request->company,
            'title'       => $request->title,
            'profile_url' => $url,
            'location'    => $request->location,
            'public_note' => $request->public_note,
        ]);
        app(LinkedinPublicSearch::class)->remember($url);

        $notify[] = ['success', 'Prospect added. The daily job will review the role and prepare a note.'];
        return back()->withNotify($notify);
    }

    public function prospectImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if ($handle === false) {
            $notify[] = ['error', 'The file could not be read.'];
            return back()->withNotify($notify);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $notify[] = ['error', 'The file is empty.'];
            return back()->withNotify($notify);
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $map = [];
        foreach ($header as $index => $column) {
            $map[strtolower(trim((string) $column))] = $index;
        }
        foreach (['name', 'company', 'profile_url'] as $required) {
            if (!array_key_exists($required, $map)) {
                fclose($handle);
                $notify[] = ['error', 'The CSV needs columns: name, company, title, profile_url, location, public_note.'];
                return back()->withNotify($notify);
            }
        }

        $imported = 0;
        $skipped = 0;
        $rowNumber = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if ($rowNumber > 100) {
                $skipped++;
                continue;
            }
            $name = $this->csvColumn($row, $map, 'name');
            $company = $this->csvColumn($row, $map, 'company');
            $urlRaw = $this->csvColumn($row, $map, 'profile_url');
            if ($name === '' || $company === '' || $urlRaw === '') {
                $skipped++;
                continue;
            }
            try {
                $url = LinkedinProspect::normalizeProfileUrl($urlRaw);
            } catch (\InvalidArgumentException $exception) {
                $skipped++;
                continue;
            }
            if (LinkedinProspect::where('profile_url', $url)->exists()) {
                $skipped++;
                continue;
            }

            $title = $this->csvColumn($row, $map, 'title');
            LinkedinProspect::create([
                'name'        => mb_substr($name, 0, 120),
                'company'     => mb_substr($company, 0, 160),
                'title'       => $title !== '' ? mb_substr($title, 0, 160) : null,
                'profile_url' => $url,
                'location'    => mb_substr($this->csvColumn($row, $map, 'location'), 0, 120) ?: null,
                'public_note' => mb_substr($this->csvColumn($row, $map, 'public_note'), 0, 1000) ?: null,
            ]);
            app(LinkedinPublicSearch::class)->remember($url);
            $imported++;
        }
        fclose($handle);

        $notify[] = ['success', "Imported {$imported} prospects. Skipped {$skipped}."];
        return back()->withNotify($notify);
    }

    public function searchMore()
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        @set_time_limit(90);

        try {
            $text = app(LinkedinPublicSearch::class)->searchMore();
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', $text];
        return back()->withNotify($notify);
    }

    public function prospectShow($id)
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        app(LinkedinOutreach::class)->moveApprovedToSent();

        $pageTitle = 'Prospect';
        $prospect = LinkedinProspect::with('messages')->findOrFail($id);
        $prospect->setRelation('messages', $prospect->messages->sortBy('id'));

        return view('admin.linkedin.prospect', compact('pageTitle', 'prospect'));
    }

    public function prospectUpdate(Request $request, $id)
    {
        $prospect = LinkedinProspect::findOrFail($id);
        if (!$prospect->canEditRecord()) {
            $notify[] = ['error', 'This prospect can no longer be edited. Record the response instead.'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'name'        => 'required|string|max:120',
            'company'     => 'required|string|max:160',
            'title'       => 'nullable|string|max:160',
            'profile_url' => 'required|string|max:255',
            'location'    => 'nullable|string|max:120',
            'public_note' => 'nullable|string|max:1000',
        ]);

        try {
            $url = LinkedinProspect::normalizeProfileUrl($request->profile_url);
        } catch (\InvalidArgumentException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $duplicate = LinkedinProspect::where('profile_url', $url)->where('id', '!=', $prospect->id)->exists();
        if ($duplicate) {
            $notify[] = ['error', 'That LinkedIn profile is already on the list.'];
            return back()->withNotify($notify);
        }

        $titleChanged = trim((string) $prospect->title) !== trim((string) $request->title);
        $prospect->fill([
            'name'        => $request->name,
            'company'     => $request->company,
            'title'       => $request->title,
            'profile_url' => $url,
            'location'    => $request->location,
            'public_note' => $request->public_note,
        ]);
        if ($titleChanged && in_array($prospect->outreach_status, ['needs_title', 'not_a_fit'], true)) {
            $prospect->outreach_status = 'new';
            $prospect->fit = 'pending';
            $prospect->researched_at = null;
        }
        $prospect->save();

        $notify[] = ['success', 'Prospect updated.'];
        return back()->withNotify($notify);
    }

    public function prospectDelete($id)
    {
        $prospect = LinkedinProspect::findOrFail($id);
        if (!$prospect->canDelete()) {
            $notify[] = ['error', 'This person stays on the list so they are not contacted again.'];
            return back()->withNotify($notify);
        }

        $prospect->delete();
        $notify[] = ['success', 'Prospect removed.'];
        return redirect()->route('admin.linkedin.prospects')->withNotify($notify);
    }

    public function prospectResponse(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|in:accepted,replied,qualified,declined,opted_out',
        ]);

        try {
            app(LinkedinOutreach::class)->recordResponse(LinkedinProspect::findOrFail($id), $request->response);
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Response saved. Declined and opted-out people are not contacted again.'];
        return back()->withNotify($notify);
    }

    public function prospectInclude($id)
    {
        try {
            app(LinkedinOutreach::class)->includeProspect(LinkedinProspect::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Prospect included. A note is prepared when today’s limit still has room.'];
        return back()->withNotify($notify);
    }

    public function prospectUndo($id)
    {
        try {
            app(LinkedinOutreach::class)->undoStop(LinkedinProspect::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Stop removed. Use this only when the response was recorded by mistake.'];
        return back()->withNotify($notify);
    }

    public function prepareFollowUp($id)
    {
        try {
            app(LinkedinOutreach::class)->prepareFollowUp(LinkedinProspect::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Follow-up prepared.'];
        return back()->withNotify($notify);
    }

    public function messageUpdate(Request $request, $id)
    {
        $message = LinkedinMessage::findOrFail($id);
        if (!$message->isOpen()) {
            $notify[] = ['error', 'Sent messages stay as they were sent.'];
            return back()->withNotify($notify);
        }

        $max = 300;
        $request->validate([
            'body' => 'required|string|max:'.$max,
        ]);

        $message->body = trim($request->body);
        $message->save();

        $notify[] = ['success', 'Message updated.'];
        return back()->withNotify($notify);
    }

    public function messageApprove($id)
    {
        try {
            $text = app(LinkedinOutreach::class)->approveMessage(LinkedinMessage::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', $text];
        return back()->withNotify($notify);
    }

    public function messageReject($id)
    {
        app(LinkedinOutreach::class)->rejectMessage(LinkedinMessage::findOrFail($id));
        $notify[] = ['success', 'Message rejected. It will not be sent.'];
        return back()->withNotify($notify);
    }

    public function messageSent($id)
    {
        try {
            app(LinkedinOutreach::class)->markSent(LinkedinMessage::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Marked as sent.'];
        return back()->withNotify($notify);
    }

    public function posts()
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        $pageTitle = 'LinkedIn Posts';
        $settings = LinkedinSetting::current();
        $openPosts = LinkedinPost::query()->where('status', '!=', 'published')->orderByDesc('id')->get();
        $published = LinkedinPost::query()->where('status', 'published')->orderByDesc('published_at')->paginate(getPaginate());
        $nextDate = app(LinkedinOutreach::class)->nextPostDate();

        return view('admin.linkedin.posts', compact('pageTitle', 'settings', 'openPosts', 'published', 'nextDate'));
    }

    public function postStore(Request $request)
    {
        $request->validate([
            'commentary' => 'required|string|max:3000',
            'image'      => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $post = new LinkedinPost();
        $post->commentary = trim($request->commentary);
        $post->image = $this->storePostImage($request);
        $post->source = 'manual';
        $post->status = 'pending_approval';
        $post->scheduled_for = app(LinkedinOutreach::class)->nextPostDate();
        $post->save();

        if (!LinkedinSetting::current()->require_approval) {
            try {
                $text = app(LinkedinOutreach::class)->approvePost($post);
                $notify[] = ['success', $text];
                return back()->withNotify($notify);
            } catch (RuntimeException $exception) {
                $notify[] = ['error', $exception->getMessage()];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Post saved and waiting for approval.'];
        return back()->withNotify($notify);
    }

    public function postUpdate(Request $request, $id)
    {
        $post = LinkedinPost::findOrFail($id);
        if ($post->status === 'published') {
            $notify[] = ['error', 'Published posts cannot be edited here.'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'commentary' => 'required|string|max:3000',
        ]);
        $post->commentary = trim($request->commentary);
        $post->save();

        $notify[] = ['success', 'Post text updated.'];
        return back()->withNotify($notify);
    }

    public function postImage(Request $request, $id)
    {
        $post = LinkedinPost::findOrFail($id);
        if ($post->status === 'published') {
            $notify[] = ['error', 'Published posts cannot be changed here.'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $post->image = $this->storePostImage($request, $post->image);
        if ($post->status === 'failed') {
            $post->status = 'pending_approval';
            $post->error = null;
        }
        $post->save();

        if (!LinkedinSetting::current()->require_approval) {
            try {
                $text = app(LinkedinOutreach::class)->approvePost($post);
                $notify[] = ['success', $text];
                return back()->withNotify($notify);
            } catch (RuntimeException $exception) {
                $notify[] = ['warning', 'Picture saved. '.$exception->getMessage()];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Picture saved. Approve the post when the text is ready.'];
        return back()->withNotify($notify);
    }

    public function postApprove($id)
    {
        try {
            $text = app(LinkedinOutreach::class)->approvePost(LinkedinPost::findOrFail($id));
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', $text];
        return back()->withNotify($notify);
    }

    public function postReject($id)
    {
        $post = LinkedinPost::findOrFail($id);
        if ($post->status === 'published') {
            $notify[] = ['error', 'A published post cannot be rejected here.'];
            return back()->withNotify($notify);
        }
        $post->status = 'rejected';
        $post->save();

        $notify[] = ['success', 'Post rejected. It will not be published.'];
        return back()->withNotify($notify);
    }

    public function postRetry($id)
    {
        $post = LinkedinPost::findOrFail($id);
        $post->status = 'approved';
        $post->error = null;
        $post->save();

        try {
            app(LinkedinOutreach::class)->publishDuePost();
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $post->refresh();
        if ($post->status === 'published') {
            $notify[] = ['success', 'Post published.'];
        } else {
            $notify[] = ['success', 'Post is approved and will publish when the 3-day gap has passed.'];
        }
        return back()->withNotify($notify);
    }

    public function report()
    {
        if ($missing = $this->missingTables()) {
            return $missing;
        }

        $pageTitle = 'LinkedIn Daily Report';
        app(LinkedinOutreach::class)->snapshot(now());
        $reports = LinkedinReport::query()->orderByDesc('report_date')->paginate(getPaginate());

        return view('admin.linkedin.report', compact('pageTitle', 'reports'));
    }

    private function csvColumn(array $row, array $map, string $key): string
    {
        if (!array_key_exists($key, $map)) {
            return '';
        }

        return trim((string) ($row[$map[$key]] ?? ''));
    }

    private function storePostImage(Request $request, ?string $old = null): string
    {
        $directory = 'assets/images/linkedin';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $oldName = $old ? basename($old) : null;
        $filename = fileUploader($request->file('image'), $directory, null, $oldName);

        return $directory.'/'.$filename;
    }

    private function missingTables()
    {
        if (Schema::hasTable('linkedin_settings')) {
            return null;
        }

        $pageTitle = 'LinkedIn Outreach';
        return view('admin.linkedin.missing', compact('pageTitle'));
    }
}
