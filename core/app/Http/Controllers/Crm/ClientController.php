<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmClient;
use App\Models\Crm\CrmCommission;
use App\Models\Crm\CrmStaff;
use App\Support\CrmOfficerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Lead Pipeline';
        $staff     = Auth::guard('crm')->user();
        $stages    = CrmOfficerProgram::leadStages();

        $query = CrmClient::with('owner')->orderByDesc('updated_at');
        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $query->where('owner_staff_id', $staff->id);
        }

        if ($request->filled('stage') && isset($stages[$request->stage])) {
            $query->where('stage', $request->stage);
        }

        $clients = $query->paginate(getPaginate());
        $agents  = CrmStaff::whereHas('role', fn ($q) => $q->whereIn('portal', ['agent', 'manager']))
            ->orWhere('is_super', 1)
            ->orderBy('name')
            ->get();

        $pipelineCounts = [];
        $base = CrmClient::query();
        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $base->where('owner_staff_id', $staff->id);
        }
        foreach ($stages as $key => $label) {
            $pipelineCounts[$key] = (clone $base)->where('stage', $key)->count();
        }

        return view('crm.clients.index', compact('pageTitle', 'clients', 'agents', 'staff', 'stages', 'pipelineCounts'));
    }

    public function store(Request $request)
    {
        $staff  = Auth::guard('crm')->user();
        $stages = array_keys(CrmOfficerProgram::leadStages());

        $request->validate([
            'name'           => 'required|string|max:160',
            'email'          => 'nullable|email|max:160',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:160',
            'country'        => 'nullable|string|max:80',
            'city'           => 'nullable|string|max:80',
            'source'         => 'nullable|string|max:80',
            'stage'          => 'required|in:' . implode(',', $stages),
            'expected_aum'   => 'nullable|numeric|min:0',
            'committed_aum'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'owner_staff_id' => 'nullable|exists:crm_staff,id',
        ]);

        $ownerId = $request->owner_staff_id;
        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $ownerId = $staff->id;
        }

        $client = CrmClient::create([
            'owner_staff_id'   => $ownerId ?: $staff->id,
            'name'             => $request->name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'company'          => $request->company,
            'country'          => $request->country,
            'city'             => $request->city,
            'source'           => $request->source,
            'stage'            => $request->stage,
            'expected_aum'     => $request->expected_aum ?: 0,
            'committed_aum'    => $request->committed_aum ?: 0,
            'notes'            => $request->notes,
            'next_follow_up'   => $request->next_follow_up,
            'last_activity_at' => now(),
        ]);

        $this->handleFundingTransition($client, null, $client->stage);

        $notify[] = ['success', 'Lead created'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $staff  = Auth::guard('crm')->user();
        $client = CrmClient::findOrFail($id);
        $stages = array_keys(CrmOfficerProgram::leadStages());

        if (!$staff->isSuper() && $staff->portal() === 'agent' && $client->owner_staff_id != $staff->id) {
            abort(403);
        }

        $request->validate([
            'name'           => 'required|string|max:160',
            'email'          => 'nullable|email|max:160',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:160',
            'country'        => 'nullable|string|max:80',
            'city'           => 'nullable|string|max:80',
            'source'         => 'nullable|string|max:80',
            'stage'          => 'required|in:' . implode(',', $stages),
            'expected_aum'   => 'nullable|numeric|min:0',
            'committed_aum'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'owner_staff_id' => 'nullable|exists:crm_staff,id',
        ]);

        $oldStage = $client->stage;

        $client->fill($request->only([
            'name', 'email', 'phone', 'company', 'country', 'city', 'source', 'stage',
            'expected_aum', 'committed_aum', 'notes', 'next_follow_up',
        ]));

        if ($staff->isSuper() || $staff->portal() === 'manager') {
            $client->owner_staff_id = $request->owner_staff_id ?: $client->owner_staff_id;
        }

        $client->last_activity_at = now();
        $client->save();

        $this->handleFundingTransition($client, $oldStage, $client->stage);

        $notify[] = ['success', 'Lead updated'];
        return back()->withNotify($notify);
    }

    protected function handleFundingTransition(CrmClient $client, ?string $oldStage, string $newStage): void
    {
        $wasFunded = in_array($oldStage, ['funded', 'active'], true);
        $isFunded  = in_array($newStage, ['funded', 'active'], true);

        if ($isFunded && !$wasFunded) {
            $amount = (float) $client->committed_aum;
            $upfront = CrmOfficerProgram::upfrontCommission($amount);

            $client->funded_on = $client->funded_on ?: now()->toDateString();
            $client->upfront_commission = $upfront;
            $client->save();

            if ($client->owner_staff_id && $amount > 0) {
                CrmCommission::create([
                    'staff_id'          => $client->owner_staff_id,
                    'client_id'         => $client->id,
                    'title'             => 'Upfront 2% — ' . $client->name,
                    'period'            => now()->format('Y-m'),
                    'base_amount'       => $amount,
                    'rate_percent'      => CrmOfficerProgram::UPFRONT_RATE,
                    'commission_amount' => $upfront,
                    'status'            => 'pending',
                    'notes'             => 'Auto-generated upfront commission on funding',
                ]);

                $officer = CrmStaff::find($client->owner_staff_id);
                if ($officer) {
                    $officer->lifetime_aum = (float) $officer->lifetime_aum + $amount;
                    $rank = $officer->rankingSnapshot();
                    $officer->rank_tier = $rank['current']['slug'];
                    $officer->save();
                }
            }
        }
    }
}
