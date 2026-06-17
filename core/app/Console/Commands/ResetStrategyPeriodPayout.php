<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use App\Models\PlanPeriodReturn;
use Illuminate\Console\Command;

class ResetStrategyPeriodPayout extends Command
{
    protected $signature = 'portal:reset-period-payout {periodReturnId : plan_period_returns.id} {--force : Skip confirmation}';

    protected $description = 'Reverse an approved strategy period payout so it can be re-approved with corrected math';

    public function handle(): int
    {
        $periodReturn = PlanPeriodReturn::with('payoutItems')->find($this->argument('periodReturnId'));

        if (!$periodReturn) {
            $this->error('Period return not found.');
            return self::FAILURE;
        }

        if ($periodReturn->payout_status !== PlanPeriodReturn::STATUS_APPROVED) {
            $this->warn('Period return is not approved — re-syncing from active investments.');
            if ($periodReturn->plan) {
                StrategyPayoutService::syncDuePeriodReturnRecords($periodReturn->plan);
                StrategyPayoutService::cleanupStalePeriodReturnRecords($periodReturn->plan);
            }
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Reverse approved payout ' . $periodReturn->periodLabel() . '?')) {
            return self::SUCCESS;
        }

        StrategyPayoutService::reverseApprovedPeriodReturn($periodReturn);

        $this->info('Period payout reversed and re-synced from weekly returns. Re-approve from admin.');
        return self::SUCCESS;
    }
}
