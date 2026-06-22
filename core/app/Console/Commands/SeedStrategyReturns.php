<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use App\Models\Plan;
use App\Models\PlanMonthlyReturn;
use App\Models\PlanWeeklyReturn;
use Illuminate\Console\Command;

/**
 * One-off / re-runnable seeder for Crownmaire strategy branding + approved
 * performance returns. Generates randomised weekly/monthly rates that add up
 * to the requested cumulative (YTD) percentage for each year, then marks them
 * approved so they render on the live performance charts.
 */
class SeedStrategyReturns extends Command
{
    protected $signature = 'portal:seed-strategy-returns {--force : Skip confirmation}';

    protected $description = 'Rename the three strategies and seed approved weekly/monthly returns to match target yearly returns';

    /** Cumulative (sum of period %) targets per calendar year. */
    private const WEEKLY_TARGETS = [
        2023 => 19.2,
        2024 => 28.5,
        2025 => 25.8,
        2026 => 13.0,
    ];

    private const GROWTH_MONTHLY_TARGETS = [
        2023 => 24.2,
        2024 => 30.0,
        2025 => 32.3,
        2026 => 17.4,
    ];

    private const LEGACY_MONTHLY_TARGETS = [
        2023 => 27.9,
        2024 => 33.1,
        2025 => 35.5,
        2026 => 17.8,
    ];

    public function handle(): int
    {
        $quarterly = Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('payout_frequency', Plan::FREQ_QUARTERLY)->first();
        $semi      = Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('payout_frequency', Plan::FREQ_SEMI_ANNUAL)->first();
        $yearly    = Plan::where('plan_mode', Plan::MODE_STRATEGY)->where('payout_frequency', Plan::FREQ_YEARLY)->first();

        if (!$quarterly || !$semi || !$yearly) {
            $this->error('Could not locate all three strategy plans (quarterly / semi_annual / yearly).');
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm('This rewrites approved performance returns for all three strategies. Continue?')) {
            return self::SUCCESS;
        }

        // 1) Rebrand the strategies.
        $this->renamePlan($quarterly, 'Crownmaire Alpha');
        $this->renamePlan($semi, 'Crownmaire Growth');
        $this->renamePlan($yearly, 'Crownmaire Legacy');

        // 2) Seed approved returns.
        $this->seedWeekly($quarterly, self::WEEKLY_TARGETS);
        $this->seedMonthly($semi, self::GROWTH_MONTHLY_TARGETS);
        $this->seedMonthly($yearly, self::LEGACY_MONTHLY_TARGETS);

        $this->info('Done. Strategy names updated and approved returns seeded.');
        return self::SUCCESS;
    }

    private function renamePlan(Plan $plan, string $name): void
    {
        $plan->name = $name;
        $plan->save();
        $this->line("Renamed plan #{$plan->id} -> {$name}");
    }

    /**
     * Quarterly strategy = weekly performance. Each week is in [-2, 3]%,
     * mostly between -1 and 1.5%. The approved weeks per calendar year sum
     * to the requested cumulative return.
     */
    private function seedWeekly(Plan $plan, array $targets): void
    {
        PlanWeeklyReturn::where('plan_id', $plan->id)->delete();

        // Build the week universe across all target years. A boundary ISO week
        // can belong to two calendar years (overlaps Dec/Jan); track that so the
        // chart's YTD sum stays exact for both years.
        $membership = []; // weekKey => [calendarYear, ...]
        $meta       = []; // weekKey => ['iso_year','week','enterable']

        foreach (array_keys($targets) as $year) {
            foreach (StrategyPayoutService::weeksInCalendarYear($year) as $w) {
                $key = $w['iso_year'] . '_' . $w['week'];
                $membership[$key][] = $year;
                $meta[$key] = [
                    'iso_year'  => (int) $w['iso_year'],
                    'week'      => (int) $w['week'],
                    'enterable' => (bool) $w['is_enterable'],
                ];
            }
        }

        // Pre-assign shared boundary weeks a fixed soft value so they can be
        // subtracted from both years they belong to.
        $assigned = [];
        foreach ($membership as $key => $years) {
            if (count(array_unique($years)) > 1 && $meta[$key]['enterable']) {
                $assigned[$key] = $this->softValue(-1.0, 1.5);
            }
        }

        foreach ($targets as $year => $target) {
            // Weeks belonging only to this calendar year and already finished.
            $exclusiveKeys = [];
            $sharedContribution = 0.0;

            foreach ($membership as $key => $years) {
                if (!in_array($year, $years, true)) {
                    continue;
                }
                if (!$meta[$key]['enterable']) {
                    continue; // future week — not on chart, skip
                }
                if (isset($assigned[$key])) {
                    $sharedContribution += $assigned[$key];
                    continue;
                }
                $exclusiveKeys[] = $key;
            }

            $remaining = round($target - $sharedContribution, 2);
            $values    = $this->distribute(count($exclusiveKeys), $remaining, -2.0, 3.0, -1.0, 1.5);

            foreach ($exclusiveKeys as $i => $key) {
                $this->storeWeekly($plan, $meta[$key]['iso_year'], $meta[$key]['week'], $values[$i]);
            }

            $this->line("  {$plan->name} weekly {$year}: target {$target}% across " . count($exclusiveKeys) . ' weeks (+shared)');
        }

        // Persist the shared boundary weeks once.
        foreach ($assigned as $key => $value) {
            $this->storeWeekly($plan, $meta[$key]['iso_year'], $meta[$key]['week'], $value);
        }
    }

