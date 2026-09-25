<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $pageTitle = 'Referrals';
        $staff     = Auth::guard('crm')->user();
        $query     = CrmReferral::with('staff')->orderByDesc('id');

        if (!$staff->isSuper() && $staff->portal() === 'agent') {
            $query->where('staff_id', $staff->id);
        }

        $referrals = $query->paginate(getPaginate());

        return view('crm.referrals.index', compact('pageTitle', 'referrals', 'staff'));
    }

    public function store(Request $request)
    {
        $staff = Auth::guard('crm')->user();

        $request->validate([
            'prospect_name'    => 'required|string|max:160',
            'prospect_email'   => 'nullable|email|max:160',
            'prospect_phone'   => 'nullable|string|max:50',
            'referrer_name'    => 'nullable|string|max:160',
            'referrer_email'   => 'nullable|email|max:160',
            'potential_amount' => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'status'           => 'nullable|in:pending,contacted,converted,closed',
        ]);

        CrmReferral::create([
            'staff_id'         => $staff->id,
            'prospect_name'    => $request->prospect_name,
            'prospect_email'   => $request->prospect_email,
            'prospect_phone'   => $request->prospect_phone,
            'referrer_name'    => $request->referrer_name,
            'referrer_email'   => $request->referrer_email,
            'potential_amount' => $request->potential_amount ?: 0,
            'notes'            => $request->notes,
            'status'           => $request->status ?: 'pending',
        ]);

        $notify[] = ['success', 'Referral logged'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $staff    = Auth::guard('crm')->user();
        $referral = CrmReferral::findOrFail($id);

        if (!$staff->isSuper() && $staff->portal() === 'agent' && $referral->staff_id != $staff->id) {
            abort(403);
        }

        $request->validate([
            'prospect_name'    => 'required|string|max:160',
            'prospect_email'   => 'nullable|email|max:160',
            'prospect_phone'   => 'nullable|string|max:50',
            'referrer_name'    => 'nullable|string|max:160',
            'referrer_email'   => 'nullable|email|max:160',
            'potential_amount' => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:pending,contacted,converted,closed',
        ]);

        $referral->fill($request->only([
            'prospect_name', 'prospect_email', 'prospect_phone',
            'referrer_name', 'referrer_email', 'potential_amount', 'notes', 'status',
        ]));
        $referral->save();

        $notify[] = ['success', 'Referral updated'];
        return back()->withNotify($notify);
    }
}
