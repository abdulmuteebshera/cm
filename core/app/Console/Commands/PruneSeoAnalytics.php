<?php

namespace App\Console\Commands;

use App\Models\SeoEvent;
use App\Models\SeoPageView;
use App\Models\SeoVisitorSession;
use App\Support\Seo\SeoTracker;
use Illuminate\Console\Command;

class PruneSeoAnalytics extends Command
{
    protected $signature = 'seo:prune {--days=180}';

    protected $description = 'Remove SEO analytics rows older than the given number of days';

    public function handle(): int
    {
        if (!SeoTracker::ready()) {
            $this->warn('SEO analytics tables are not installed.');
            return self::SUCCESS;
        }

        $days = max(30, (int) $this->option('days'));
        $before = now()->subDays($days);

        $events = SeoEvent::where('created_at', '<', $before)->delete();
        $views  = SeoPageView::where('entered_at', '<', $before)->delete();
        $sessions = SeoVisitorSession::where('started_at', '<', $before)->delete();

        $this->info("Pruned {$sessions} sessions, {$views} page views, {$events} events older than {$days} days.");

        return self::SUCCESS;
    }
}
