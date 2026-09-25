<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmStaff;
use App\Support\CrmOfficerProgram;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    public function index()
    {
        $pageTitle = 'Goals & Achievements';
        $staff     = Auth::guard('crm')->user();
        $tiers     = CrmOfficerProgram::rankingTiers();

        $officers = CrmStaff::whereHas('role', fn ($q) => $q->where('portal', 'agent'))
            ->orderByDesc('lifetime_aum')
            ->get()
            ->map(function (CrmStaff $officer) {
                $officer->ranking = $officer->rankingSnapshot();
                return $officer;
            });

        $mine = $staff->portal() === 'agent' ? $staff->rankingSnapshot() : null;

        return view('crm.ranking.index', compact('pageTitle', 'tiers', 'officers', 'mine', 'staff'));
    }
}
