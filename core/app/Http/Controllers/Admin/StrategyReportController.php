<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\StrategyPayoutService;
use App\Models\Plan;
use App\Models\PlanStrategyReport;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StrategyReportController extends Controller
{
    public function index()
    {
        $pageTitle  = 'Strategy Reports';
        $plans      = StrategyPayoutService::activeStrategyPlans();
        $entryYears = StrategyPayoutService::performanceEntryYears();
        $reports    = PlanStrategyReport::with('plan')
            ->orderByDesc('year')
            ->orderBy('plan_id')
            ->get();

        return view('admin.strategy.reports', compact('pageTitle', 'plans', 'entryYears', 'reports'));
    }

    public function store(Request $request)
    {
        $entryYears = StrategyPayoutService::performanceEntryYears();

        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'year'    => 'required|integer|in:' . implode(',', $entryYears),
            'report'  => ['required', 'file', 'max:10240', new FileTypeValidate(['pdf'])],
        ]);

        $plan = Plan::where('plan_mode', Plan::MODE_STRATEGY)->findOrFail($request->plan_id);

        $existing = PlanStrategyReport::where('plan_id', $plan->id)
            ->where('year', $request->year)
            ->first();

        $directory = assetFilesystemPath(getFilePath('strategyReport') . '/' . $plan->id . '/' . $request->year);
        $oldFile     = $existing ? basename($existing->file_path) : null;

        try {
            $filename = fileUploader($request->file('report'), $directory, null, $oldFile);
        } catch (\Exception $exception) {
            $notify[] = ['error', 'Could not upload the PDF report.'];
            return back()->withNotify($notify);
        }

        $report = $existing ?? new PlanStrategyReport();
        $report->plan_id       = $plan->id;
        $report->year          = (int) $request->year;
        $report->file_path     = $plan->id . '/' . $request->year . '/' . $filename;
        $report->original_name = $request->file('report')->getClientOriginalName();
        $report->uploaded_by   = auth('admin')->id();
        $report->save();

        $notify[] = ['success', 'Strategy report uploaded successfully.'];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        $report = PlanStrategyReport::findOrFail($id);
        $path   = StrategyPayoutService::strategyReportFilePath($report);

        if (File::exists($path)) {
            File::delete($path);
        }

        $report->delete();

        $notify[] = ['success', 'Strategy report deleted successfully.'];
        return back()->withNotify($notify);
    }
}
