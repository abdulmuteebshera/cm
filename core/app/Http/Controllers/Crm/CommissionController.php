<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmClient;
use App\Models\Crm\CrmCommission;
use App\Models\Crm\CrmStaff;
use App\Support\CrmOfficerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    public function index()
    {
        $pageTitle = 'Commission Desk';
        $staff     = Auth::guard('crm')->user();
        $query     = CrmCommission::with(['staff', 'client'])->orderByDesc('id');

        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $query->where('staff_id', $staff->id);
        }

        $commissions = $query->paginate(getPaginate());
        $agents      = CrmStaff::orderBy('name')->get();
        $clients     = $staff->portal() === 'agent'
            ? CrmClient::where('owner_staff_id', $staff->id)->orderBy('name')->get()
            : CrmClient::orderBy('name')->get();

        $scope = CrmCommission::query();
        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $scope->where('staff_id', $staff->id);
        }

        $summary = [
            'upfront'   => (float) (clone $scope)->where('rate_percent', CrmOfficerProgram::UPFRONT_RATE)->sum('commission_amount'),
            'retention' => (float) (clone $scope)->where('rate_percent', CrmOfficerProgram::RETENTION_RATE)->sum('commission_amount'),
            'pending'   => (float) (clone $scope)->where('status', 'pending')->sum('commission_amount'),
            'paid'      => (float) (clone $scope)->where('status', 'paid')->sum('commission_amount'),
            'upfront_rate'   => CrmOfficerProgram::UPFRONT_RATE,
            'retention_rate' => CrmOfficerProgram::RETENTION_RATE,
        ];

        return view('crm.commissions.index', compact('pageTitle', 'commissions', 'agents', 'clients', 'staff', 'summary'));
    }

    public function store(Request $request)
    {
        $auth = Auth::guard('crm')->user();

        $request->validate([
            'staff_id'     => 'required|exists:crm_staff,id',
            'client_id'    => 'nullable|exists:crm_clients,id',
            'title'        => 'required|string|max:160',
            'period'       => 'nullable|string|max:40',
            'base_amount'  => 'required|numeric|min:0',
            'rate_percent' => 'required|numeric|min:0|max:100',
            'status'       => 'required|in:pending,approved,paid,rejected',
            'notes'        => 'nullable|string',
        ]);

        if (!$auth->isSuper() && $auth->portal() === 'agent') {
            $request->merge(['staff_id' => $auth->id]);
        }

        $amount = ($request->base_amount * $request->rate_percent) / 100;

        CrmCommission::create([
            'staff_id'          => $request->staff_id,
            'client_id'         => $request->client_id,
            'title'             => $request->title,
            'period'            => $request->period,
            'base_amount'       => $request->base_amount,
            'rate_percent'      => $request->rate_percent,
            'commission_amount' => $amount,
            'status'            => $request->status,
            'notes'             => $request->notes,
            'paid_at'           => $request->status === 'paid' ? now() : null,
        ]);

        $notify[] = ['success', 'Commission recorded'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $commission = CrmCommission::findOrFail($id);
        $auth       = Auth::guard('crm')->user();

        if (!$auth->isSuper() && $auth->portal() === 'agent') {
            $notify[] = ['error', 'Officers can view commissions; status changes require manager/CRM admin.'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'status' => 'required|in:pending,approved,paid,rejected',
            'notes'  => 'nullable|string',
        ]);

        $commission->status  = $request->status;
        $commission->notes   = $request->notes;
        $commission->paid_at = $request->status === 'paid' ? ($commission->paid_at ?: now()) : null;
        $commission->save();

        $notify[] = ['success', 'Commission updated'];
        return back()->withNotify($notify);
    }

    /** Generate annual 1% retention commissions for currently funded clients */
    public function generateRetention(Request $request)
    {
        $auth = Auth::guard('crm')->user();
        if (!$auth->isSuper() && $auth->portal() === 'agent') {
            abort(403);
        }

        $year = (string) (now()->year);
        $created = 0;

        $clients = CrmClient::whereIn('stage', ['funded', 'active'])
            ->where('committed_aum', '>', 0)
            ->get();

        foreach ($clients as $client) {
            if (!$client->owner_staff_id) {
                continue;
            }

            $exists = CrmCommission::where('client_id', $client->id)
                ->where('rate_percent', CrmOfficerProgram::RETENTION_RATE)
                ->where('period', $year)
                ->exists();

            if ($exists) {
                continue;
            }

            $amount = (float) $client->committed_aum;
            $commission = CrmOfficerProgram::retentionCommission($amount);

            CrmCommission::create([
                'staff_id'          => $client->owner_staff_id,
                'client_id'         => $client->id,
                'title'             => 'Retention 1% — ' . $client->name . ' (' . $year . ')',
                'period'            => $year,
                'base_amount'       => $amount,
                'rate_percent'      => CrmOfficerProgram::RETENTION_RATE,
                'commission_amount' => $commission,
                'status'            => 'pending',
                'notes'             => 'Annual retention while capital remains invested',
            ]);

            $client->retention_commission_ytd = $commission;
            $client->save();
            $created++;
        }

        $notify[] = ['success', "Generated {$created} retention commission record(s) for {$year}."];
        return back()->withNotify($notify);
    }
}
