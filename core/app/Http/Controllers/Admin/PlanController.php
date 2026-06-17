<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\TimeSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index()
    {
        $pageTitle = "Investment Plans & Strategies";
        $plans     = Plan::with('timeSetting')->orderBy('id', 'desc')->get();
        $times     = TimeSetting::where('status', 1)->get();
        return view('admin.plan.index', compact('pageTitle', 'plans', 'times'));
    }

    public function store(Request $request)
    {
        $this->validation($request);
        $plan = new Plan();
        $this->saveData($plan, $request);

        $notify[] = ['success', ($request->plan_mode ?? 0) == Plan::MODE_STRATEGY ? 'Strategy created successfully' : 'Plan added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);
        $plan = Plan::findOrFail($id);
        $this->saveData($plan, $request);

        $notify[] = ['success', $plan->isStrategy() ? 'Strategy updated successfully' : 'Plan updated successfully'];
        return back()->withNotify($notify);
    }

    protected function saveData($plan, $request)
    {
        $plan->name         = $request->name;
        $plan->minimum      = $request->minimum ?? 0;
        $plan->maximum      = $request->maximum ?? 0;
        $plan->fixed_amount = $request->amount ?? 0;
        $plan->featured     = $request->featured ? 1 : 0;
        $plan->plan_mode    = (int) ($request->plan_mode ?? Plan::MODE_LEGACY);

        if ($plan->plan_mode === Plan::MODE_STRATEGY) {
            $weeklyTime = TimeSetting::where('status', 1)->orderBy('time')->first();
            $plan->interest          = 0;
            $plan->interest_type     = 1;
            $plan->time_setting_id   = $weeklyTime?->id ?? $request->time;
            $plan->payout_frequency  = $request->payout_frequency ?? Plan::FREQ_QUARTERLY;
            $plan->capital_back      = 0;
            $plan->lifetime          = 1;
            $plan->repeat_time       = 0;
            $plan->compound_interest = 0;
            $plan->hold_capital      = 0;
        } else {
            $plan->interest          = $request->interest;
            $plan->interest_type     = $request->interest_type == 1 ? 1 : 0;
            $plan->time_setting_id   = $request->time;
            $plan->capital_back      = $request->capital_back ?? 0;
            $plan->lifetime          = $request->return_type == 1 ? 1 : 0;
            $plan->repeat_time       = $request->repeat_time ?? 0;
            $plan->compound_interest = $request->compound_interest ? 1 : 0;
            $plan->hold_capital      = $request->hold_capital ? 1 : 0;
        }

        $plan->save();
    }

    protected function validation($request)
    {
        $isStrategy = (int) ($request->plan_mode ?? 0) === Plan::MODE_STRATEGY;

        if ($isStrategy) {
            $request->validate([
                'name'             => 'required',
                'plan_mode'        => 'required|in:1',
                'payout_frequency' => 'required|in:quarterly,semi_annual,yearly',
                'invest_type'      => 'required|in:1,2',
                'minimum'          => 'nullable|required_if:invest_type,1|gt:0',
                'maximum'          => 'nullable|required_if:invest_type,1|gt:minimum',
                'amount'           => 'nullable|required_if:invest_type,2|gt:0',
            ]);
            return;
        }

        $request->validate([
            'name'          => 'required',
            'invest_type'   => 'required|in:1,2',
            'interest_type' => 'required|in:1,2',
            'interest'      => 'required|numeric|gt:0',
            'time'          => 'required|integer|gt:0',
            'return_type'   => 'required|integer|in:1,0',
            'minimum'       => 'nullable|required_if:invest_type,1|gt:0',
            'maximum'       => 'nullable|required_if:invest_type,1|gt:minimum',
            'amount'        => 'nullable|required_if:invest_type,2|gt:0',
            'repeat_time'   => 'nullable|required_if:return_type,2|integer|gt:0',
            'capital_back'  => 'nullable|required_if:return_type,2|in:1,0',
        ]);

        if ($request->compound_interest && ((!$request->capital_back && !$request->return_type) || $request->interest_type == 2)) {
            throw ValidationException::withMessages(['error' => 'For compound interest, a lifetime plan or capital return and a percentage-based interest rate are required.']);
        }

        if ($request->hold_capital && !$request->capital_back) {
            throw ValidationException::withMessages(['error' => 'When hold capital is enabled, capital back is required.']);
        }
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }
}
