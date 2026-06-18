<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\StrategyPayoutService;
use App\Models\Plan;
use App\Models\PlanStrategyReport;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function dashboardAnalytics()
    {
        $user = auth()->user();
        $year = (int) date('Y');

        $data = [
            'chart_data'          => StrategyPayoutService::userReturnAnalyticsChart($user, $year)->values(),
            'strategy_charts'     => StrategyPayoutService::userStrategyChartsForDashboard($year)->values(),
            'strategy_chart_year' => $year,
            'year_to_date_return' => StrategyPayoutService::userYearToDateReturn($user, $year),
        ];

        return getResponse('strategy_analytics', 'success', 'Strategy analytics data', $data);
    }

    public function performance(Request $request)
    {
        $plans        = StrategyPayoutService::activeStrategyPlans();
        $selectedPlan = null;
        $yearSections = collect();
        $planId       = (int) $request->query('plan_id');

        if ($planId > 0) {
            $selectedPlan = $plans->firstWhere('id', $planId);

            if ($selectedPlan) {
                $yearSections = StrategyPayoutService::strategyPerformanceYearsForPlan($selectedPlan->id);
            }
        }

        return getResponse('strategy_performance', 'success', 'Strategy performance data', [
            'plans'         => $plans,
            'selected_plan' => $selectedPlan,
            'year_sections' => $yearSections->values(),
        ]);
    }

    public function report($planId, $year)
    {
        $plan = Plan::query()
            ->where('plan_mode', Plan::MODE_STRATEGY)
            ->where('status', 1)
            ->findOrFail($planId);

        $report = PlanStrategyReport::query()
            ->where('plan_id', $plan->id)
            ->where('year', (int) $year)
            ->firstOrFail();

        $path = StrategyPayoutService::strategyReportFilePath($report);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $report->displayName() . '"',
        ]);
    }
}
