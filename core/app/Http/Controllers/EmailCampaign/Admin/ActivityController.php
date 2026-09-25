<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;

class ActivityController extends Controller
{
    public function index()
    {
        $pageTitle = 'Activity Log';
        $logs = EcActivityLog::query()->latest('id')->paginate(40);

        return view('emailcampaign.admin.activity', compact('pageTitle', 'logs'));
    }
}
