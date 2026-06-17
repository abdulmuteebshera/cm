<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\PlanPeriodReturn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PrepareStrategyPayoutTest extends Command
{
    protected $signature = 'portal:prepare-payout-test
        {username=muteeb : Username to prepare}
        {--plan= : Strategy plan ID (defaults to first quarterly plan)}
        {--period=1 : Period index (Q1=1, Q2=2, etc.)}
        {--year= : Calendar year (defaults to current year)}
        {--invest-date= : Backdate invest created_at (Y-m-d)}
        {--weekly-rate=3 : Weekly return percent to seed and approve}
        {--force : Skip confirmation}';

    protected $description = 'Seed approved weekly returns and submit a pending period return for payout testing';

    public function handle(): int
    {
        $username = (string) $this->argument('username');
        $user     = User::where('username', $username)->orWhere('firstname', 'Muteeb')->first();

        if (!$user) {
            $this->error("User [{$username}] not found.");
            return self::FAILURE;
        }

        $plan = $this->resolvePlan();
        if (!$plan) {
            $this->error('No active strategy plan found.');
            return self::FAILURE;
        }

        $year        = (int) ($this->option('year') ?: now()->year);
        $periodIndex = (int) $this->option('period');
        $weeklyRate  = (float) $this->option('weekly-rate');

        if (!$this->option('force') && !$this->confirm("Prepare payout test for {$user->username} on {$plan->name} ({$plan->periodLabel($year, $periodIndex)})?")) {
            return self::SUCCESS;
        }

        $invest = Invest::where('user_id', $user->id)->where('plan_id', $plan->id)->where('status', 1)->latest()->first();
        if (!$invest) {
            $this->error('No active investment found for this user/plan.');
            return self::FAILURE;
        }

        if ($date = $this->option('invest-date')) {
            $invest->created_at = Carbon::parse($date)->startOfDay();
            $invest->save();
            $this->info("Invest backdated to {$invest->created_at}.");
        }

        $periodMeta = StrategyPayoutService::periodMeta($plan, $year, $periodIndex);
        $seededWeeks = 0;

        foreach (StrategyPayoutService::weeksInCalendarYear($year) as $weekMeta) {
            $weekStart = Carbon::parse($weekMeta['week_start']);
            $weekEnd   = Carbon::parse($weekMeta['week_end']);
            $periodStart = Carbon::parse($periodMeta['period_start']);
            $periodEnd   = Carbon::parse($periodMeta['period_end']);

            if ($weekEnd->lt($periodStart) || $weekStart->gt($periodEnd)) {
                continue;
            }

            $weeklyRecord = StrategyPayoutService::syncWeeklyReturnRecord(
                $plan,
                $weekMeta['iso_year'],
                $weekMeta['week'],
                $weeklyRate
            );

            if ($weeklyRecord->isApprovable()) {
                StrategyPayoutService::approveWeeklyReturn($weeklyRecord, 0);
            }

            $seededWeeks++;
        }

        $periodReturn = StrategyPayoutService::syncPeriodReturnRecord($plan, $year, $periodIndex);

        $this->info("Seeded {$seededWeeks} approved weekly returns at {$weeklyRate}% each.");
        $this->info("Period return {$periodReturn->periodLabel()} = {$periodReturn->return_percent}% (status: {$periodReturn->payout_status}).");
        $this->info('Next: Admin → Strategy Payout Approvals → review and approve.');

        return self::SUCCESS;
    }

    private function resolvePlan(): ?Plan
    {
        if ($this->option('plan')) {
            return Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('status', 1)->find($this->option('plan'));
        }

        return Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('status', 1)->orderBy('id')->first();
    }
}
