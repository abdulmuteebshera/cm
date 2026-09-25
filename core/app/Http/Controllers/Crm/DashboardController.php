<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmClient;
use App\Models\Crm\CrmCommission;
use App\Models\Crm\CrmFinanceEntry;
use App\Models\Crm\CrmFundPosition;
use App\Models\Crm\CrmReferral;
use App\Models\Crm\CrmStaff;
use App\Models\Deposit;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use App\Support\CrmOfficerProgram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    protected function staff(): CrmStaff
    {
        return Auth::guard('crm')->user();
    }

    public function crm()
    {
        $pageTitle = 'CRM Command Center';
        $stats = $this->portalStats();
        $stats['staff']     = CrmStaff::count();
        $stats['clients']   = CrmClient::count();
        $stats['referrals'] = CrmReferral::count();
        $stats['positions'] = Schema::hasTable('crm_fund_positions') ? CrmFundPosition::count() : 0;
        $stats['fund_aum']  = Schema::hasTable('crm_fund_positions') ? (float) CrmFundPosition::sum('market_value') : 0;
        $stats['income']    = Schema::hasTable('crm_finance_entries') ? (float) CrmFinanceEntry::where('type', 'income')->sum('amount') : 0;
        $stats['expense']   = Schema::hasTable('crm_finance_entries') ? (float) CrmFinanceEntry::where('type', 'expense')->sum('amount') : 0;

        return view('crm.dashboard.crm', compact('pageTitle', 'stats'));
    }

    public function manager()
    {
        $pageTitle = 'Manager Command Center';
        $staff     = $this->staff();
        $stages    = CrmOfficerProgram::leadStages();
        $pipeline  = [];
        foreach ($stages as $key => $label) {
            $pipeline[$key] = [
                'label' => $label,
                'count' => CrmClient::where('stage', $key)->count(),
            ];
        }

        $agents = CrmStaff::with('role')
            ->whereHas('role', fn ($q) => $q->where('portal', 'agent'))
            ->withCount(['clients'])
            ->orderByDesc('lifetime_aum')
            ->limit(8)
            ->get();

        $stats = [
            'clients'   => CrmClient::count(),
            'active'    => CrmClient::whereIn('stage', ['funded', 'active', 'investment_pending', 'proposal_sent', 'meeting_done'])->count(),
            'agents'    => $agents->count(),
            'funded_aum'=> (float) CrmClient::whereIn('stage', ['funded', 'active'])->sum('committed_aum'),
            'modules'   => count($staff->adminDeskPermissionSlugs()),
        ];

        $recent = CrmClient::with('owner')->orderByDesc('updated_at')->limit(8)->get();

        return view('crm.dashboard.manager', compact('pageTitle', 'stats', 'pipeline', 'agents', 'recent', 'staff'));
    }

    public function agent()
    {
        $pageTitle = 'Investment Officer Desk';
        $staff     = $this->staff();
        $stages    = CrmOfficerProgram::leadStages();

        $mine = CrmClient::where('owner_staff_id', $staff->id);
        $pipeline = [];
        foreach ($stages as $key => $label) {
            $pipeline[$key] = [
                'label' => $label,
                'count' => (clone $mine)->where('stage', $key)->count(),
            ];
        }

        $fundedAum = (float) (clone $mine)->whereIn('stage', ['funded', 'active'])->sum('committed_aum');
        $expected  = (float) (clone $mine)->sum('expected_aum');
        $upfront   = (float) CrmCommission::where('staff_id', $staff->id)->where('rate_percent', CrmOfficerProgram::UPFRONT_RATE)->sum('commission_amount');
        $retention = (float) CrmCommission::where('staff_id', $staff->id)->where('rate_percent', CrmOfficerProgram::RETENTION_RATE)->sum('commission_amount');
        $pending   = (float) CrmCommission::where('staff_id', $staff->id)->where('status', 'pending')->sum('commission_amount');
        $paid      = (float) CrmCommission::where('staff_id', $staff->id)->where('status', 'paid')->sum('commission_amount');

        $ranking = $staff->rankingSnapshot();
        $clients = CrmClient::where('owner_staff_id', $staff->id)->orderByDesc('updated_at')->limit(8)->get();
        $followUps = CrmClient::where('owner_staff_id', $staff->id)
            ->whereNotNull('next_follow_up')
            ->whereDate('next_follow_up', '<=', now()->addDays(7))
            ->orderBy('next_follow_up')
            ->limit(6)
            ->get();

        $stats = [
            'leads'       => (clone $mine)->count(),
            'funded'      => (clone $mine)->whereIn('stage', ['funded', 'active'])->count(),
            'funded_aum'  => $fundedAum,
            'expected'    => $expected,
            'upfront'     => $upfront,
            'retention'   => $retention,
            'pending_pay' => $pending,
            'paid'        => $paid,
            'referrals'   => CrmReferral::where('staff_id', $staff->id)->count(),
        ];

        return view('crm.dashboard.agent', compact(
            'pageTitle', 'stats', 'pipeline', 'ranking', 'clients', 'followUps', 'staff', 'stages'
        ));
    }

    public function trader()
    {
        $pageTitle = 'Trading Desk';
        $positions = CrmFundPosition::orderByDesc('id')->limit(12)->get();
        $stats     = [
            'positions'  => CrmFundPosition::count(),
            'allocated'  => (float) CrmFundPosition::sum('allocated_amount'),
            'market'     => (float) CrmFundPosition::sum('market_value'),
            'pnl'        => (float) CrmFundPosition::sum('pnl'),
        ];

        return view('crm.dashboard.trader', compact('pageTitle', 'stats', 'positions'));
    }

    public function finance()
    {
        $pageTitle = 'Finance Portal';
        $income    = (float) CrmFinanceEntry::where('type', 'income')->sum('amount');
        $expense   = (float) CrmFinanceEntry::where('type', 'expense')->sum('amount');
        $entries   = CrmFinanceEntry::orderByDesc('entry_date')->limit(12)->get();
        $stats     = [
            'income'  => $income,
            'expense' => $expense,
            'net'     => $income - $expense,
            'entries' => CrmFinanceEntry::count(),
        ];

        return view('crm.dashboard.finance', compact('pageTitle', 'stats', 'entries'));
    }

    protected function portalStats(): array
    {
        $stats = [
            'investors'   => 0,
            'deposits'    => 0,
            'withdrawals' => 0,
            'tickets'     => 0,
        ];

        try {
            $stats['investors']   = User::count();
            $stats['deposits']    = class_exists(Deposit::class) ? Deposit::where('status', 1)->count() : 0;
            $stats['withdrawals'] = class_exists(Withdrawal::class) ? Withdrawal::where('status', 1)->count() : 0;
            $stats['tickets']     = class_exists(SupportTicket::class) ? SupportTicket::whereIn('status', [0, 2])->count() : 0;
        } catch (\Throwable $e) {
        }

        return $stats;
    }
}
