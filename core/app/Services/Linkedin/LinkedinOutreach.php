<?php

namespace App\Services\Linkedin;

use App\Models\LinkedinMessage;
use App\Models\LinkedinPost;
use App\Models\LinkedinProspect;
use App\Models\LinkedinReport;
use App\Models\LinkedinSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LinkedinOutreach
{
    public function __construct(private LinkedinClient $client)
    {
    }

    public function runIfDue(): void
    {
        if (!Schema::hasTable('linkedin_settings')) {
            return;
        }

        $settings = LinkedinSetting::current();
        if (!$settings->enabled) {
            return;
        }
        if ($settings->last_daily_run_at && $settings->last_daily_run_at->isToday()) {
            return;
        }

        $this->run();
    }

    public function run(): string
    {
        if (!Schema::hasTable('linkedin_settings')) {
            return 'LinkedIn tables are not installed.';
        }

        $settings = LinkedinSetting::current();
        if (!$settings->enabled) {
            return 'Turn on daily automation before running it.';
        }

        $this->reviewProspects();
        $this->draftFollowUps();
        $this->preparePost();

        $settings = $settings->fresh();
        if (!$settings->require_approval) {
            $this->releaseOpenQueue();
            $this->releasePosts();
        }

        try {
            $this->publishDuePost();
        } catch (RuntimeException $exception) {
            LinkedinSetting::query()->whereKey($settings->id)->update([
                'last_error' => mb_substr($exception->getMessage(), 0, 500),
            ]);
        }

        LinkedinSetting::query()->whereKey($settings->id)->update([
            'last_daily_run_at' => now(),
        ]);
        $this->snapshot(now());

        return 'Today’s LinkedIn queue is ready.';
    }

    public function reviewProspects(): int
    {
        $settings = LinkedinSetting::current();
        $drafted = 0;
        $slots = $this->connectionSlots();

        $prospects = LinkedinProspect::query()
            ->where('do_not_contact', false)
            ->where(function ($query) {
                $query->where('outreach_status', 'new')
                    ->orWhere(function ($query) {
                        $query->where('outreach_status', 'redraft')
                            ->whereDate('redraft_on', '<=', today());
                    });
            })
            ->whereDoesntHave('messages', function ($query) {
                $query->where('kind', 'connection')->whereIn('status', ['pending_approval', 'approved', 'sent']);
            })
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->sortByDesc(fn (LinkedinProspect $prospect) => $this->clientScore($prospect))
            ->values();

        foreach ($prospects as $prospect) {
            if ($prospect->researched_at === null || $prospect->outreach_status === 'redraft') {
                $prospect->researched_at = now();
            }

            if ($prospect->fit !== 'fit') {
                $title = trim((string) $prospect->title);
                if ($title === '') {
                    $prospect->fit = 'needs_title';
                    $prospect->outreach_status = 'needs_title';
                    $prospect->save();
                    continue;
                }
                if (!$this->matchesAudience($title, $settings)) {
                    $prospect->fit = 'not_a_fit';
                    $prospect->outreach_status = 'not_a_fit';
                    $prospect->save();
                    continue;
                }
                if ($this->clientScore($prospect) < 20) {
                    $prospect->fit = 'too_small';
                    $prospect->outreach_status = 'too_small';
                    $prospect->save();
                    continue;
                }
            }

            if ($slots < 1) {
                $prospect->fit = 'fit';
                $prospect->save();
                continue;
            }

            $this->createConnectionDraft($prospect, $settings);
            $drafted++;
            $slots--;
        }

        return $drafted;
    }

    public function draftFollowUps(): int
    {
        $settings = LinkedinSetting::current();
        $made = 0;
        $room = $this->followUpSlots();

        $prospects = LinkedinProspect::query()
            ->where('do_not_contact', false)
            ->where('outreach_status', 'accepted')
            ->where('follow_up_status', 'none')
            ->whereDoesntHave('messages', function ($query) {
                $query->where('kind', 'follow_up')->whereIn('status', ['pending_approval', 'approved', 'sent']);
            })
            ->orderBy('id')
            ->limit($room)
            ->get();

        foreach ($prospects as $prospect) {
            if ($room < 1) {
                break;
            }
            $this->createFollowUpDraft($prospect, $settings);
            $made++;
            $room--;
        }

        return $made;
    }

    public function prepareFollowUp(LinkedinProspect $prospect): LinkedinMessage
    {
        if ($prospect->do_not_contact) {
            throw new RuntimeException('Outreach is stopped for this person.');
        }
        if (!in_array($prospect->outreach_status, ['accepted', 'replied'], true)) {
            throw new RuntimeException('A follow-up is only prepared after they accept the connection.');
        }
        $open = $prospect->messages()->where('kind', 'follow_up')->whereIn('status', ['pending_approval', 'approved'])->exists();
        if ($open) {
            throw new RuntimeException('A follow-up is already waiting.');
        }
        if ($this->followUpSlots() < 1) {
            throw new RuntimeException('The daily follow-up limit of '.$this->dailyLimit().' is already filled.');
        }

        return $this->createFollowUpDraft($prospect, LinkedinSetting::current());
    }

    public function approveMessage(LinkedinMessage $message): string
    {
        $message->load('prospect');
        $prospect = $message->prospect;
        if (!$prospect || $prospect->do_not_contact) {
            throw new RuntimeException('Outreach is stopped for this person.');
        }
        if ($message->status === 'sent') {
            throw new RuntimeException('This message was already marked sent.');
        }
        if ($message->kind === 'connection' && $this->connectionSlots(true) < 1 && $message->status !== 'approved') {
            throw new RuntimeException('Today’s limit of '.$this->dailyLimit().' connection requests is already filled.');
        }
        if ($message->kind === 'follow_up' && $this->followUpSlots(true) < 1 && $message->status !== 'approved') {
            throw new RuntimeException('Today’s follow-up limit of '.$this->dailyLimit().' is already filled.');
        }

        $message->status = 'approved';
        $message->approved_at = $message->approved_at ?: now();
        $message->save();

        if ($message->kind === 'connection') {
            $prospect->outreach_status = 'approved';
        } else {
            $prospect->follow_up_status = 'approved';
        }
        $prospect->save();

        $this->markSent($message->fresh(['prospect']));

        return 'Approved and marked as sent. Open the profile to send the note on LinkedIn.';
    }

    public function moveApprovedToSent(): void
    {
        $messages = LinkedinMessage::query()->with('prospect')->where('status', 'approved')->get();
        foreach ($messages as $message) {
            $message->status = 'sent';
            $message->sent_at = $message->sent_at ?: now();
            $message->approved_at = $message->approved_at ?: now();
            $message->save();

            $prospect = $message->prospect;
            if (!$prospect) {
                continue;
            }
            if ($message->kind === 'connection' && in_array($prospect->outreach_status, ['approved', 'pending_approval'], true)) {
                $prospect->outreach_status = 'sent';
                $prospect->outreach_date = $prospect->outreach_date ?: today();
                $prospect->save();
            }
            if ($message->kind === 'follow_up' && in_array($prospect->follow_up_status, ['approved', 'pending_approval'], true)) {
                $prospect->follow_up_status = 'sent';
                $prospect->save();
            }
        }
    }

    public function rejectMessage(LinkedinMessage $message): void
    {
        $message->load('prospect');
        $message->status = 'rejected';
        $message->save();

        $prospect = $message->prospect;
        if (!$prospect || $prospect->do_not_contact) {
            return;
        }

        if ($message->kind === 'connection' && in_array($prospect->outreach_status, ['pending_approval', 'approved'], true)) {
            $prospect->outreach_status = 'redraft';
            $prospect->redraft_on = today()->addDay();
            $prospect->save();
        }

        if ($message->kind === 'follow_up') {
            $prospect->follow_up_status = 'skipped';
            $prospect->save();
        }
    }

    public function markSent(LinkedinMessage $message): void
    {
        $message->load('prospect');
        $prospect = $message->prospect;
        if (!$prospect || $prospect->do_not_contact) {
            throw new RuntimeException('Outreach is stopped for this person.');
        }
        if ($message->status !== 'approved') {
            throw new RuntimeException('Approve the message before marking it sent.');
        }
        if ($message->kind === 'connection') {
            $sentToday = LinkedinMessage::query()->where('kind', 'connection')->whereDate('sent_at', today())->count();
            $limit = $this->dailyLimit();
            if ($sentToday >= $limit) {
                throw new RuntimeException($limit.' connection requests are already marked sent today. Send the rest tomorrow.');
            }
        }
        if ($message->kind === 'follow_up') {
            $sentToday = LinkedinMessage::query()->where('kind', 'follow_up')->whereDate('sent_at', today())->count();
            if ($sentToday >= $this->dailyLimit()) {
                throw new RuntimeException($this->dailyLimit().' follow-ups are already marked sent today.');
            }
        }

        $message->status = 'sent';
        $message->sent_at = now();
        $message->save();

        if ($message->kind === 'connection') {
            $prospect->outreach_status = 'sent';
            $prospect->outreach_date = today();
        } else {
            $prospect->follow_up_status = 'sent';
        }
        $prospect->save();
        $this->snapshot(now());
    }

    public function recordResponse(LinkedinProspect $prospect, string $response): void
    {
        if (!in_array($response, ['accepted', 'replied', 'qualified', 'declined', 'opted_out'], true)) {
            throw new RuntimeException('Choose a response.');
        }
        if (in_array($prospect->stop_reason, ['declined', 'opted_out'], true)) {
            throw new RuntimeException('This person declined or opted out. Outreach stays stopped.');
        }

        if (in_array($response, ['declined', 'opted_out'], true)) {
            $this->stop($prospect, $response);
            $this->snapshot(now());
            return;
        }

        if ($response === 'qualified') {
            $this->cancelOpenMessages($prospect);
            $prospect->response = 'qualified';
            $prospect->response_at = now();
            $prospect->qualified_at = now();
            $prospect->outreach_status = 'qualified';
            $prospect->follow_up_status = 'stopped';
            $prospect->do_not_contact = true;
            $prospect->stop_reason = 'qualified';
            $prospect->save();
            $this->snapshot(now());
            return;
        }

        $prospect->response = $response;
        $prospect->response_at = now();
        $prospect->outreach_status = $response;
        if ($response === 'replied') {
            $prospect->follow_up_status = $prospect->follow_up_status === 'sent' ? 'sent' : 'hold';
        }
        if ($response === 'accepted' && $prospect->follow_up_status === 'hold') {
            $prospect->follow_up_status = 'none';
        }
        $prospect->save();

        if ($response === 'accepted') {
            $this->draftFollowUps();
        }
        $this->snapshot(now());
    }

    public function undoStop(LinkedinProspect $prospect): void
    {
        if (!in_array($prospect->stop_reason, ['declined', 'opted_out', 'qualified'], true)) {
            throw new RuntimeException('This record is not stopped.');
        }

        $prospect->do_not_contact = false;
        $prospect->stop_reason = null;
        $prospect->response = 'none';
        $prospect->qualified_at = null;
        $prospect->outreach_status = $prospect->outreach_date ? 'sent' : 'new';
        $prospect->follow_up_status = 'none';
        $prospect->save();
        $this->snapshot(now());
    }

    public function includeProspect(LinkedinProspect $prospect): void
    {
        if ($prospect->do_not_contact) {
            throw new RuntimeException('Outreach is stopped for this person.');
        }

        $prospect->fit = 'fit';
        $prospect->outreach_status = 'new';
        $prospect->researched_at = null;
        $prospect->save();
        $this->reviewProspects();
    }

    public function preparePost(): void
    {
        $settings = LinkedinSetting::current();
        if (!$settings->posting_enabled) {
            return;
        }

        $open = LinkedinPost::query()->whereIn('status', ['pending_approval', 'approved', 'failed'])->exists();
        if ($open || !$this->postIntervalOpen()) {
            $this->refreshLegacyDraft($settings);
            return;
        }

        LinkedinPost::query()->create([
            'commentary'    => $this->composePost((int) $settings->post_template_index),
            'status'        => 'pending_approval',
            'source'        => 'scheduled',
            'scheduled_for' => $this->nextPostDate(),
        ]);

        $settings->post_template_index = (int) $settings->post_template_index + 1;
        $settings->save();
    }

    public function approvePost(LinkedinPost $post): string
    {
        if (!$post->image) {
            throw new RuntimeException('Add a picture before approving this post.');
        }
        if (in_array($post->status, ['published', 'rejected'], true)) {
            throw new RuntimeException('This post can no longer be approved.');
        }

        $post->status = 'approved';
        $post->error = null;
        $post->scheduled_for = $this->nextPostDate();
        $post->save();

        if ($this->postIntervalOpen() && $post->scheduled_for->lte(today())) {
            $this->publishPost($post);
            $this->snapshot(now());

            return 'Post published on the connected LinkedIn profile.';
        }

        return 'Post approved. It will publish on '.$post->scheduled_for->format('d M Y').', which keeps the one-post-every-3-days pace.';
    }

    public function publishDuePost(): ?string
    {
        $settings = LinkedinSetting::current();
        if (!$settings->posting_enabled || !$settings->isConnected() || !$this->postIntervalOpen()) {
            return null;
        }

        $post = LinkedinPost::query()
            ->where('status', 'approved')
            ->whereNotNull('image')
            ->whereDate('scheduled_for', '<=', today())
            ->orderBy('scheduled_for')
            ->orderBy('id')
            ->first();

        if (!$post) {
            return null;
        }

        $this->publishPost($post);

        return $post->linkedin_post_id;
    }

    public function releaseOpenQueue(): void
    {
        $connectionRoom = $this->connectionSlots(true);
        $pendingConnections = LinkedinMessage::query()
            ->where('kind', 'connection')
            ->where('status', 'pending_approval')
            ->orderBy('id')
            ->limit($connectionRoom)
            ->get();
        foreach ($pendingConnections as $message) {
            try {
                $this->approveMessage($message);
            } catch (RuntimeException $exception) {
                continue;
            }
        }

        $followRoom = $this->followUpSlots(true);
        $pendingFollowUps = LinkedinMessage::query()
            ->where('kind', 'follow_up')
            ->where('status', 'pending_approval')
            ->orderBy('id')
            ->limit($followRoom)
            ->get();
        foreach ($pendingFollowUps as $message) {
            try {
                $this->approveMessage($message);
            } catch (RuntimeException $exception) {
                continue;
            }
        }
    }

    public function releasePosts(): void
    {
        if (LinkedinSetting::current()->require_approval) {
            return;
        }

        $posts = LinkedinPost::query()
            ->where('status', 'pending_approval')
            ->whereNotNull('image')
            ->orderBy('id')
            ->get();

        foreach ($posts as $post) {
            try {
                $this->approvePost($post);
            } catch (RuntimeException $exception) {
                continue;
            }
        }
    }

    public function snapshot(Carbon $day): LinkedinReport
    {
        $report = LinkedinReport::query()->firstOrNew([
            'report_date' => $day->toDateString(),
        ]);

        $researched = LinkedinProspect::query()->whereDate('researched_at', $day)->orderBy('id')->get();
        $approved = LinkedinMessage::query()->with('prospect')->whereDate('approved_at', $day)->orderBy('id')->get();
        $replies = LinkedinProspect::query()->whereDate('response_at', $day)->whereIn('response', ['replied', 'qualified'])->orderBy('id')->get();
        $qualified = LinkedinProspect::query()->whereDate('qualified_at', $day)->orderBy('id')->get();
        $declined = LinkedinProspect::query()->whereDate('response_at', $day)->where('response', 'declined')->count();
        $optedOut = LinkedinProspect::query()->whereDate('response_at', $day)->where('response', 'opted_out')->count();
        $postsPublished = LinkedinPost::query()->whereDate('published_at', $day)->count();

        $waitingForPicture = LinkedinPost::query()
            ->whereIn('status', ['pending_approval', 'approved', 'failed'])
            ->whereNull('image')
            ->exists();

        $report->researched = $researched->count();
        $report->approved = $approved->count();
        $report->replies = $replies->count();
        $report->qualified = $qualified->count();
        $report->declined = $declined;
        $report->opted_out = $optedOut;
        $report->posts_published = $postsPublished;
        $report->notes = $waitingForPicture ? 'A post is waiting for a picture before it can be approved or published.' : null;
        $report->summary = [
            'researched' => $researched->map(fn (LinkedinProspect $prospect) => $this->prospectSummary($prospect))->all(),
            'approved'   => $approved->map(fn (LinkedinMessage $message) => [
                'name'    => $message->prospect->name ?? '',
                'company' => $message->prospect->company ?? '',
                'kind'    => $message->kindLabel(),
                'url'     => $message->prospect->profile_url ?? '',
            ])->all(),
            'replies'    => $replies->map(fn (LinkedinProspect $prospect) => $this->prospectSummary($prospect))->all(),
            'qualified'  => $qualified->map(fn (LinkedinProspect $prospect) => $this->prospectSummary($prospect))->all(),
        ];
        $report->save();

        return $report;
    }

    public function connectionSlots(bool $forApproval = false): int
    {
        $limit = $this->dailyLimit();
        $sentToday = LinkedinMessage::query()->where('kind', 'connection')->whereDate('sent_at', today())->count();
        $active = LinkedinMessage::query()->where('kind', 'connection')->where('status', 'approved')->count();
        if (!$forApproval) {
            $active += LinkedinMessage::query()->where('kind', 'connection')->where('status', 'pending_approval')->count();
        }

        return max(0, $limit - $sentToday - $active);
    }

    public function postIntervalOpen(): bool
    {
        $days = max(3, (int) LinkedinSetting::current()->post_interval_days);
        $last = LinkedinPost::query()->where('status', 'published')->max('published_at');
        if (!$last) {
            return true;
        }

        return Carbon::parse($last)->startOfDay()->lte(today()->subDays($days));
    }

    public function nextPostDate(): Carbon
    {
        $days = max(3, (int) LinkedinSetting::current()->post_interval_days);
        $last = LinkedinPost::query()->where('status', 'published')->max('published_at');
        if (!$last) {
            return today();
        }

        $next = Carbon::parse($last)->startOfDay()->addDays($days);

        return $next->lt(today()) ? today() : $next;
    }

    private function createConnectionDraft(LinkedinProspect $prospect, LinkedinSetting $settings): LinkedinMessage
    {
        return DB::transaction(function () use ($prospect, $settings) {
            $message = $prospect->messages()->create([
                'kind'   => 'connection',
                'body'   => $this->connectionNote($prospect),
                'basis'  => $this->basis($prospect),
                'status' => $settings->require_approval ? 'pending_approval' : 'approved',
                'approved_at' => $settings->require_approval ? null : now(),
            ]);

            $prospect->fit = 'fit';
            $prospect->outreach_status = $settings->require_approval ? 'pending_approval' : 'approved';
            $prospect->redraft_on = null;
            $prospect->researched_at = $prospect->researched_at ?: now();
            $prospect->save();

            return $message;
        });
    }

    private function createFollowUpDraft(LinkedinProspect $prospect, LinkedinSetting $settings): LinkedinMessage
    {
        return DB::transaction(function () use ($prospect, $settings) {
            $message = $prospect->messages()->create([
                'kind'   => 'follow_up',
                'body'   => $this->followUpNote($prospect),
                'basis'  => $this->basis($prospect),
                'status' => $settings->require_approval ? 'pending_approval' : 'approved',
                'approved_at' => $settings->require_approval ? null : now(),
            ]);

            $prospect->follow_up_status = $settings->require_approval ? 'pending_approval' : 'approved';
            $prospect->save();

            return $message;
        });
    }

    private function publishPost(LinkedinPost $post): void
    {
        $settings = LinkedinSetting::current();
        try {
            $id = $this->client->publish($settings, $post->commentary, $post->image);
            $post->status = 'published';
            $post->published_at = now();
            $post->linkedin_post_id = $id !== '' ? $id : null;
            $post->error = null;
            $post->save();
        } catch (RuntimeException $exception) {
            $post->status = 'failed';
            $post->error = $exception->getMessage();
            $post->save();
            $settings->last_error = $exception->getMessage();
            $settings->save();
            throw $exception;
        }
    }

    private function stop(LinkedinProspect $prospect, string $reason): void
    {
        $this->cancelOpenMessages($prospect);
        $prospect->do_not_contact = true;
        $prospect->stop_reason = $reason;
        $prospect->response = $reason;
        $prospect->response_at = now();
        $prospect->outreach_status = $reason;
        $prospect->follow_up_status = 'stopped';
        $prospect->save();
    }

    private function cancelOpenMessages(LinkedinProspect $prospect): void
    {
        $prospect->messages()->whereIn('status', ['pending_approval', 'approved'])->update([
            'status' => 'cancelled',
        ]);
    }

    private function matchesAudience(?string $title, LinkedinSetting $settings): bool
    {
        $title = trim((string) $title);
        if ($title === '') {
            return false;
        }

        $lower = strtolower($title);
        $strong = '/\b(founder|co-founder|cofounder|ceo|chief executive|managing director|proprietor)\b/i';
        $hasStrong = preg_match($strong, $lower) === 1;

        if (!$hasStrong) {
            foreach (['intern', 'student', 'assistant', 'analyst', 'coordinator'] as $blocked) {
                if (preg_match('/\b'.preg_quote($blocked, '/').'\b/i', $lower)) {
                    return false;
                }
            }
            if (preg_match('/\bvice president\b/i', $lower)) {
                return false;
            }
            if (str_contains($lower, 'product owner')) {
                return false;
            }
        }

        foreach ($settings->audienceNeedles() as $needle) {
            if ($needle === '') {
                continue;
            }
            if (preg_match('/\b'.preg_quote($needle, '/').'\b/i', $lower)) {
                return true;
            }
        }

        return false;
    }

    private function connectionNote(LinkedinProspect $prospect): string
    {
        $first = $this->firstName($prospect->name);

        return "Hi {$first}, I came across your profile and wanted to reach out. I’m with Crownmaire Capital, and I’d be glad to connect and explore whether there may be opportunities for us to work together. Looking forward to connecting!";
    }

    private function followUpNote(LinkedinProspect $prospect): string
    {
        $first = $this->firstName($prospect->name);

        return "Hi {$first}, thank you for connecting. I’m with Crownmaire Capital, and I’d be glad to explore whether there may be opportunities for us to work together.";
    }

    private function firstName(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $first = trim((string) ($parts[0] ?? ''), '.,');
        if ($first === '' || mb_strlen($first) < 2) {
            return 'there';
        }

        return $first;
    }

    private function basis(LinkedinProspect $prospect): string
    {
        return 'Welcome note for '.$this->firstName($prospect->name).'. No account is mentioned.';
    }

    private function followUpSlots(bool $forApproval = false): int
    {
        $sentToday = LinkedinMessage::query()->where('kind', 'follow_up')->whereDate('sent_at', today())->count();
        $active = LinkedinMessage::query()->where('kind', 'follow_up')->where('status', 'approved')->count();
        if (!$forApproval) {
            $active += LinkedinMessage::query()->where('kind', 'follow_up')->where('status', 'pending_approval')->count();
        }

        return max(0, $this->dailyLimit() - $sentToday - $active);
    }

    private function dailyLimit(): int
    {
        return min(20, max(1, (int) LinkedinSetting::current()->daily_connection_limit));
    }

    private function clientScore(LinkedinProspect $prospect): int
    {
        $text = strtolower(trim($prospect->title.' '.$prospect->company.' '.$prospect->public_note));
        $score = 20;

        if (preg_match('/\b(family office|holding|chairman|chairwoman|private equity|institutional|multi-family)\b/', $text)) {
            $score += 40;
        }
        if (preg_match('/\b(ceo|chief executive|managing partner|general partner|fund|funds)\b/', $text)) {
            $score += 25;
        }
        if (preg_match('/\b(million|acquisition|exited|portfolio|aum|international|offices)\b/', $text)) {
            $score += 15;
        }
        if (preg_match('/\b(freelancer|freelance|coach|student|intern|dropship|solopreneur|influencer|affiliate)\b/', $text)
            || str_contains($text, 'virtual assistant')
            || str_contains($text, 'side hustle')
            || str_contains($text, 'just started')
            || str_contains($text, 'open to work')
            || str_contains($text, 'seeking opportunities')) {
            $score -= 40;
        }

        return $score;
    }

    private function refreshLegacyDraft(LinkedinSetting $settings): void
    {
        $draft = LinkedinPost::query()
            ->where('source', 'scheduled')
            ->where('status', 'pending_approval')
            ->whereNull('image')
            ->orderByDesc('id')
            ->first();

        if (!$draft || !$this->isLegacyPost($draft->commentary)) {
            return;
        }

        $draft->commentary = $this->composePost((int) $settings->post_template_index);
        $draft->save();
        $settings->post_template_index = (int) $settings->post_template_index + 1;
        $settings->save();
    }

    private function isLegacyPost(string $commentary): bool
    {
        foreach ([
            'Business owners usually want a straight answer',
            'A useful investment conversation starts with the company',
            'Crownmaire Capital helps business owners think through investment services',
            'Founders ask us where to start',
        ] as $old) {
            if (str_contains($commentary, $old)) {
                return true;
            }
        }

        return false;
    }

    private function composePost(int $index): string
    {
        $posts = [
            "Crownmaire Capital is a quantitative asset management firm for qualified investors. The programs are private and invitation-only, built on research, algorithmic execution, and multi-asset exposure across currencies, indices, commodities, futures, and select equities.\n\nOffices are in New York and Dubai. Participation starts at \$20,000 for qualified investors, with capacity for larger allocations.\n\nEach investment inquiry is reviewed before access is offered.",
            "Qualified investors use Crownmaire Capital when they want a disciplined quantitative program rather than a public product menu. Access is invitation-only. The firm works with select high-net-worth individuals and institutional counterparties.\n\nMinimum allocation is \$20,000. Larger capital is the priority.\n\nSend an investment inquiry and the team follows up if there is a fit.",
            "Crownmaire manages diversified exposure across currencies, indices, commodities, futures, and select equities. Portfolios are structured around quantitative models, active risk controls, and institutional reporting inside the member portal.\n\nThis is for qualified participants who can allocate from \$20,000, including larger private allocations.",
            "Crownmaire Capital operates from New York and Dubai. The firm is not a traditional asset manager. Programs are contractual, private, and limited to qualified investors.\n\nThe work is quantitative: data, algorithmic execution, and risk-aware allocation. Conversations start from \$20,000 and scale for larger clients.",
            "Members of Crownmaire Capital see capital activity, performance, and portfolio allocation in an institutional portal. The public step is an investment inquiry. The team then decides whether a private program is a fit.\n\nQualified investors allocating from \$20,000, and those allocating well above that, are the audience.",
            "A Crownmaire allocation is for someone who already has capital to put to work. The firm runs private multi-asset quantitative programs for qualified investors, with a \$20,000 minimum and a preference for larger relationships.\n\nNew York and Dubai. Invitation-only. No public signup.",
        ];

        $hashtags = [
            '#CrownmaireCapital #QuantitativeAssetManagement #QualifiedInvestors #MultiAsset #NewYork #Dubai',
            '#CrownmaireCapital #PrivateInvestment #AlgorithmicInvesting #AssetManagement #QualifiedInvestors #InstitutionalInvesting',
            '#CrownmaireCapital #MultiAsset #QuantitativeTrading #AlgorithmicInvestmentManagement #QualifiedInvestors #Dubai',
            '#CrownmaireCapital #NewYork #Dubai #QuantitativeAssetManagement #Fintech #PrivateInvestmentPrograms',
            '#CrownmaireCapital #InstitutionalReporting #QuantitativeAssetManagement #QualifiedInvestors #AssetManagement #InvestmentInquiry',
            '#CrownmaireCapital #QualifiedInvestors #PrivateInvestmentPrograms #QuantitativeAssetManagement #MultiAsset #NewYork',
        ];

        $choice = $index % count($posts);

        return $posts[$choice]."\n\n".$hashtags[$choice];
    }

    private function prospectSummary(LinkedinProspect $prospect): array
    {
        return [
            'name'    => $prospect->name,
            'company' => $prospect->company,
            'url'     => $prospect->profile_url,
            'status'  => $prospect->statusLabel(),
        ];
    }
}
