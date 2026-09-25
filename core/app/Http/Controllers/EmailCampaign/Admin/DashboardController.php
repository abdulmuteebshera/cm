<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcCampaign;
use App\Models\EmailCampaign\EcTemplate;
use App\Models\EmailCampaign\EcUser;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Admin Dashboard';

        $stats = [
            'users'      => EcUser::query()->count(),
            'templates'  => EcTemplate::query()->where('status', 1)->count(),
            'campaigns'  => EcCampaign::query()->count(),
            'running'    => EcCampaign::query()->where('status', 'running')->count(),
        ];

        $recentActivity = EcActivityLog::query()->latest('id')->limit(15)->get();
        $runningCampaigns = EcCampaign::query()->with('user')->where('status', 'running')->latest('id')->limit(10)->get();

        return view('emailcampaign.admin.dashboard', compact('pageTitle', 'stats', 'recentActivity', 'runningCampaigns'));
    }
}
