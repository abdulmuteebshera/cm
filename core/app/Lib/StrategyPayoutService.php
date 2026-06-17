<?php

namespace App\Lib;

use App\Models\Invest;
use App\Models\PeriodPayoutItem;
use App\Models\Plan;
use App\Models\PlanPeriodReturn;
use App\Models\PlanMonthlyReturn;
use App\Models\PlanStrategyReport;
use App\Models\PlanWeeklyReturn;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WeeklyPayoutItem;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StrategyPayoutService
{
    public const COMPANY_FOUNDED_YEAR = 2022;

    public static function nextWeekEnd(?CarbonInterface $from = null): Carbon
    {
        $date = $from ? Carbon::parse($from) : now();

        return $date->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
    }

    public static function weekDateRange(int $isoYear, int $isoWeek): array
    {
        $start = Carbon::now()->setISODate($isoYear, $isoWeek)->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end   = $start->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }

    public static function weekDateLabel(int $isoYear, int $isoWeek): string
    {
        $range = self::weekDateRange($isoYear, $isoWeek);

        return $range['start']->format('M d') . ' - ' . $range['end']->format('M d, Y');
    }

    public static function isWeekEnterable(int $isoYear, int $isoWeek): bool
    {
        $range = self::weekDateRange($isoYear, $isoWeek);

        return now()->greaterThan($range['end']);
    }

    public static function weeksInCalendarYear(int $year): Collection
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd   = Carbon::create($year, 12, 31)->endOfDay();
        $cursor    = $yearStart->copy()->startOfWeek(Carbon::MONDAY);
        $weeks     = collect();

        while ($cursor->lte($yearEnd)) {
            $weekStart = $cursor->copy()->startOfDay();
            $weekEnd   = $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            $overlaps  = $weekStart->lte($yearEnd) && $weekEnd->gte($yearStart);

            if ($overlaps) {
                $isoYear = (int) $weekStart->isoWeekYear;
                $isoWeek = (int) $weekStart->isoWeek;

                $weeks->push([
                    'iso_year'    => $isoYear,
                    'week'        => $isoWeek,
                    'week_start'  => $weekStart,
                    'week_end'    => $weekEnd,
                    'date_label'  => self::weekDateLabel($isoYear, $isoWeek),
                    'is_enterable'=> self::isWeekEnterable($isoYear, $isoWeek),
                ]);
            }

            $cursor->addWeek();
        }

        return $weeks->values();
    }

    public static function weeksGroupedByMonth(int $year): Collection
    {
        return self::weeksInCalendarYear($year)
            ->groupBy(function (array $weekMeta): string {
                return Carbon::parse($weekMeta['week_start'])->format('F Y');
            })
            ->sortKeys();
    }

    /** Weeks overlapping the last N months (for admin weekly approval). */
    public static function weeksInRecentMonths(int $months = 5, ?CarbonInterface $from = null): Collection
    {
        $end   = ($from ? Carbon::parse($from) : now())->copy()->endOfDay();
        $start = $end->copy()->subMonths($months)->startOfMonth()->startOfDay();
        $cursor = $start->copy()->startOfWeek(Carbon::MONDAY);
        $weeks  = collect();

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy()->startOfDay();
            $weekEnd   = $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

            if ($weekEnd->gte($start) && $weekStart->lte($end)) {
                $isoYear = (int) $weekStart->isoWeekYear;
                $isoWeek = (int) $weekStart->isoWeek;

                $weeks->push([
                    'iso_year'     => $isoYear,
                    'week'         => $isoWeek,
                    'week_start'   => $weekStart,
                    'week_end'     => $weekEnd,
                    'date_label'   => self::weekDateLabel($isoYear, $isoWeek),
                    'is_enterable' => self::isWeekEnterable($isoYear, $isoWeek),
                ]);
            }

            $cursor->addWeek();
        }

        return $weeks->values();
    }

    public static function weeksGroupedByMonthForRecentMonths(int $months = 5): Collection
    {
        return self::weeksInRecentMonths($months)
            ->groupBy(function (array $weekMeta): string {
                return Carbon::parse($weekMeta['week_start'])->format('F Y');
            })
            ->sortKeys();
    }

    public static function syncWeeklyReturnRecord(Plan $plan, int $isoYear, int $isoWeek, ?float $returnPercent): PlanWeeklyReturn
    {
        $record = PlanWeeklyReturn::firstOrNew([
            'plan_id' => $plan->id,
            'year'    => $isoYear,
            'week'    => $isoWeek,
        ]);

        if ($record->payout_status === PlanWeeklyReturn::STATUS_APPROVED) {
            return $record;
        }

        $rate = round((float) ($returnPercent ?? 0), 4);

        $record->return_percent = $rate;
        $record->total_payout   = 0;

        if (!self::isWeekEnterable($isoYear, $isoWeek)) {
            $record->payout_status = PlanWeeklyReturn::STATUS_DRAFT;
        } else {
            $record->payout_status = abs($rate) > 0 ? PlanWeeklyReturn::STATUS_PENDING : PlanWeeklyReturn::STATUS_DRAFT;
        }

        $record->approved_at = null;
        $record->save();

        return $record;
    }

    public static function approveWeeklyReturn(PlanWeeklyReturn $record, int $adminId = 0): PlanWeeklyReturn
    {
        if ($record->payout_status === PlanWeeklyReturn::STATUS_APPROVED) {
            return $record;
        }

        if (!self::isWeekEnterable((int) $record->year, (int) $record->week)) {
            throw new RuntimeException('This weekly return cannot be approved yet.');
        }

        // Weekly approval is chart performance only; no investor wallet credit here.
        $record->payout_status = PlanWeeklyReturn::STATUS_APPROVED;
        $record->approved_at   = now();
        $record->total_payout  = 0;
        $record->save();

        return $record;
    }

    public static function rejectWeeklyReturn(PlanWeeklyReturn $record, int $adminId = 0): PlanWeeklyReturn
    {
        if ($record->payout_status === PlanWeeklyReturn::STATUS_APPROVED) {
            throw new RuntimeException('Approved weekly returns cannot be rejected.');
        }

        $record->payout_status = PlanWeeklyReturn::STATUS_REJECTED;
        $record->approved_at   = null;
        $record->save();

        return $record;
    }

    public static function planUsesMonthlyPerformance(Plan $plan): bool
    {
        return $plan->usesMonthlyPerformanceTracking();
    }

    public static function performanceTrackingLabel(Plan $plan): string
    {
        return self::planUsesMonthlyPerformance($plan) ? 'Monthly Return %' : 'Weekly Return %';
    }

    /** Last N calendar months for admin monthly approval. */
    public static function monthsInRecentMonths(int $months = 5, ?CarbonInterface $from = null): Collection
    {
        $end    = ($from ? Carbon::parse($from) : now())->copy()->startOfMonth();
        $start  = $end->copy()->subMonths($months - 1)->startOfMonth();
        $cursor = $start->copy();
        $items  = collect();

        while ($cursor->lte($end)) {
            $monthStart = $cursor->copy()->startOfMonth()->startOfDay();
            $monthEnd   = $cursor->copy()->endOfMonth()->endOfDay();

            $items->push([
                'year'         => (int) $cursor->year,
                'month'        => (int) $cursor->month,
                'month_start'  => $monthStart,
                'month_end'    => $monthEnd,
                'date_label'   => $cursor->format('F Y'),
                'is_enterable' => self::isMonthEnterable((int) $cursor->year, (int) $cursor->month),
            ]);

            $cursor->addMonth();
        }

        return $items->values();
    }

    /** Years available for admin performance entry (company founded 2022 through current year). */
    public static function performanceEntryYears(?int $fromYear = null): array
    {
        $fromYear = $fromYear ?? self::COMPANY_FOUNDED_YEAR;
        $current  = (int) date('Y');
        $years    = [];

        for ($year = $fromYear; $year <= $current; $year++) {
            $years[] = $year;
        }

        return $years;
    }

    public static function resolvePerformanceEntryYear(?int $year = null): int
    {
        $year  = $year ?? (int) date('Y');
        $years = self::performanceEntryYears();

        return in_array($year, $years, true) ? $year : (int) end($years);
    }

    public static function monthsInCalendarYear(int $year): Collection
    {
        $items = collect();

        for ($month = 1; $month <= 12; $month++) {
            $cursor     = Carbon::create($year, $month, 1);
            $monthStart = $cursor->copy()->startOfMonth()->startOfDay();
            $monthEnd   = $cursor->copy()->endOfMonth()->endOfDay();

            $items->push([
                'year'         => $year,
                'month'        => $month,
                'month_start'  => $monthStart,
                'month_end'    => $monthEnd,
                'date_label'   => $cursor->format('F Y'),
                'is_enterable' => self::isMonthEnterable($year, $month),
            ]);
        }

        return $items->values();
    }

    public static function isMonthEnterable(int $year, int $month, ?CarbonInterface $now = null): bool
    {
        $now      = ($now ?? now())->startOfDay();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        return $now->gt($monthEnd);
    }

    public static function syncMonthlyReturnRecord(Plan $plan, int $year, int $month, ?float $returnPercent): PlanMonthlyReturn
    {
        $record = PlanMonthlyReturn::firstOrNew([
            'plan_id' => $plan->id,
            'year'    => $year,
            'month'   => $month,
        ]);

        if ($record->payout_status === PlanMonthlyReturn::STATUS_APPROVED) {
            return $record;
        }

        $rate = round((float) ($returnPercent ?? 0), 4);

        $record->return_percent = $rate;
        $record->total_payout   = 0;

        if (!self::isMonthEnterable($year, $month)) {
            $record->payout_status = PlanMonthlyReturn::STATUS_DRAFT;
        } else {
            $record->payout_status = abs($rate) > 0 ? PlanMonthlyReturn::STATUS_PENDING : PlanMonthlyReturn::STATUS_DRAFT;
        }

        $record->approved_at = null;
        $record->save();

        return $record;
    }

    public static function approveMonthlyReturn(PlanMonthlyReturn $record, int $adminId = 0): PlanMonthlyReturn
    {
        if ($record->payout_status === PlanMonthlyReturn::STATUS_APPROVED) {
            return $record;
        }

        if (!self::isMonthEnterable((int) $record->year, (int) $record->month)) {
            throw new RuntimeException('This monthly return cannot be approved yet.');
        }

        $record->payout_status = PlanMonthlyReturn::STATUS_APPROVED;
        $record->approved_at   = now();
        $record->total_payout  = 0;
        $record->save();

        return $record;
    }

    public static function rejectMonthlyReturn(PlanMonthlyReturn $record, int $adminId = 0): PlanMonthlyReturn
    {
        if ($record->payout_status === PlanMonthlyReturn::STATUS_APPROVED) {
            throw new RuntimeException('Approved monthly returns cannot be rejected.');
        }

        $record->payout_status = PlanMonthlyReturn::STATUS_REJECTED;
        $record->approved_at   = null;
        $record->save();

        return $record;
    }

    public static function planMonthlyPerformancePoints(Plan $plan, ?int $year = null): Collection
    {
        $yearStart = $year !== null ? Carbon::create($year, 1, 1)->startOfDay() : null;
        $yearEnd   = $year !== null ? Carbon::create($year, 12, 31)->endOfDay() : null;

        return PlanMonthlyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
            ->when($year !== null, fn ($query) => $query->where('year', $year))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->filter(function (PlanMonthlyReturn $row) use ($yearStart, $yearEnd): bool {
                if ($yearStart === null || $yearEnd === null) {
                    return true;
                }

                $monthStart = Carbon::create((int) $row->year, (int) $row->month, 1)->startOfDay();
                $monthEnd   = Carbon::create((int) $row->year, (int) $row->month, 1)->endOfMonth()->endOfDay();

                return $monthStart->lte($yearEnd) && $monthEnd->gte($yearStart);
            })
            ->map(function (PlanMonthlyReturn $row): array {
                $monthStart = Carbon::create((int) $row->year, (int) $row->month, 1)->startOfDay();

                return [
                    'year'           => (int) $row->year,
                    'month'          => (int) $row->month,
                    'period_index'   => (int) $row->month,
                    'label'          => $monthStart->format('M Y'),
                    'date_label'     => $monthStart->format('M Y'),
                    'return_percent' => (float) $row->return_percent,
                ];
            })
            ->values();
    }

    public static function planPerformancePoints(Plan $plan, ?int $year = null): Collection
    {
        return self::planUsesMonthlyPerformance($plan)
            ? self::planMonthlyPerformancePoints($plan, $year)
            : self::planWeeklyPerformancePoints($plan, $year);
    }

    public static function approvedMonthlyReturnsInPeriod(Plan $plan, CarbonInterface|string $periodStart, CarbonInterface|string $periodEnd): Collection
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $end   = Carbon::parse($periodEnd)->endOfDay();

        return PlanMonthlyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
            ->whereBetween('year', [(int) $start->year, (int) $end->year])
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->filter(function (PlanMonthlyReturn $row) use ($start, $end): bool {
                $monthStart = Carbon::create((int) $row->year, (int) $row->month, 1)->startOfDay();
                $monthEnd   = Carbon::create((int) $row->year, (int) $row->month, 1)->endOfMonth()->endOfDay();

                return $monthStart->lte($end) && $monthEnd->gte($start);
            })
            ->values();
    }

    public static function approvedPerformanceInPeriod(Plan $plan, CarbonInterface|string $periodStart, CarbonInterface|string $periodEnd): Collection
    {
        return self::planUsesMonthlyPerformance($plan)
            ? self::approvedMonthlyReturnsInPeriod($plan, $periodStart, $periodEnd)
            : self::approvedWeeklyReturnsInPeriod($plan, $periodStart, $periodEnd);
    }

    public static function periodReturnPercentFromPerformance(Collection $approvedRows, Plan $plan): float
    {
        return round((float) $approvedRows->sum('return_percent'), 4);
    }

    public static function planWeeklyPerformancePoints(Plan $plan, ?int $year = null): Collection
    {
        $yearStart = $year !== null ? Carbon::create($year, 1, 1)->startOfDay() : null;
        $yearEnd   = $year !== null ? Carbon::create($year, 12, 31)->endOfDay() : null;

        return PlanWeeklyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
            ->when($year !== null, fn ($query) => $query->whereBetween('year', [$year - 1, $year + 1]))
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->filter(function (PlanWeeklyReturn $row) use ($yearStart, $yearEnd): bool {
                if ($yearStart === null || $yearEnd === null) {
                    return true;
                }

                $range = self::weekDateRange((int) $row->year, (int) $row->week);

                return $range['start']->lte($yearEnd) && $range['end']->gte($yearStart);
            })
            ->map(function (PlanWeeklyReturn $row): array {
                $range = self::weekDateRange((int) $row->year, (int) $row->week);

                return [
                    'iso_year'       => (int) $row->year,
                    'week'           => (int) $row->week,
                    'period_index'   => (int) $row->week,
                    'label'          => 'W' . $row->week . ' ' . $row->year,
                    'date_label'     => self::weekDateLabel((int) $row->year, (int) $row->week),
                    'week_start'     => $range['start']->toDateString(),
                    'week_end'       => $range['end']->toDateString(),
                    'return_percent' => (float) $row->return_percent,
                ];
            })
            ->values();
    }

    public static function dashboardStrategyCharts(): Collection
    {
        return self::userStrategyChartsForDashboard();
    }

    /** All active strategy charts for the client dashboard (objects with points). */
    public static function userStrategyChartsForDashboard(?int $year = null): Collection
    {
        $year = $year ?? (int) date('Y');

        return self::strategyChartsForYear($year);
    }

    /** All active strategy plans for user selection. */
    public static function activeStrategyPlans(): Collection
    {
        return Plan::query()
            ->where('status', 1)
            ->where('plan_mode', Plan::MODE_STRATEGY)
            ->orderBy('id')
            ->get();
    }

    /** Years with approved performance or uploaded report for one strategy. */
    public static function strategyPerformanceYearsForPlan(int $planId): Collection
    {
        $plan = Plan::query()
            ->where('plan_mode', Plan::MODE_STRATEGY)
            ->where('status', 1)
            ->findOrFail($planId);

        $years = collect();

        if (self::planUsesMonthlyPerformance($plan)) {
            PlanMonthlyReturn::query()
                ->where('plan_id', $plan->id)
                ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
                ->pluck('year')
                ->each(fn ($year) => $years->push((int) $year));
        } else {
            PlanWeeklyReturn::query()
                ->where('plan_id', $plan->id)
                ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
                ->get(['year', 'week'])
                ->each(function (PlanWeeklyReturn $row) use ($years): void {
                    $range = self::weekDateRange((int) $row->year, (int) $row->week);
                    $years->push((int) $range['start']->year, (int) $range['end']->year);
                });
        }

        PlanStrategyReport::query()
            ->where('plan_id', $plan->id)
            ->pluck('year')
            ->each(fn ($year) => $years->push((int) $year));

        return $years->unique()->sortDesc()->values()->map(function (int $year) use ($plan): object {
            $report = PlanStrategyReport::query()
                ->where('plan_id', $plan->id)
                ->where('year', $year)
                ->first();

            return (object) [
                'year'   => $year,
                'chart'  => self::strategyChartForPlan($plan, $year),
                'report' => $report,
            ];
        })->filter(function (object $section): bool {
            return $section->chart->point_count > 0 || $section->report !== null;
        })->values();
    }

    public static function strategyReportFilePath(PlanStrategyReport $report): string
    {
        return assetFilesystemPath(getFilePath('strategyReport') . '/' . $report->file_path);
    }

    /** @deprecated Use strategyPerformanceYearsForPlan() */
    public static function strategyPerformanceByYear(): Collection
    {
        $years = collect();

        PlanWeeklyReturn::query()
            ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
            ->whereHas('plan', fn ($query) => $query->where('status', 1)->where('plan_mode', Plan::MODE_STRATEGY))
            ->get(['year', 'week'])
            ->each(function (PlanWeeklyReturn $row) use ($years): void {
                $range = self::weekDateRange((int) $row->year, (int) $row->week);
                $years->push((int) $range['start']->year, (int) $range['end']->year);
            });

        PlanMonthlyReturn::query()
            ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
            ->whereHas('plan', fn ($query) => $query->where('status', 1)->where('plan_mode', Plan::MODE_STRATEGY))
            ->get(['year', 'month'])
            ->each(function (PlanMonthlyReturn $row) use ($years): void {
                $years->push((int) $row->year);
            });

        return $years->unique()->sortDesc()->values()->map(function (int $year): object {
            return (object) [
                'year'   => $year,
                'charts' => self::strategyChartsForYear($year),
            ];
        })->filter(function (object $section): bool {
            return $section->charts->contains(fn (object $chart): bool => $chart->point_count > 0);
        })->values();
    }

    /** @deprecated Use strategyPerformanceByYear() */
    public static function strategyPerformanceHistory(?int $excludeYear = null): Collection
    {
        return self::strategyPerformanceByYear()
            ->when($excludeYear !== null, fn (Collection $sections) => $sections->filter(
                fn (object $section): bool => (int) $section->year !== $excludeYear
            )->values());
    }

    private static function strategyChartsForYear(int $year): Collection
    {
        return self::activeStrategyPlans()->map(fn (Plan $plan): object => self::strategyChartForPlan($plan, $year));
    }

    public static function strategyChartForPlan(Plan $plan, int $year): object
    {
        $points = self::planPerformancePoints($plan, $year)->map(fn (array $point): object => (object) [
            'rate_percent'   => (float) $point['return_percent'],
            'period_index'   => (int) ($point['period_index'] ?? $point['week'] ?? $point['month'] ?? 0),
            'date_label'     => (string) $point['date_label'],
        ]);

        return (object) [
            'plan'              => $plan,
            'plan_name'         => $plan->name,
            'frequency_label'   => $plan->payoutFrequencyLabel(),
            'point_count'       => $points->count(),
            'week_count'        => $points->count(),
            'ytd_percent'       => round((float) $points->sum('rate_percent'), 4),
            'points'            => $points,
            'year'              => $year,
        ];
    }

    /** Approved period payout % points for Return Analytics (quarterly / semi / yearly). */
    public static function userReturnAnalyticsChart(int|object $user, ?int $year = null): Collection
    {
        $userId = is_object($user) ? (int) ($user->id ?? 0) : (int) $user;
        $year   = $year ?? (int) now()->year;

        if ($userId <= 0) {
            return collect();
        }

        $items = PeriodPayoutItem::query()
            ->where('user_id', $userId)
            ->whereHas('planPeriodReturn', function ($query) use ($year): void {
                $query
                    ->where('year', $year)
                    ->where('payout_status', PlanPeriodReturn::STATUS_APPROVED);
            })
            ->with(['planPeriodReturn.plan', 'invest'])
            ->get()
            ->sortBy(fn (PeriodPayoutItem $item) => $item->planPeriodReturn?->period_end?->format('Y-m-d') ?? '')
            ->values();

        $running = 0.0;

        return $items->map(function (PeriodPayoutItem $item) use (&$running): object {
            $period = $item->planPeriodReturn;
            $plan   = $period?->plan;
            $rate   = round((float) ($item->rate_percent ?: $period?->return_percent ?: 0), 4);
            $running = round($running + $rate, 4);

            $freqLabel = $plan?->payoutFrequencyLabel() ?? '';
            $label     = ($plan ? __($plan->name) . ' · ' : '') . ($period?->periodLabel() ?? '');

            return (object) [
                'label'              => $label,
                'return_percent'     => $rate,
                'cumulative_percent' => $running,
                'frequency_label'    => $freqLabel,
                'period_end'         => $period?->period_end?->format('Y-m-d'),
                'amount'             => round((float) $item->amount, 4),
            ];
        });
    }

    public static function reverseApprovedPeriodReturn(PlanPeriodReturn $periodReturn): PlanPeriodReturn
    {
        if ($periodReturn->payout_status !== PlanPeriodReturn::STATUS_APPROVED) {
            return $periodReturn;
        }

        return DB::transaction(function () use ($periodReturn): PlanPeriodReturn {
            $periodReturn->load(['payoutItems', 'plan']);

            foreach ($periodReturn->payoutItems as $item) {
                $user = User::find($item->user_id);
                if ($user) {
                    $user->interest_wallet = max(0, (float) $user->interest_wallet - (float) $item->amount);
                    $user->save();
                }

                $invest = Invest::find($item->invest_id);
                if ($invest) {
                    $invest->paid = max(0, (float) $invest->paid - (float) $item->amount);
                    $invest->return_rec_time = max(0, (int) $invest->return_rec_time - 1);
                    if ($periodReturn->plan) {
                        $invest->next_time = self::nextInvestPayoutDate($invest, $periodReturn->plan)->toDateTimeString();
                    }
                    $invest->save();
                }

                if ($item->transaction_id) {
                    Transaction::where('id', $item->transaction_id)->delete();
                } else {
                    Transaction::query()
                        ->where('user_id', $item->user_id)
                        ->where('invest_id', $item->invest_id)
                        ->whereIn('remark', ['strategy_period_payout', 'interest'])
                        ->where('amount', $item->amount)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->delete();
                }
            }

            PeriodPayoutItem::where('plan_period_return_id', $periodReturn->id)->delete();

            $periodReturn->payout_status = PlanPeriodReturn::STATUS_PENDING;
            $periodReturn->total_payout  = 0;
            $periodReturn->approved_at   = null;
            $periodReturn->save();

            if ($periodReturn->plan) {
                self::syncDuePeriodReturnRecords($periodReturn->plan);

                return PlanPeriodReturn::query()
                    ->where('plan_id', $periodReturn->plan_id)
                    ->whereDate('payout_date', $periodReturn->payout_date ?? self::periodPayoutDate($periodReturn->period_end))
                    ->first() ?? $periodReturn->fresh();
            }

            return $periodReturn->fresh();
        });
    }

    public static function resetUserApprovedPayouts(int|object $user): int
    {
        $userId = is_object($user) ? (int) ($user->id ?? 0) : (int) $user;

        if ($userId <= 0) {
            return 0;
        }

        $reversed = 0;
        $periodIds = PeriodPayoutItem::query()
            ->where('user_id', $userId)
            ->pluck('plan_period_return_id')
            ->unique();

        foreach ($periodIds as $periodId) {
            $period = PlanPeriodReturn::find($periodId);
            if ($period && $period->payout_status === PlanPeriodReturn::STATUS_APPROVED) {
                self::reverseApprovedPeriodReturn($period);
                $reversed++;
            }
        }

        PeriodPayoutItem::where('user_id', $userId)->delete();

        StrategyPayoutService::syncDuePeriodReturnRecords();

        return $reversed;
    }

    public static function approvedWeeklyReturns(Plan $plan): Collection
    {
        return PlanWeeklyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
            ->orderByDesc('year')
            ->orderByDesc('week')
            ->get();
    }

    public static function averageWeeklyReturn(Plan $plan): float
    {
        $avg = PlanWeeklyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
            ->avg('return_percent');

        return round((float) ($avg ?? 0), 4);
    }

    public static function approvedMonthlyReturns(Plan $plan): Collection
    {
        return PlanMonthlyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
    }

    public static function averageMonthlyReturn(Plan $plan): float
    {
        $avg = PlanMonthlyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanMonthlyReturn::STATUS_APPROVED)
            ->avg('return_percent');

        return round((float) ($avg ?? 0), 4);
    }

    /** Payout is credited on the 1st day after the investment-based period ends. */
    public static function periodPayoutDate(CarbonInterface|string $periodEnd): Carbon
    {
        return Carbon::parse($periodEnd)->startOfDay()->addDay()->startOfMonth()->startOfDay();
    }

    public static function isPayoutEnterable(CarbonInterface|string $payoutDate, ?CarbonInterface $now = null): bool
    {
        $now = ($now ?? now())->startOfDay();

        return $now->gte(Carbon::parse($payoutDate)->startOfDay());
    }

    public static function isPeriodEnterable(CarbonInterface|string $periodEnd, ?CarbonInterface $now = null): bool
    {
        return self::isPayoutEnterable(self::periodPayoutDate($periodEnd), $now);
    }

    public static function planPeriodMonths(Plan $plan): int
    {
        return match ($plan->payout_frequency ?? Plan::FREQ_QUARTERLY) {
            Plan::FREQ_SEMI_ANNUAL => 6,
            Plan::FREQ_YEARLY      => 12,
            default                => 3,
        };
    }

    /** Rolling period anchor: 1st of invest month (before 15th) or 1st of next month (on/after 15th). */
    public static function investPeriodAnchor(Invest $invest): Carbon
    {
        $investDate = Carbon::parse($invest->created_at)->startOfDay();

        if ((int) $investDate->day < 15) {
            return $investDate->copy()->startOfMonth();
        }

        return $investDate->copy()->startOfMonth()->addMonth();
    }

    /**
     * Investment-date payout cycle (not calendar quarters).
     *
     * Quarterly example: invest Feb 1 → Feb 1–Apr 30, payout May 1.
     * Invest Feb 12 (before 15th) → same May 1 payout, days before Feb 12 deducted.
     * Invest Feb 20 (after 15th) → extra Feb 20–28, main Mar 1–May 31, payout Jun 1.
     *
     * @return array{
     *     cycle_index: int,
     *     period_start: Carbon,
     *     period_end: Carbon,
     *     payout_date: Carbon,
     *     main_start: ?Carbon,
     *     main_end: ?Carbon,
     *     extra_start: ?Carbon,
     *     extra_end: ?Carbon,
     *     period_days: int,
     *     is_enterable: bool
     * }|null
     */
    public static function investCycleSchedule(Invest $invest, Plan $plan, int $cycleIndex): ?array
    {
        if ($cycleIndex < 0) {
            return null;
        }

        $months     = self::planPeriodMonths($plan);
        $anchor     = self::investPeriodAnchor($invest);
        $investDate = Carbon::parse($invest->created_at)->startOfDay();

        $periodStart = $anchor->copy()->addMonths($months * $cycleIndex);
        $periodEnd   = $periodStart->copy()->addMonths($months)->subDay()->startOfDay();
        $payoutDate  = self::periodPayoutDate($periodEnd);
        $periodDays  = max(1, $periodStart->diffInDays($periodEnd) + 1);

        $mainStart  = $periodStart->copy();
        $mainEnd    = $periodEnd->copy();
        $extraStart = null;
        $extraEnd   = null;

        if ($cycleIndex === 0) {
            if ((int) $investDate->day < 15) {
                if ($investDate->gt($periodStart)) {
                    $mainStart = $investDate->copy();
                }
            } else {
                $extraStart = $investDate->copy();
                $extraEnd   = $investDate->copy()->endOfMonth()->startOfDay();

                if ($extraEnd->gt($periodEnd)) {
                    $extraEnd = $periodEnd->copy();
                }

                $mainStart = $investDate->copy()->startOfMonth()->addMonth()->startOfDay();

                if ($mainStart->gt($periodEnd)) {
                    $mainStart = null;
                    $mainEnd   = null;
                }
            }
        }

        return [
            'cycle_index'   => $cycleIndex,
            'period_start'  => $periodStart,
            'period_end'    => $periodEnd,
            'payout_date'   => $payoutDate,
            'main_start'    => $mainStart,
            'main_end'      => $mainEnd,
            'extra_start'   => $extraStart,
            'extra_end'     => $extraEnd,
            'period_days'   => $periodDays,
            'is_enterable'  => self::isPayoutEnterable($payoutDate),
        ];
    }

    public static function nextInvestPayoutDate(Invest $invest, Plan $plan, ?CarbonInterface $from = null): Carbon
    {
        $cycle = (int) ($invest->return_rec_time ?? 0);

        while (true) {
            $schedule = self::investCycleSchedule($invest, $plan, $cycle);

            if (!$schedule) {
                break;
            }

            if (!$from || $schedule['payout_date']->gte(Carbon::parse($from)->startOfDay())) {
                return $schedule['payout_date']->copy();
            }

            $cycle++;
        }

        $fallback = self::investPeriodAnchor($invest)->copy()->addMonths(self::planPeriodMonths($plan));

        return self::periodPayoutDate($fallback->subDay());
    }

    public static function nextPeriodEnd(Plan $plan, ?CarbonInterface $from = null): Carbon
    {
        $months = self::planPeriodMonths($plan);
        $point  = ($from ? Carbon::parse($from) : now())->startOfDay();
        $end    = $point->copy()->addMonths($months)->subDay();

        return self::periodPayoutDate($end);
    }

    public static function investCycleIsPaid(Invest $invest, Plan $plan, int $cycleIndex): bool
    {
        $schedule = self::investCycleSchedule($invest, $plan, $cycleIndex);

        if (!$schedule) {
            return false;
        }

        return PeriodPayoutItem::query()
            ->where('invest_id', $invest->id)
            ->whereHas('planPeriodReturn', function ($query) use ($plan, $schedule): void {
                $query
                    ->where('plan_id', $plan->id)
                    ->where('payout_status', PlanPeriodReturn::STATUS_APPROVED)
                    ->whereDate('payout_date', $schedule['payout_date']->toDateString());
            })
            ->exists();
    }

    public static function periodsInYear(Plan $plan, int $year): Collection
    {
        $frequency = $plan->payout_frequency ?? Plan::FREQ_QUARTERLY;
        $periods   = collect();

        if ($frequency === Plan::FREQ_YEARLY) {
            $periods->push(self::periodMeta($plan, $year, 1));
            return $periods;
        }

        if ($frequency === Plan::FREQ_SEMI_ANNUAL) {
            $periods->push(self::periodMeta($plan, $year, 1));
            $periods->push(self::periodMeta($plan, $year, 2));
            return $periods;
        }

        for ($i = 1; $i <= 4; $i++) {
            $periods->push(self::periodMeta($plan, $year, $i));
        }

        return $periods;
    }

    public static function periodMeta(Plan $plan, int $year, int $periodIndex): array
    {
        $frequency = $plan->payout_frequency ?? Plan::FREQ_QUARTERLY;

        if ($frequency === Plan::FREQ_YEARLY) {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year, 12, 31)->endOfDay();

            return self::finishPeriodMeta($plan, $year, 1, $plan->periodLabel($year, 1), $start, $end);
        }

        if ($frequency === Plan::FREQ_SEMI_ANNUAL) {
            $map = [
                1 => [1, 6],
                2 => [7, 12],
            ];
            $index = in_array($periodIndex, [1, 2], true) ? $periodIndex : 1;
            [$startMonth, $endMonth] = $map[$index];
            $start = Carbon::create($year, $startMonth, 1)->startOfDay();
            $end   = Carbon::create($year, $endMonth, 1)->endOfMonth()->endOfDay();

            return self::finishPeriodMeta($plan, $year, $index, $plan->periodLabel($year, $index), $start, $end);
        }

        $quarterMap = [
            1 => [1, 3],
            2 => [4, 6],
            3 => [7, 9],
            4 => [10, 12],
        ];

        $index = in_array($periodIndex, [1, 2, 3, 4], true) ? $periodIndex : 1;
        [$startMonth, $endMonth] = $quarterMap[$index];

        $start = Carbon::create($year, $startMonth, 1)->startOfDay();
        $end   = Carbon::create($year, $endMonth, 1)->endOfMonth()->endOfDay();

        return self::finishPeriodMeta($plan, $year, $index, $plan->periodLabel($year, $index), $start, $end);
    }

    private static function finishPeriodMeta(Plan $plan, int $year, int $periodIndex, string $label, Carbon $start, Carbon $end): array
    {
        $payoutDate = self::periodPayoutDate($end);

        return [
            'year'         => $year,
            'period_index' => $periodIndex,
            'label'        => $label,
            'period_start' => $start->toDateString(),
            'period_end'   => $end->toDateString(),
            'date_label'   => $start->format('M d') . ' - ' . $end->copy()->startOfDay()->format('M d, Y'),
            'payout_date'  => $payoutDate->toDateString(),
            'is_enterable' => self::isPeriodEnterable($end),
        ];
    }

    public static function approvedWeeklyReturnsInPeriod(Plan $plan, CarbonInterface|string $periodStart, CarbonInterface|string $periodEnd): Collection
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $end   = Carbon::parse($periodEnd)->endOfDay();

        $minIsoYear = (int) $start->copy()->subWeeks(1)->isoWeekYear;
        $maxIsoYear = (int) $end->copy()->addWeeks(1)->isoWeekYear;

        return PlanWeeklyReturn::query()
            ->where('plan_id', $plan->id)
            ->where('payout_status', PlanWeeklyReturn::STATUS_APPROVED)
            ->whereBetween('year', [$minIsoYear, $maxIsoYear])
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->filter(function (PlanWeeklyReturn $row) use ($start, $end): bool {
                $range = self::weekDateRange((int) $row->year, (int) $row->week);

                return $range['start']->lte($end) && $range['end']->gte($start);
            })
            ->values();
    }

    public static function periodReturnPercentFromWeekly(Collection $approvedWeeks): float
    {
        return round((float) $approvedWeeks->sum('return_percent'), 4);
    }

    public static function buildPeriodReturnFromWeekly(Plan $plan, int $year, int $periodIndex): array
    {
        $meta  = self::periodMeta($plan, $year, $periodIndex);
        $rows  = self::approvedPerformanceInPeriod($plan, $meta['period_start'], $meta['period_end']);

        return [
            'period_meta'    => $meta,
            'weeks'          => $rows,
            'return_percent' => self::periodReturnPercentFromPerformance($rows, $plan),
        ];
    }

    public static function periodPerformanceBreakdown(PlanPeriodReturn $record): Collection
    {
        $plan = $record->relationLoaded('plan')
            ? $record->plan
            : Plan::find($record->plan_id);

        if (!$plan) {
            return collect();
        }

        $monthly = self::planUsesMonthlyPerformance($plan);

        return self::approvedPerformanceInPeriod($plan, $record->period_start, $record->period_end)
            ->map(function ($row) use ($monthly): object {
                if ($monthly) {
                    return (object) [
                        'label'          => Carbon::create((int) $row->year, (int) $row->month, 1)->format('M Y'),
                        'date_label'     => Carbon::create((int) $row->year, (int) $row->month, 1)->format('M Y'),
                        'return_percent' => (float) $row->return_percent,
                    ];
                }

                return (object) [
                    'year'           => (int) $row->year,
                    'week'           => (int) $row->week,
                    'label'          => 'W' . $row->week . ' ' . $row->year,
                    'date_label'     => self::weekDateLabel((int) $row->year, (int) $row->week),
                    'return_percent' => (float) $row->return_percent,
                ];
            })
            ->values();
    }

    /** @deprecated Use periodPerformanceBreakdown() */
    public static function periodWeeklyBreakdown(PlanPeriodReturn $record): Collection
    {
        return self::periodPerformanceBreakdown($record);
    }

    public static function syncPayoutBatchRecord(
        Plan $plan,
        CarbonInterface|string $payoutDate,
        CarbonInterface|string $periodStart,
        CarbonInterface|string $periodEnd
    ): PlanPeriodReturn {
        $payout   = Carbon::parse($payoutDate)->startOfDay();
        $start    = Carbon::parse($periodStart)->startOfDay();
        $end      = Carbon::parse($periodEnd)->startOfDay();
        $rows     = self::approvedPerformanceInPeriod($plan, $start, $end);
        $rate     = self::periodReturnPercentFromPerformance($rows, $plan);
        $enterable = self::isPayoutEnterable($payout);

        $record = PlanPeriodReturn::firstOrNew([
            'plan_id'     => $plan->id,
            'payout_date' => $payout->toDateString(),
        ]);

        if ($record->payout_status === PlanPeriodReturn::STATUS_APPROVED) {
            return $record;
        }

        if ($record->exists && $record->payout_status === PlanPeriodReturn::STATUS_REJECTED) {
            $record->period_start   = $start->toDateString();
            $record->period_end     = $end->toDateString();
            $record->return_percent = $rate;
            $record->year           = (int) $payout->year;
            $record->period_index   = (int) $payout->format('md');
            $record->save();

            return $record;
        }

        $previousStatus = $record->exists ? $record->payout_status : null;

        $record->year           = (int) $payout->year;
        $record->period_index   = (int) $payout->format('md');
        $record->period_start   = $start->toDateString();
        $record->period_end     = $end->toDateString();
        $record->return_percent = $rate;

        if (!$enterable || $rows->isEmpty()) {
            $record->payout_status = PlanPeriodReturn::STATUS_DRAFT;
            $record->total_payout  = 0;
        } elseif ($previousStatus === PlanPeriodReturn::STATUS_PENDING || abs($rate) >= 0) {
            $record->payout_status = PlanPeriodReturn::STATUS_PENDING;
        } else {
            $record->payout_status = PlanPeriodReturn::STATUS_DRAFT;
            $record->total_payout  = 0;
        }

        $record->approved_at = null;
        $record->save();

        if ($record->payout_status === PlanPeriodReturn::STATUS_PENDING) {
            $preview = self::previewPeriodPayout($record);

            if ($preview['count'] === 0) {
                $record->delete();

                return $record;
            }

            $record->total_payout = $preview['total'];
            $record->save();
        }

        return $record;
    }

    /** Remove leftover calendar batches and empty pending rows. */
    public static function cleanupStalePeriodReturnRecords(?Plan $plan = null): int
    {
        $removed = 0;

        $query = PlanPeriodReturn::query()
            ->whereIn('payout_status', [
                PlanPeriodReturn::STATUS_DRAFT,
                PlanPeriodReturn::STATUS_PENDING,
            ])
            ->whereHas('plan', fn ($q) => $q->where('plan_mode', Plan::MODE_STRATEGY));

        if ($plan) {
            $query->where('plan_id', $plan->id);
        }

        foreach ($query->get() as $record) {
            $preview = self::previewPeriodPayout($record);

            if ($preview['count'] === 0) {
                $record->delete();
                $removed++;
            }
        }

        return $removed;
    }

    public static function refreshInvestNextPayoutTimes(?Plan $plan = null): void
    {
        $query = Invest::query()
            ->where('status', 1)
            ->whereHas('plan', fn ($q) => $q->where('plan_mode', Plan::MODE_STRATEGY));

        if ($plan) {
            $query->where('plan_id', $plan->id);
        }

        foreach ($query->with('plan')->get() as $invest) {
            if (!$invest->plan) {
                continue;
            }

            $invest->next_time = self::nextInvestPayoutDate($invest, $invest->plan)->toDateTimeString();
            $invest->save();
        }
    }

    /** @deprecated Calendar-based — use syncDuePeriodReturnRecords() instead. */
    public static function syncPeriodReturnRecord(Plan $plan, int $year, int $periodIndex): PlanPeriodReturn
    {
        self::syncDuePeriodReturnRecords($plan);
        self::cleanupStalePeriodReturnRecords($plan);

        return PlanPeriodReturn::query()
            ->where('plan_id', $plan->id)
            ->orderByDesc('payout_date')
            ->firstOrNew(['plan_id' => $plan->id, 'payout_date' => now()->toDateString()]);
    }

    /**
     * Auto-queue payout batches when each investment's payout date has passed.
     */
    public static function syncDuePeriodReturnRecords(?Plan $plan = null): int
    {
        $synced  = 0;
        $plans   = $plan
            ? collect([$plan])
            : Plan::query()->where('status', 1)->where('plan_mode', Plan::MODE_STRATEGY)->get();
        $batches = [];

        foreach ($plans as $strategyPlan) {
            $invests = Invest::query()
                ->where('plan_id', $strategyPlan->id)
                ->where('status', 1)
                ->get();

            foreach ($invests as $invest) {
                $cycle    = (int) ($invest->return_rec_time ?? 0);
                $schedule = self::investCycleSchedule($invest, $strategyPlan, $cycle);

                if (!$schedule || !$schedule['is_enterable']) {
                    continue;
                }

                if (self::investCycleIsPaid($invest, $strategyPlan, $cycle)) {
                    continue;
                }

                $rows = self::approvedPerformanceInPeriod(
                    $strategyPlan,
                    $schedule['period_start'],
                    $schedule['period_end']
                );

                if ($rows->isEmpty()) {
                    continue;
                }

                $key = $strategyPlan->id . '|' . $schedule['payout_date']->toDateString();

                if (!isset($batches[$key])) {
                    $batches[$key] = [
                        'plan'        => $strategyPlan,
                        'payout_date' => $schedule['payout_date']->copy(),
                        'period_start'=> $schedule['period_start']->copy(),
                        'period_end'  => $schedule['period_end']->copy(),
                    ];
                } else {
                    if ($schedule['period_start']->lt($batches[$key]['period_start'])) {
                        $batches[$key]['period_start'] = $schedule['period_start']->copy();
                    }
                    if ($schedule['period_end']->gt($batches[$key]['period_end'])) {
                        $batches[$key]['period_end'] = $schedule['period_end']->copy();
                    }
                }
            }
        }

        foreach ($batches as $batch) {
            $existing = PlanPeriodReturn::query()
                ->where('plan_id', $batch['plan']->id)
                ->whereDate('payout_date', $batch['payout_date']->toDateString())
                ->first();

            if ($existing && $existing->payout_status === PlanPeriodReturn::STATUS_APPROVED) {
                continue;
            }

            if ($existing && $existing->payout_status === PlanPeriodReturn::STATUS_REJECTED) {
                continue;
            }

            self::syncPayoutBatchRecord(
                $batch['plan'],
                $batch['payout_date'],
                $batch['period_start'],
                $batch['period_end']
            );
            $synced++;
        }

        self::cleanupStalePeriodReturnRecords($plan);
        self::refreshInvestNextPayoutTimes($plan);

        return $synced;
    }

    public static function advanceInvestAfterPayout(Invest $invest, Plan $plan): void
    {
        $invest->return_rec_time = (int) ($invest->return_rec_time ?? 0) + 1;
        $invest->next_time       = self::nextInvestPayoutDate($invest, $plan)->toDateTimeString();
        $invest->save();
    }

    /**
     * @return array{main: float, extra: float, total: float, rate_percent: float}
     */
    public static function calculateInvestCycleReturnBreakdown(Invest $invest, Plan $plan, int $cycleIndex): array
    {
        $schedule = self::investCycleSchedule($invest, $plan, $cycleIndex);

        if (!$schedule) {
            return ['main' => 0.0, 'extra' => 0.0, 'total' => 0.0, 'rate_percent' => 0.0];
        }

        $amount = (float) ($invest->amount ?? 0);
        if ($amount == 0.0) {
            return ['main' => 0.0, 'extra' => 0.0, 'total' => 0.0, 'rate_percent' => 0.0];
        }

        $rows = self::approvedPerformanceInPeriod(
            $plan,
            $schedule['period_start'],
            $schedule['period_end']
        );
        $rate = self::periodReturnPercentFromPerformance($rows, $plan);

        $main  = 0.0;
        $extra = 0.0;

        if ($schedule['main_start'] && $schedule['main_end'] && $rate != 0.0) {
            $mainDays         = $schedule['main_start']->diffInDays($schedule['main_end']) + 1;
            $fullPeriodPayout = $amount * ($rate / 100);
            $prorationFactor  = min(1, $mainDays / $schedule['period_days']);
            $main             = round($fullPeriodPayout * $prorationFactor, 8);
        }

        if ($schedule['extra_start'] && $schedule['extra_end']) {
            $extra = self::calculateExtraDaysReturn(
                $invest,
                $plan,
                $schedule['extra_start'],
                $schedule['extra_end']
            );
        }

        return [
            'main'         => $main,
            'extra'        => $extra,
            'total'        => round($main + $extra, 8),
            'rate_percent' => $rate,
        ];
    }

    /** @deprecated Use calculateInvestCycleReturnBreakdown() */
    public static function investPeriodEligibility(
        Invest $invest,
        CarbonInterface|string $periodStart,
        CarbonInterface|string $periodEnd
    ): ?array {
        $plan = $invest->relationLoaded('plan') ? $invest->plan : Plan::find($invest->plan_id);

        if (!$plan) {
            return null;
        }

        $schedule = self::investCycleSchedule($invest, $plan, (int) ($invest->return_rec_time ?? 0));

        if (!$schedule) {
            return null;
        }

        return [
            'main_start'  => $schedule['main_start'],
            'main_end'    => $schedule['main_end'],
            'extra_start' => $schedule['extra_start'],
            'extra_end'   => $schedule['extra_end'],
            'period_days' => $schedule['period_days'],
        ];
    }

    public static function calculateExtraDaysReturn(
        Invest $invest,
        Plan $plan,
        CarbonInterface|string $rangeStart,
        CarbonInterface|string $rangeEnd
    ): float {
        return self::planUsesMonthlyPerformance($plan)
            ? self::calculateMonthlyRangeReturn($invest, $plan, $rangeStart, $rangeEnd)
            : self::calculateWeeklyRangeReturn($invest, $plan, $rangeStart, $rangeEnd);
    }

    public static function calculateMonthlyRangeReturn(
        Invest $invest,
        Plan $plan,
        CarbonInterface|string $rangeStart,
        CarbonInterface|string $rangeEnd
    ): float {
        $amount = (float) ($invest->amount ?? 0);
        if ($amount == 0.0) {
            return 0.0;
        }

        $start = Carbon::parse($rangeStart)->startOfDay();
        $end   = Carbon::parse($rangeEnd)->startOfDay();

        if ($end->lt($start)) {
            return 0.0;
        }

        $months = self::approvedMonthlyReturnsInPeriod($plan, $start, $end);
        $total  = 0.0;

        foreach ($months as $monthRow) {
            $monthStart = Carbon::create((int) $monthRow->year, (int) $monthRow->month, 1)->startOfDay();
            $monthEnd   = Carbon::create((int) $monthRow->year, (int) $monthRow->month, 1)->endOfMonth()->startOfDay();
            $overlapStart = $monthStart->max($start);
            $overlapEnd   = $monthEnd->min($end);

            if ($overlapStart->gt($overlapEnd)) {
                continue;
            }

            $overlapDays = $overlapStart->diffInDays($overlapEnd) + 1;
            $monthDays   = max(1, $monthStart->daysInMonth);
            $fraction    = min(1.0, $overlapDays / $monthDays);
            $total      += $amount * ((float) $monthRow->return_percent / 100) * $fraction;
        }

        return round($total, 8);
    }

    public static function calculateWeeklyRangeReturn(
        Invest $invest,
        Plan $plan,
        CarbonInterface|string $rangeStart,
        CarbonInterface|string $rangeEnd
    ): float {
        $amount = (float) ($invest->amount ?? 0);
        if ($amount == 0.0) {
            return 0.0;
        }

        $start = Carbon::parse($rangeStart)->startOfDay();
        $end   = Carbon::parse($rangeEnd)->startOfDay();

        if ($end->lt($start)) {
            return 0.0;
        }

        $weeks = self::approvedWeeklyReturnsInPeriod($plan, $start, $end);
        $total = 0.0;

        foreach ($weeks as $weekRow) {
            $range        = self::weekDateRange((int) $weekRow->year, (int) $weekRow->week);
            $overlapStart = $range['start']->startOfDay()->max($start);
            $overlapEnd   = $range['end']->startOfDay()->min($end);

            if ($overlapStart->gt($overlapEnd)) {
                continue;
            }

            $overlapDays = $overlapStart->diffInDays($overlapEnd) + 1;
            $fraction    = min(1.0, $overlapDays / 7);
            $total      += $amount * ((float) $weekRow->return_percent / 100) * $fraction;
        }

        return round($total, 8);
    }

    /**
     * @return array{main: float, extra: float, total: float}
     */
    public static function calculateInvestPeriodReturnBreakdown(
        Invest $invest,
        Plan $plan,
        float $periodReturnPercent,
        CarbonInterface|string $periodStart,
        CarbonInterface|string $periodEnd
    ): array {
        $breakdown = self::calculateInvestCycleReturnBreakdown(
            $invest,
            $plan,
            (int) ($invest->return_rec_time ?? 0)
        );

        return [
            'main'  => $breakdown['main'],
            'extra' => $breakdown['extra'],
            'total' => $breakdown['total'],
        ];
    }

    public static function calculateInvestPeriodReturn(
        Invest $invest,
        Plan $plan,
        float $periodReturnPercent,
        CarbonInterface|string $periodStart,
        CarbonInterface|string $periodEnd
    ): float {
        return self::calculateInvestPeriodReturnBreakdown(
            $invest,
            $plan,
            $periodReturnPercent,
            $periodStart,
            $periodEnd
        )['total'];
    }

    public static function previewPeriodPayout(PlanPeriodReturn $record): array
    {
        $plan = $record->relationLoaded('plan')
            ? $record->plan
            : Plan::find($record->plan_id);

        if (!$plan || !$record->payout_date) {
            return ['count' => 0, 'total' => 0.0, 'lines' => []];
        }

        $payoutDate = Carbon::parse($record->payout_date)->startOfDay();

        $invests = Invest::with('user')
            ->where('plan_id', $record->plan_id)
            ->where('status', 1)
            ->orderBy('created_at')
            ->get();

        $lines = [];
        $total = 0.0;

        foreach ($invests as $invest) {
            $cycle    = (int) ($invest->return_rec_time ?? 0);
            $schedule = self::investCycleSchedule($invest, $plan, $cycle);

            if (!$schedule || $schedule['payout_date']->toDateString() !== $payoutDate->toDateString()) {
                continue;
            }

            if (self::investCycleIsPaid($invest, $plan, $cycle)) {
                continue;
            }

            $breakdown = self::calculateInvestCycleReturnBreakdown($invest, $plan, $cycle);
            $amount    = $breakdown['total'];

            if ($amount == 0.0) {
                continue;
            }

            $lines[] = [
                'user'         => $invest->user,
                'invest'       => $invest,
                'amount'       => $amount,
                'main_amount'  => $breakdown['main'],
                'extra_amount' => $breakdown['extra'],
                'rate_percent' => $breakdown['rate_percent'],
            ];

            $total += $amount;
        }

        usort($lines, static fn (array $a, array $b): int => $b['amount'] <=> $a['amount']);

        return [
            'count' => count($lines),
            'total' => round($total, 8),
            'lines' => $lines,
        ];
    }

    public static function approvePeriodReturn(PlanPeriodReturn $record, int $adminId = 0): PlanPeriodReturn
    {
        return DB::transaction(function () use ($record): PlanPeriodReturn {
            $lockedRecord = PlanPeriodReturn::query()
                ->with('plan')
                ->lockForUpdate()
                ->findOrFail($record->id);

            if ($lockedRecord->payout_status === PlanPeriodReturn::STATUS_APPROVED) {
                return $lockedRecord;
            }

            if ($lockedRecord->payout_status !== PlanPeriodReturn::STATUS_PENDING) {
                throw new RuntimeException('This period return cannot be approved.');
            }

            if (!self::isPayoutEnterable($lockedRecord->payout_date ?? self::periodPayoutDate($lockedRecord->period_end))) {
                throw new RuntimeException('Period payout is available on the scheduled payout date.');
            }

            $preview = self::previewPeriodPayout($lockedRecord);
            $setting = gs();
            $total   = 0.0;

            PeriodPayoutItem::where('plan_period_return_id', $lockedRecord->id)->delete();

            foreach ($preview['lines'] as $line) {
                /** @var Invest $invest */
                $invest = $line['invest'];
                $user   = $line['user'];
                $amount = (float) $line['amount'];

                if (!$user || $amount == 0.0) {
                    continue;
                }

                $user->interest_wallet += $amount;
                $user->save();

                $trx = getTrx();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->invest_id    = $invest->id;
                $transaction->amount       = $amount;
                $transaction->charge       = 0;
                $transaction->post_balance = $user->interest_wallet;
                $transaction->trx_type     = $amount >= 0 ? '+' : '-';
                $transaction->trx          = $trx;
                $transaction->wallet_type  = 'interest_wallet';
                $transaction->remark       = 'strategy_period_payout';
                $transaction->details      = showAmount($amount) . ' ' . $setting->cur_text . ' period payout from ' . $lockedRecord->plan->name . ' (' . $lockedRecord->periodLabel() . ')';
                $transaction->save();

                PeriodPayoutItem::create([
                    'plan_period_return_id' => $lockedRecord->id,
                    'user_id'               => $user->id,
                    'invest_id'             => $invest->id,
                    'transaction_id'        => $transaction->id,
                    'amount'                => $amount,
                    'rate_percent'          => (float) ($line['rate_percent'] ?? $lockedRecord->return_percent),
                ]);

                if ($amount > 0 && (int) ($setting->invest_return_commission ?? 0) === 1) {
                    HyipLab::levelCommission($user, $amount, 'invest_return_commission', $trx, $setting);
                }

                $invest->paid += $amount;
                self::advanceInvestAfterPayout($invest, $lockedRecord->plan);

                $total += $amount;
            }

            $lockedRecord->payout_status = PlanPeriodReturn::STATUS_APPROVED;
            $lockedRecord->approved_at   = now();
            $lockedRecord->total_payout  = round($total, 8);
            $lockedRecord->save();

            return $lockedRecord->fresh(['plan', 'payoutItems.user', 'payoutItems.invest']);
        });
    }

    public static function rejectPeriodReturn(PlanPeriodReturn $record, int $adminId = 0): PlanPeriodReturn
    {
        if ($record->payout_status === PlanPeriodReturn::STATUS_APPROVED) {
            throw new RuntimeException('Approved period returns cannot be rejected.');
        }

        $record->payout_status = PlanPeriodReturn::STATUS_REJECTED;
        $record->approved_at   = null;
        $record->total_payout  = 0;
        $record->save();

        return $record;
    }

    public static function pendingWeeklyCount(?Plan $plan = null): int
    {
        $query = PlanWeeklyReturn::query()->where('payout_status', PlanWeeklyReturn::STATUS_PENDING);

        if ($plan) {
            $query->where('plan_id', $plan->id);
        }

        return (int) $query->count();
    }

    public static function pendingPeriodCount(?Plan $plan = null): int
    {
        $query = PlanPeriodReturn::query()->where('payout_status', PlanPeriodReturn::STATUS_PENDING);

        if ($plan) {
            $query->where('plan_id', $plan->id);
        }

        return (int) $query->count();
    }

    public static function userYearToDateReturn(int|object $user, ?int $year = null): ?object
    {
        $userId = is_object($user) ? (int) ($user->id ?? 0) : (int) $user;
        $targetYear = $year ?? (int) now()->year;

        if ($userId <= 0) {
            return null;
        }

        $items = PeriodPayoutItem::query()
            ->where('user_id', $userId)
            ->whereHas('planPeriodReturn', function ($query) use ($targetYear): void {
                $query
                    ->where('year', $targetYear)
                    ->where('payout_status', PlanPeriodReturn::STATUS_APPROVED);
            })
            ->with('planPeriodReturn')
            ->get();

        if ($items->isEmpty()) {
            return null;
        }

        $totalInvested = (float) Invest::query()
            ->where('user_id', $userId)
            ->where('status', 1)
            ->whereHas('plan', fn ($q) => $q->where('plan_mode', Plan::MODE_STRATEGY))
            ->sum('amount');

        $totalPercent = 0.0;
        $seenPeriods  = [];

        foreach ($items as $item) {
            $periodId = (int) $item->plan_period_return_id;
            if (isset($seenPeriods[$periodId])) {
                continue;
            }
            $seenPeriods[$periodId] = true;

            $rate = (float) ($item->rate_percent ?: optional($item->planPeriodReturn)->return_percent ?: 0);
            $investAmount = (float) Invest::query()
                ->where('user_id', $userId)
                ->where('plan_id', optional($item->planPeriodReturn)->plan_id)
                ->where('status', 1)
                ->sum('amount');

            if ($totalInvested > 0 && $investAmount > 0) {
                $totalPercent += $rate * ($investAmount / $totalInvested);
            } else {
                $totalPercent += $rate;
            }
        }

        return (object) [
            'year'           => $targetYear,
            'total_percent'  => round($totalPercent, 4),
            'payout_count'   => count($seenPeriods),
        ];
    }

    public static function userPortfolioReturnChart(int|object $user, ?int $year = null): array
    {
        $userId = is_object($user) ? (int) ($user->id ?? 0) : (int) $user;
        $targetYear = $year ?? (int) now()->year;

        $labels = [];
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($targetYear, $month, 1)->format('M');
            $monthly[$month] = 0.0;
        }

        if ($userId > 0) {
            $items = PeriodPayoutItem::query()
                ->where('user_id', $userId)
                ->whereHas('planPeriodReturn', function ($query) use ($targetYear): void {
                    $query
                        ->where('year', $targetYear)
                        ->where('payout_status', PlanPeriodReturn::STATUS_APPROVED);
                })
                ->with('planPeriodReturn')
                ->get();

            foreach ($items as $item) {
                $periodEnd = optional($item->planPeriodReturn)->period_end;
                if (!$periodEnd) {
                    continue;
                }
                $month = (int) Carbon::parse($periodEnd)->month;
                $monthly[$month] += (float) $item->amount;
            }
        }

        $periodSeries = [];
        $cumulativeSeries = [];
        $running = 0.0;
        foreach ($monthly as $amount) {
            $amount = round((float) $amount, 4);
            $periodSeries[] = $amount;
            $running += $amount;
            $cumulativeSeries[] = round($running, 4);
        }

        return [
            'year'   => $targetYear,
            'labels' => $labels,
            'series' => [
                'period_returns'     => $periodSeries,
                'cumulative_returns' => $cumulativeSeries,
            ],
            'total'  => round(array_sum($periodSeries), 4),
        ];
    }
}
