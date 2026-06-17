<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use Illuminate\Console\Command;

class SyncStrategyPeriodPayouts extends Command
{
    protected $signature = 'strategy:sync-period-payouts';

    protected $description = 'Queue pending strategy period payouts whose payout date has passed';

    public function handle(): int
    {
        $synced = StrategyPayoutService::syncDuePeriodReturnRecords();

        $this->info("Synced {$synced} due period payout(s) to pending approval.");

        return self::SUCCESS;
    }
}
