<?php

namespace App\Console\Commands;

use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcCampaign;
use App\Models\EmailCampaign\EcCampaignRecipient;
use App\Support\EmailCampaign\EcMailSender;
use App\Support\EmailCampaign\EcTemplateRenderer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessEcCampaigns extends Command
{
    protected $signature = 'ec:process-campaigns';

    protected $description = 'Send one pending email per running email campaign (respects send delay)';

    public function handle(EcMailSender $sender): int
    {
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

        return self::SUCCESS;
    }

    protected function processCampaign(EcCampaign $campaign, EcMailSender $sender): void
    {
        DB::transaction(function () use ($campaign, $sender): void {
            $locked = EcCampaign::query()->whereKey($campaign->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'running') {
                return;
            }

            if ($locked->next_send_at && $locked->next_send_at->isFuture()) {
                return;
            }

            /** @var EcCampaignRecipient|null $recipient */
            $recipient = EcCampaignRecipient::query()
                ->where('ec_campaign_id', $locked->id)
                ->where('status', 'pending')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$recipient) {
                $locked->status = 'completed';
                $locked->completed_at = now();
                $locked->next_send_at = null;
                $locked->save();

                EcActivityLog::record(
                    'system',
                    null,
                    'campaign.completed',
                    'Campaign completed: ' . $locked->name,
                    'campaign',
                    $locked->id,
                    ['ec_user_id' => $locked->ec_user_id]
                );

                return;
            }

            $vars = EcTemplateRenderer::varsForRecipient($recipient->name, $recipient->email, $recipient->merge_data);
            $subject = EcTemplateRenderer::render($locked->subject, $vars);
            $html    = EcTemplateRenderer::render($locked->body_html, $vars);
            $text    = $locked->body_text ? EcTemplateRenderer::render($locked->body_text, $vars) : null;

            try {
                $sender->send($recipient->email, $recipient->name, $subject, $html, $text);
                $recipient->status = 'sent';
                $recipient->sent_at = now();
                $recipient->error_message = null;
                $recipient->save();

                $locked->sent_count = EcCampaignRecipient::query()
                    ->where('ec_campaign_id', $locked->id)
                    ->where('status', 'sent')
                    ->count();
            } catch (\Throwable $e) {
                $recipient->status = 'failed';
                $recipient->error_message = $e->getMessage();
                $recipient->save();

                $locked->failed_count = EcCampaignRecipient::query()
                    ->where('ec_campaign_id', $locked->id)
                    ->where('status', 'failed')
                    ->count();

                EcActivityLog::record(
                    'system',
                    null,
                    'campaign.send_failed',
                    'Failed to send to ' . $recipient->email,
                    'campaign',
                    $locked->id,
                    ['error' => $e->getMessage(), 'ec_user_id' => $locked->ec_user_id]
                );
            }

            $delay = max(5, (int) ($locked->send_delay_seconds ?: 10));
            $locked->next_send_at = now()->addSeconds($delay);
            $locked->save();
        });
    }
}
