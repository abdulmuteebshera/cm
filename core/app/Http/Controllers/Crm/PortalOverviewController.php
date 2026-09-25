<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Invest;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Schema;

class PortalOverviewController extends Controller
{
    public function investors()
    {
        $pageTitle = 'Portal Investors (Read-only)';
        $investors = User::orderByDesc('id')->paginate(getPaginate());

        return view('crm.portal.investors', compact('pageTitle', 'investors'));
    }

    public function deposits()
    {
        $pageTitle = 'Portal Deposits (Read-only)';
        $deposits  = Deposit::with('user')->orderByDesc('id')->paginate(getPaginate());

        return view('crm.portal.deposits', compact('pageTitle', 'deposits'));
    }

    public function withdrawals()
    {
        $pageTitle   = 'Portal Withdrawals (Read-only)';
        $withdrawals = Withdrawal::with('user')->orderByDesc('id')->paginate(getPaginate());

        return view('crm.portal.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function tickets()
    {
        $pageTitle = 'Portal Support Tickets (Read-only)';
        $tickets   = SupportTicket::with('user')->orderByDesc('id')->paginate(getPaginate());

        return view('crm.portal.tickets', compact('pageTitle', 'tickets'));
    }

    public function investments()
    {
        $pageTitle   = 'Portal Investments (Read-only)';
        $investments = Schema::hasTable('invests')
            ? Invest::with('user', 'plan')->orderByDesc('id')->paginate(getPaginate())
            : collect();

        return view('crm.portal.investments', compact('pageTitle', 'investments'));
    }

    public function jobs()
    {
        $pageTitle    = 'Careers Overview (Read-only)';
        $jobPosts     = Schema::hasTable('job_posts') ? JobPost::withCount('applications')->orderByDesc('id')->get() : collect();
        $applications = Schema::hasTable('job_applications')
            ? JobApplication::with('jobPost')->orderByDesc('id')->paginate(getPaginate())
            : collect();

        return view('crm.portal.jobs', compact('pageTitle', 'jobPosts', 'applications'));
    }
}