    /**
     * 6-month & yearly strategies = monthly performance. Each month is in
     * [-3, 4]%, mostly between -3 and 3%. Approved months per year sum to the
     * requested cumulative return.
     */
    private function seedMonthly(Plan $plan, array $targets): void
    {
        PlanMonthlyReturn::where('plan_id', $plan->id)->delete();

        foreach ($targets as $year => $target) {
            $months = collect(StrategyPayoutService::monthsInCalendarYear($year))
                ->filter(fn ($m) => (bool) $m['is_enterable'])
                ->values();

            $values = $this->distribute($months->count(), (float) $target, -3.0, 4.0, -2.5, 3.0);

            foreach ($months as $i => $m) {
                $this->storeMonthly($plan, (int) $m['year'], (int) $m['month'], $values[$i]);
            }

            $this->line("  {$plan->name} monthly {$year}: target {$target}% across " . $months->count() . ' months');
        }
    }

    private function storeWeekly(Plan $plan, int $isoYear, int $week, float $value): void
    {
        $record = PlanWeeklyReturn::firstOrNew([
            'plan_id' => $plan->id,
            'year'    => $isoYear,
            'week'    => $week,
        ]);

        $record->return_percent = round($value, 4);
        $record->total_payout   = 0;
        $record->payout_status  = PlanWeeklyReturn::STATUS_APPROVED;
        $record->approved_by    = 1;
        $record->approved_at    = now();
        $record->save();
    }

    private function storeMonthly(Plan $plan, int $year, int $month, float $value): void
    {
        $record = PlanMonthlyReturn::firstOrNew([
            'plan_id' => $plan->id,
            'year'    => $year,
            'month'   => $month,
        ]);

        $record->return_percent = round($value, 4);
        $record->total_payout   = 0;
        $record->payout_status  = PlanMonthlyReturn::STATUS_APPROVED;
        $record->approved_by    = 1;
        $record->approved_at    = now();
        $record->save();
    }

    /** A single random value (2dp) inside the "soft" range. */
    private function softValue(float $softMin, float $softMax): float
    {
        return round(mt_rand((int) round($softMin * 100), (int) round($softMax * 100)) / 100, 2);
    }

    /**
     * Distribute $target across $n values (2dp each), every value kept inside
     * the hard [$hardMin, $hardMax] range, biased toward the soft range. The
     * returned values sum to exactly $target (to 2dp) when feasible.
     */
    private function distribute(int $n, float $target, float $hardMin, float $hardMax, float $softMin, float $softMax): array
    {
        if ($n <= 0) {
            return [];
        }

        $values = [];
        for ($i = 0; $i < $n; $i++) {
            $values[$i] = $this->softValue($softMin, $softMax);
        }

        // Iteratively nudge values toward the target, respecting hard bounds.
        for ($iter = 0; $iter < 5000; $iter++) {
            $residual = $target - array_sum($values);
            if (abs($residual) < 0.001) {
                break;
            }

            $movable = [];
            foreach ($values as $i => $v) {
                if ($residual > 0 && $v < $hardMax) {
                    $movable[] = $i;
                } elseif ($residual < 0 && $v > $hardMin) {
                    $movable[] = $i;
                }
            }

            if (empty($movable)) {
                break;
            }

            $step = $residual / count($movable);
            foreach ($movable as $i) {
                $values[$i] = max($hardMin, min($hardMax, $values[$i] + $step));
            }
        }

        // Round to 2dp, then push the rounding remainder onto cells with headroom.
        foreach ($values as $i => $v) {
            $values[$i] = round($v, 2);
        }

        $residualCents = (int) round(($target - array_sum($values)) * 100);
        $guard = 0;
        while ($residualCents !== 0 && $guard < 100000) {
            $dir = $residualCents > 0 ? 1 : -1;
            $moved = false;

            foreach (array_keys($values) as $i) {
                if ($residualCents === 0) {
                    break;
                }
                $next = round($values[$i] + $dir * 0.01, 2);
                if ($next >= $hardMin && $next <= $hardMax) {
                    $values[$i] = $next;
                    $residualCents -= $dir;
                    $moved = true;
                }
            }

            if (!$moved) {
                break;
            }
            $guard++;
        }

        return $values;
    }
}
