<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\StrategyPayoutService;
use App\Models\Plan;
use App\Models\PlanPeriodReturn;
use App\Models\PlanMonthlyReturn;
use App\Models\PlanWeeklyReturn;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function weeklyReturns($planId)
    {
        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);

        if ($plan->usesMonthlyPerformanceTracking()) {
            return redirect()->route('admin.strategy.monthly.returns', $plan->id);
        }

        $pageTitle    = 'Weekly Performance — ' . $plan->name;
        $entryYears   = StrategyPayoutService::performanceEntryYears();
        $year         = StrategyPayoutService::resolvePerformanceEntryYear((int) request('year'));
        $weeks        = StrategyPayoutService::weeksInCalendarYear($year);
        $isoYears     = $weeks->pluck('iso_year')->unique()->all();

        $savedReturns = PlanWeeklyReturn::where('plan_id', $plan->id)
            ->whereIn('year', $isoYears)
            ->get()
            ->keyBy(fn ($row) => $row->year . '-' . $row->week);

        $quarters = StrategyPayoutService::weeksGroupedByMonth($year);

        return view('admin.strategy.weekly_returns', compact('pageTitle', 'plan', 'entryYears', 'year', 'savedReturns', 'quarters'));
    }

    public function saveWeeklyReturns(Request $request, $planId)
    {
        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);

        $entryYears = StrategyPayoutService::performanceEntryYears();
        $year       = StrategyPayoutService::resolvePerformanceEntryYear((int) $request->input('year'));

        $request->validate([
            'year'      => 'required|integer|in:' . implode(',', $entryYears),
            'returns'   => 'nullable|array',
            'returns.*' => 'nullable|numeric|between:-100,100',
        ]);

        foreach (StrategyPayoutService::weeksInCalendarYear($year) as $weekMeta) {
            $fieldKey = $weekMeta['iso_year'] . '_' . $weekMeta['week'];

            if (!array_key_exists($fieldKey, $request->returns ?? [])) {
                continue;
            }

            if (!$weekMeta['is_enterable']) {
                continue;
            }

            $rate = $request->returns[$fieldKey];

            StrategyPayoutService::syncWeeklyReturnRecord(
                $plan,
                $weekMeta['iso_year'],
                $weekMeta['week'],
                $rate === null || $rate === '' ? 0 : (float) $rate
            );
        }

        $notify[] = ['success', 'Weekly return rates saved. Approve each week to publish live performance on charts.'];
        return redirect()->route('admin.strategy.weekly.returns', ['planId' => $plan->id, 'year' => $year])->withNotify($notify);
    }

    public function approveWeeklyReturn($id)
    {
        $record = PlanWeeklyReturn::with('plan')->findOrFail($id);
        StrategyPayoutService::approveWeeklyReturn($record, auth('admin')->id());

        $range = StrategyPayoutService::weekDateRange((int) $record->year, (int) $record->week);
        $year  = StrategyPayoutService::resolvePerformanceEntryYear(
            (int) request('year') ?: (int) $range['start']->year
        );

        $notify[] = ['success', $record->weekLabel() . ' performance approved — visible on strategy performance charts'];
        return redirect()->route('admin.strategy.weekly.returns', ['planId' => $record->plan_id, 'year' => $year])->withNotify($notify);
    }

    public function rejectWeeklyReturn($id)
    {
        $record = PlanWeeklyReturn::findOrFail($id);
        StrategyPayoutService::rejectWeeklyReturn($record, auth('admin')->id());

        $notify[] = ['success', 'Weekly performance return rejected'];
        return back()->withNotify($notify);
    }

    public function monthlyReturns($planId)
    {
        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);

        if (!$plan->usesMonthlyPerformanceTracking()) {
            return redirect()->route('admin.strategy.weekly.returns', $plan->id);
        }

        $pageTitle  = 'Monthly Performance — ' . $plan->name;
        $entryYears = StrategyPayoutService::performanceEntryYears();
        $year       = StrategyPayoutService::resolvePerformanceEntryYear((int) request('year'));
        $periods    = StrategyPayoutService::monthsInCalendarYear($year);

        $savedReturns = PlanMonthlyReturn::where('plan_id', $plan->id)
            ->where('year', $year)
            ->get()
            ->keyBy(fn ($row) => $row->year . '-' . $row->month);

        return view('admin.strategy.monthly_returns', compact('pageTitle', 'plan', 'entryYears', 'year', 'periods', 'savedReturns'));
    }

    public function saveMonthlyReturns(Request $request, $planId)
    {
        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);

        $entryYears = StrategyPayoutService::performanceEntryYears();
        $year       = StrategyPayoutService::resolvePerformanceEntryYear((int) $request->input('year'));

        $request->validate([
            'year'      => 'required|integer|in:' . implode(',', $entryYears),
            'returns'   => 'nullable|array',
            'returns.*' => 'nullable|numeric|between:-100,100',
        ]);

        foreach (StrategyPayoutService::monthsInCalendarYear($year) as $monthMeta) {
            $fieldKey = $monthMeta['year'] . '_' . $monthMeta['month'];

            if (!array_key_exists($fieldKey, $request->returns ?? [])) {
                continue;
            }

            if (!$monthMeta['is_enterable']) {
                continue;
            }

            $rate = $request->returns[$fieldKey];

            StrategyPayoutService::syncMonthlyReturnRecord(
                $plan,
                $monthMeta['year'],
                $monthMeta['month'],
                $rate === null || $rate === '' ? 0 : (float) $rate
            );
        }

        $notify[] = ['success', 'Monthly return rates saved. Approve each month to publish live performance on charts.'];
        return redirect()->route('admin.strategy.monthly.returns', ['planId' => $plan->id, 'year' => $year])->withNotify($notify);
    }

    public function approveMonthlyReturn($id)
    {
        $record = PlanMonthlyReturn::with('plan')->findOrFail($id);
        StrategyPayoutService::approveMonthlyReturn($record, auth('admin')->id());

        $year = StrategyPayoutService::resolvePerformanceEntryYear(
            (int) request('year') ?: (int) $record->year
        );

        $notify[] = ['success', $record->monthLabel() . ' performance approved — visible on strategy performance charts'];
        return redirect()->route('admin.strategy.monthly.returns', ['planId' => $record->plan_id, 'year' => $year])->withNotify($notify);
    }

    public function rejectMonthlyReturn($id)
    {
        $record = PlanMonthlyReturn::findOrFail($id);
        StrategyPayoutService::rejectMonthlyReturn($record, auth('admin')->id());

        $notify[] = ['success', 'Monthly performance return rejected'];
        return back()->withNotify($notify);
    }

    public function periodReturns($planId)
    {
        $plan      = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);
        $pageTitle = 'Period Returns — ' . $plan->name;
        $year      = (int) request('year', date('Y'));

        StrategyPayoutService::syncDuePeriodReturnRecords($plan);

        $batches = PlanPeriodReturn::where('plan_id', $plan->id)
            ->whereYear('payout_date', $year)
            ->where(function ($query): void {
                $query
                    ->where('payout_status', PlanPeriodReturn::STATUS_APPROVED)
                    ->orWhere(function ($pending): void {
                        $pending
                            ->where('payout_status', PlanPeriodReturn::STATUS_PENDING)
                            ->where('total_payout', '>', 0);
                    });
            })
            ->orderByDesc('payout_date')
            ->get();

        return view('admin.strategy.period_returns', compact('pageTitle', 'plan', 'year', 'batches'));
    }

    public function savePeriodReturns(Request $request, $planId)
    {
        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($planId);
        StrategyPayoutService::syncDuePeriodReturnRecords($plan);

        $notify[] = ['success', 'Due payout batches refreshed from active investments.'];
        return back()->withNotify($notify);
    }

    public function approvePeriodReturn($id)
    {
        $record = PlanPeriodReturn::with('plan')->findOrFail($id);
        StrategyPayoutService::approvePeriodReturn($record, auth('admin')->id());

        $notify[] = ['success', $record->periodLabel() . ' payout approved and credited to investors'];
        return back()->withNotify($notify);
    }

    public function rejectPeriodReturn($id)
    {
        $record = PlanPeriodReturn::findOrFail($id);
        StrategyPayoutService::rejectPeriodReturn($record, auth('admin')->id());

        $notify[] = ['success', 'Period return rejected'];
        return back()->withNotify($notify);
    }

    public function payouts()
    {
        StrategyPayoutService::syncDuePeriodReturnRecords();

        $pageTitle = 'Strategy Payout Approvals';
        $status    = request('status', 'pending');

        $periodReturns = PlanPeriodReturn::with('plan')
            ->when($status === 'pending', fn ($q) => $q->where('total_payout', '>', 0))
            ->when($status !== 'all' && $status !== 'pending', fn ($q) => $q->where('payout_status', $status))
            ->when($status === 'pending', fn ($q) => $q->where('payout_status', PlanPeriodReturn::STATUS_PENDING))
            ->orderByDesc('payout_date')
            ->paginate(getPaginate());

        $pendingCount = StrategyPayoutService::pendingPeriodCount();

        return view('admin.strategy.payouts', compact('pageTitle', 'periodReturns', 'status', 'pendingCount'));
    }

    public function payoutDetails($id)
    {
        $record = PlanPeriodReturn::with(['plan', 'payoutItems.user', 'payoutItems.invest'])->findOrFail($id);

        if ($record->payout_status !== PlanPeriodReturn::STATUS_APPROVED && $record->plan) {
            StrategyPayoutService::syncPayoutBatchRecord(
                $record->plan,
                $record->payout_date ?? StrategyPayoutService::periodPayoutDate($record->period_end),
                $record->period_start,
                $record->period_end
            );
            $record->refresh();
        }

        $pageTitle = $record->periodLabel() . ' — ' . $record->plan->name;
        $preview   = $record->isApprovable()
            ? StrategyPayoutService::previewPeriodPayout($record)
            : ['total' => $record->total_payout, 'lines' => [], 'count' => $record->payoutItems->count()];
        $weeklyBreakdown = StrategyPayoutService::periodWeeklyBreakdown($record);

        return view('admin.strategy.payout_details', compact('pageTitle', 'record', 'preview', 'weeklyBreakdown'));
    }
}
