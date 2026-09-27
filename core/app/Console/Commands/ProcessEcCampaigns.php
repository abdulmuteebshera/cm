<?php

namespace App\Console\Commands;

use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcCampaign;
use App\Models\EmailCampaign\EcCampaignRecipient;
use App\Support\EmailCampaign\EcMailSender;
use App\Support\EmailCampaign\EcTemplateRenderer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ProcessEcCampaigns extends Command
{
    protected $signature = 'ec:process-campaigns';

    protected $description = 'Send one pending email per running email campaign (respects send delay)';

    public function handle(EcMailSender $sender): int
    {
        $lock = Cache::lock('ec:process-campaigns', 90);

        if (!$lock->get()) {
            return self::SUCCESS;
        }

        try {
            $campaigns = EcCampaign::query()
                ->where('status', 'running')
                ->where(function ($q): void {
                    $q->whereNull('next_send_at')->orWhere('next_send_at', '<=', now());
                })
                ->orderBy('id')
                ->limit(20)
                ->get();

            foreach ($campaigns as $campaign) {
                $this->processCampaign($campaign, $sender);
            }
        } finally {
            $lock->release();
        }

        return self::SUCCESS;
    }

    protected function processCampaign(EcCampaign $campaign, EcMailSender $sender): void
    {
        $campaign->refresh();
        if ($campaign->status !== 'running') {
            return;
        }

        if ($campaign->next_send_at && $campaign->next_send_at->isFuture()) {
            return;
        }

        $recipient = $this->claimNextRecipient($campaign->id);

        if (!$recipient) {
            $this->tryCompleteCampaign($campaign->id);

            return;
        }

        EcCampaign::query()
            ->where('id', $campaign->id)
            ->where('status', 'running')
            ->update([
                'next_send_at' => now()->addSeconds(max(5, (int) ($campaign->send_delay_seconds ?: 10))),
            ]);

        $campaign->refresh();

        $vars = EcTemplateRenderer::varsForRecipient($recipient->name, $recipient->email, $recipient->merge_data);
        $subject = EcTemplateRenderer::render($campaign->subject, $vars);
        $html    = EcTemplateRenderer::render($campaign->body_html, $vars);
        $text    = $campaign->body_text ? EcTemplateRenderer::render($campaign->body_text, $vars) : null;

        try {
            $sender->send($recipient->email, $recipient->name, $subject, $html, $text);
            EcCampaignRecipient::query()->where('id', $recipient->id)->update([
                'status'         => 'sent',
                'sent_at'        => now(),
                'error_message'  => null,
            ]);
        } catch (\Throwable $e) {
            EcCampaignRecipient::query()->where('id', $recipient->id)->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            EcActivityLog::record(
                'system',
                null,
                'campaign.send_failed',
                'Failed to send to ' . $recipient->email,
                'campaign',
                $campaign->id,
                [
                    'error'       => $e->getMessage(),
                    'ec_user_id'  => $campaign->ec_user_id,
                    'ec_admin_id' => $campaign->ec_admin_id,
                ]
            );
        }

        EcCampaign::query()->find($campaign->id)?->syncTotals();
    }

    protected function claimNextRecipient(int $campaignId): ?EcCampaignRecipient
    {
        $staleBefore = now()->subMinutes(3);

        $candidate = EcCampaignRecipient::query()
            ->where('ec_campaign_id', $campaignId)
            ->where('status', 'pending')
            ->orderBy('id')
            ->first();

        if (!$candidate) {
            $candidate = EcCampaignRecipient::query()
                ->where('ec_campaign_id', $campaignId)
                ->where('status', 'sending')
                ->where('updated_at', '<', $staleBefore)
                ->orderBy('id')
                ->first();
        }

        if (!$candidate) {
            return null;
        }

        $claimed = EcCampaignRecipient::query()
            ->where('id', $candidate->id)
            ->where('status', 'pending')
            ->update(['status' => 'sending', 'updated_at' => now()]);

        if ($claimed === 0) {
            $claimed = EcCampaignRecipient::query()
                ->where('id', $candidate->id)
                ->where('status', 'sending')
                ->where('updated_at', '<', $staleBefore)
                ->update(['status' => 'sending', 'updated_at' => now()]);
        }

        if ($claimed === 0) {
            return null;
        }

        return EcCampaignRecipient::query()->find($candidate->id);
    }

    protected function tryCompleteCampaign(int $campaignId): void
    {
        $hasQueue = EcCampaignRecipient::query()
            ->where('ec_campaign_id', $campaignId)
            ->whereIn('status', ['pending', 'sending'])
            ->exists();

        if ($hasQueue) {
            return;
        }

        EcCampaign::query()
            ->where('id', $campaignId)
            ->where('status', 'running')
            ->update([
                'status'       => 'completed',
                'completed_at' => now(),
                'next_send_at' => null,
            ]);

        $campaign = EcCampaign::query()->find($campaignId);
        if ($campaign) {
            $campaign->syncTotals();
            EcActivityLog::record(
                'system',
                null,
                'campaign.completed',
                'Campaign completed: ' . $campaign->name,
                'campaign',
                $campaign->id,
                ['ec_user_id' => $campaign->ec_user_id, 'ec_admin_id' => $campaign->ec_admin_id]
            );
        }
    }
}
