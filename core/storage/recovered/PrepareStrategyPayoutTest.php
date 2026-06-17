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
        {--invest-date= : Investment start date (defaults to start of period)}';

    protected $description = 'Backdate a strategy investment, seed sample weekly returns, and create a pending period payout from approved weekly performance';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();

        if (!$user) {
            $this->error('User not found.');
            return self::FAILURE;
        }

        $plan = $this->option('plan')
            ? Plan::where('plan_mode', Plan::MODE_STRATEGY)->find($this->option('plan'))
            : Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('payout_frequency', Plan::FREQ_QUARTERLY)->first();

        if (!$plan) {
            $this->error('Strategy plan not found.');
            return self::FAILURE;
        }

        $year        = (int) ($this->option('year') ?: date('Y'));
        $periodIndex = (int) $this->option('period');
        $periodMeta  = collect(StrategyPayoutService::periodsInYear($plan, $year))
            ->firstWhere('period_index', $periodIndex);

        if (!$periodMeta) {
            $this->error("Period {$periodIndex} not found for {$year}.");
            return self::FAILURE;
        }

        $invest = Invest::where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', 1)
            ->first();

        if (!$invest) {
            $this->error("No active investment found for {$user->username} in {$plan->name}.");
            return self::FAILURE;
        }

        $periodStart = Carbon::parse($periodMeta['period_start'])->startOfDay();
        $periodEnd   = Carbon::parse($periodMeta['period_end'])->endOfDay();
        $investDate  = $this->option('invest-date')
            ? Carbon::parse($this->option('invest-date'))
            : $periodStart->copy()->addDays(4)->setTime(10, 0, 0);

        $invest->created_at      = $investDate;
        $invest->next_time       = StrategyPayoutService::nextPeriodEnd($plan);
        $invest->return_rec_time = 0;
        $invest->paid            = 0;
        $invest->net_interest    = 0;
        $invest->save();

        PlanPeriodReturn::where('plan_id', $plan->id)
            ->where('year', $year)
            ->where('period_index', $periodIndex)
            ->where('payout_status', '!=', PlanPeriodReturn::STATUS_APPROVED)
            ->delete();

        $sampleRates = [0.5, 0.75, -0.25, 1.0, 0.6, 0.4, 0.8, 0.55, 0.65, 0.7, 0.45, 0.9, 0.35];
        $seededWeeks = 0;

        foreach (StrategyPayoutService::weeksInCalendarYear($year) as $weekMeta) {
            $weekStart = StrategyPayoutService::weekDateRange($weekMeta['iso_year'], $weekMeta['week'])['start'];
            $weekEnd   = StrategyPayoutService::weekDateRange($weekMeta['iso_year'], $weekMeta['week'])['end'];

            if ($weekEnd->lt($periodStart) || $weekStart->gt($periodEnd)) {
                continue;
            }

            if (!$weekMeta['is_enterable']) {
                continue;
            }

            $rate = $sampleRates[$seededWeeks % count($sampleRates)];
            $weeklyRecord = StrategyPayoutService::syncWeeklyReturnRecord(
                $plan,
                $weekMeta['iso_year'],
                $weekMeta['week'],
                $rate
            );
            StrategyPayoutService::approveWeeklyReturn($weeklyRecord, 0);
            $seededWeeks++;
        }

        if ($seededWeeks === 0) {
            $this->error('No enterable weeks found in this period to seed weekly returns.');
            return self::FAILURE;
        }

        $record = StrategyPayoutService::buildPeriodReturnFromWeekly($plan, $year, $periodIndex);

        if (!$record) {
            $this->error('Could not build period payout from weekly returns.');
            return self::FAILURE;
        }

        $preview = StrategyPayoutService::previewPeriodPayout($record);
        $weeklyTotal = StrategyPayoutService::periodReturnPercentFromWeekly($plan, $periodStart, $periodEnd);

        $this->info("Prepared payout test for {$user->firstname} {$user->lastname} ({$user->username})");
        $this->line("Strategy: {$plan->name} (ID {$plan->id})");
        $this->line("Investment #{$invest->id} backdated to {$invest->created_at}");
        $this->line("Seeded {$seededWeeks} approved weekly returns in {$record->periodLabel()}");
        $this->line("Period return (sum of weekly %): {$weeklyTotal}% — status: {$record->payout_status}");
        $this->line('Estimated total payout: ' . showAmount($preview['total']));

        foreach ($preview['lines'] as $line) {
            $this->line("  · {$line['user']->username}: " . showAmount($line['amount']));
        }

        $this->newLine();
        $this->comment('Admin: Strategy Payout Approvals → pending item, or Period Returns → Review & Approve');

        return self::SUCCESS;
    }
}
