<?php

namespace App\Http\Controllers\EmailCampaign\User;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcCampaign;
use App\Models\EmailCampaign\EcCampaignRecipient;
use App\Models\EmailCampaign\EcGroup;
use App\Models\EmailCampaign\EcGroupMember;
use App\Models\EmailCampaign\EcTemplate;
use App\Support\EmailCampaign\EcGroupAccess;
use App\Support\EmailCampaign\EcRecipientImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index()
    {
        $user = Auth::guard('ec_user')->user();
        $pageTitle = 'My Campaigns';
        $campaigns = EcCampaign::query()->where('ec_user_id', $user->id)->with('template')->latest('id')->paginate(20);

        return view('emailcampaign.user.campaigns.index', compact('pageTitle', 'campaigns'));
    }

    public function create()
    {
        $user = Auth::guard('ec_user')->user();
        $pageTitle = 'New Campaign';
        $templates = EcTemplate::query()->where('status', 1)->orderBy('name')->get();
        $groups = EcGroupAccess::queryForUser($user->id)->withCount('members')->orderBy('name')->get();

        $templatesJson = $templates->map(static function ($t) {
            return [
                'id'        => $t->id,
                'subject'   => $t->subject,
                'body_html' => $t->body_html,
                'body_text' => $t->body_text,
            ];
        })->values();

        return view('emailcampaign.user.campaigns.create', compact('pageTitle', 'templates', 'groups', 'templatesJson'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('ec_user')->user();

        $data = $request->validate([
            'name'            => 'required|string|max:120',
            'ec_template_id'  => 'required|exists:ec_templates,id',
            'ec_group_id'     => 'nullable|exists:ec_groups,id',
            'subject'         => 'required|string|max:255',
            'body_html'       => 'required|string',
            'body_text'       => 'nullable|string',
        ]);

        if (!empty($data['ec_group_id'])) {
            if (!EcGroupAccess::userCanUse($user->id, (int) $data['ec_group_id'])) {
                return back()->withNotify([['error', 'Invalid email group.']])->withInput();
            }
        }

        $campaign = EcCampaign::query()->create([
            'ec_user_id'         => $user->id,
            'ec_template_id'     => $data['ec_template_id'],
            'ec_group_id'        => $data['ec_group_id'] ?? null,
            'name'               => $data['name'],
            'subject'            => $data['subject'],
            'body_html'          => $data['body_html'],
            'body_text'          => $data['body_text'] ?? null,
            'status'             => 'draft',
            'send_delay_seconds' => 10,
        ]);

        if ($campaign->ec_group_id) {
            $this->syncRecipientsFromGroup($campaign);
        }

        EcActivityLog::record('user', $user->id, 'campaign.created', 'Created campaign: ' . $campaign->name, 'campaign', $campaign->id);

        return redirect()->route('ec.user.campaigns.show', $campaign->id)->withNotify([['success', 'Campaign created. Add recipients or import a list.']]);
    }

    public function show(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->with(['template', 'group'])->findOrFail($id);
        $pageTitle = $campaign->name;

        $remaining = $campaign->remainingCount();
        $etaSeconds = $campaign->estimatedSecondsRemaining();
        $recipients = EcCampaignRecipient::query()->where('ec_campaign_id', $campaign->id)->latest('id')->paginate(30);

        return view('emailcampaign.user.campaigns.show', compact('pageTitle', 'campaign', 'remaining', 'etaSeconds', 'recipients'));
    }

    public function updateContent(Request $request, int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);

        if (in_array($campaign->status, ['running', 'completed'], true)) {
            return back()->withNotify([['error', 'Pause the campaign before editing content.']]);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'subject'   => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
        ]);

        $campaign->fill($data);
        $campaign->save();

        EcActivityLog::record('user', $user->id, 'campaign.content_updated', 'Updated content for: ' . $campaign->name, 'campaign', $campaign->id);

        return back()->withNotify([['success', 'Campaign content saved.']]);
    }

    public function loadTemplate(Request $request, int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);

        $data = $request->validate([
            'ec_template_id' => 'required|exists:ec_templates,id',
        ]);

        $template = EcTemplate::query()->where('status', 1)->findOrFail($data['ec_template_id']);

        $campaign->ec_template_id = $template->id;
        $campaign->subject = $template->subject;
        $campaign->body_html = $template->body_html;
        $campaign->body_text = $template->body_text;
        $campaign->save();

        return back()->withNotify([['success', 'Loaded template into this campaign (you can edit content here).']]);
    }

    public function addRecipient(Request $request, int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = $this->editableCampaign($user->id, $id);

        $data = $request->validate([
            'email' => 'required|email',
            'name'  => 'nullable|string|max:120',
        ]);

        EcCampaignRecipient::query()->firstOrCreate(
            ['ec_campaign_id' => $campaign->id, 'email' => strtolower($data['email'])],
            ['name' => $data['name'] ?? null, 'status' => 'pending']
        );

        $campaign->syncTotals();

        return back()->withNotify([['success', 'Recipient added.']]);
    }

    public function importRecipients(Request $request, int $id, EcRecipientImport $import)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = $this->editableCampaign($user->id, $id);

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
            EcCampaignRecipient::query()->firstOrCreate(
                ['ec_campaign_id' => $campaign->id, 'email' => $row['email']],
                ['name' => $row['name'], 'merge_data' => $row['merge_data'], 'status' => 'pending']
            );
            $added++;
        }

        $campaign->syncTotals();

        EcActivityLog::record('user', $user->id, 'campaign.import', "Imported {$added} recipients to {$campaign->name}", 'campaign', $campaign->id);

        return back()->withNotify([['success', "Imported {$added} rows."]]);
    }

    public function pullFromGroup(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = $this->editableCampaign($user->id, $id);

        if (!$campaign->ec_group_id) {
            return back()->withNotify([['error', 'No group linked to this campaign.']]);
        }

        $this->syncRecipientsFromGroup($campaign);

        return back()->withNotify([['success', 'Recipients synced from linked group.']]);
    }

    public function start(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);

        if ($campaign->status === 'running') {
            return back()->withNotify([['error', 'Campaign is already running.']]);
        }

        if ($campaign->status === 'completed') {
            return back()->withNotify([['error', 'Campaign is completed. Duplicate the campaign to send again.']]);
        }

        $pending = $campaign->remainingCount();
        if ($pending === 0) {
            return back()->withNotify([['error', 'Add recipients before starting.']]);
        }

        $campaign->status = 'running';
        if (!$campaign->started_at) {
            $campaign->started_at = now();
        }
        $campaign->paused_at = null;
        $campaign->next_send_at = now();
        $campaign->save();

        EcActivityLog::record('user', $user->id, 'campaign.started', 'Started campaign: ' . $campaign->name, 'campaign', $campaign->id);

        return back()->withNotify([['success', 'Campaign started. Emails send every 10 seconds from the shared SMTP account.']]);
    }

    public function pause(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);

        if ($campaign->status !== 'running') {
            return back()->withNotify([['error', 'Only running campaigns can be paused.']]);
        }

        $campaign->status = 'paused';
        $campaign->paused_at = now();
        $campaign->save();

        EcActivityLog::record('user', $user->id, 'campaign.paused', 'Paused campaign: ' . $campaign->name, 'campaign', $campaign->id);

        return back()->withNotify([['success', 'Campaign paused. Resume anytime to continue from the same place.']]);
    }

    public function resume(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);

        if ($campaign->status !== 'paused') {
            return back()->withNotify([['error', 'Campaign is not paused.']]);
        }

        if ($campaign->remainingCount() === 0) {
            $campaign->status = 'completed';
            $campaign->completed_at = now();
            $campaign->save();

            return back()->withNotify([['success', 'No pending recipients — campaign marked completed.']]);
        }

        $campaign->status = 'running';
        $campaign->next_send_at = now();
        $campaign->save();

        EcActivityLog::record('user', $user->id, 'campaign.resumed', 'Resumed campaign: ' . $campaign->name, 'campaign', $campaign->id);

        return back()->withNotify([['success', 'Campaign resumed.']]);
    }

    public function statusJson(int $id)
    {
        $user = Auth::guard('ec_user')->user();
        $campaign = EcCampaign::query()->where('ec_user_id', $user->id)->findOrFail($id);
        $campaign->syncTotals();

        $remaining = $campaign->remainingCount();
        $etaSeconds = $campaign->estimatedSecondsRemaining();

        return response()->json([
            'status'          => $campaign->status,
            'sent'            => (int) $campaign->sent_count,
            'failed'          => (int) $campaign->failed_count,
            'remaining'       => $remaining,
            'total'           => (int) $campaign->total_recipients,
            'eta_seconds'     => $etaSeconds,
            'eta_human'       => $this->formatDuration($etaSeconds),
            'next_send_at'    => optional($campaign->next_send_at)->toIso8601String(),
        ]);
    }

    protected function editableCampaign(int $userId, int $campaignId): EcCampaign
    {
        $campaign = EcCampaign::query()->where('ec_user_id', $userId)->findOrFail($campaignId);
        if ($campaign->status === 'running') {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                redirect()->back()->withNotify([['error', 'Pause the campaign before changing recipients.']])
            );
        }

        return $campaign;
    }

    protected function syncRecipientsFromGroup(EcCampaign $campaign): void
    {
        $members = EcGroupMember::query()->where('ec_group_id', $campaign->ec_group_id)->get();
        foreach ($members as $member) {
            EcCampaignRecipient::query()->firstOrCreate(
                ['ec_campaign_id' => $campaign->id, 'email' => $member->email],
                [
                    'name'        => $member->name,
                    'merge_data'  => $member->merge_data,
                    'status'      => 'pending',
                ]
            );
        }
        $campaign->syncTotals();
    }

    protected function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0m';
        }
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;
        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }
        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }

        return sprintf('%ds', $secs);
    }
}
