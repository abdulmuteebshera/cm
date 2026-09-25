<?php

namespace App\Http\Controllers\EmailCampaign\User;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcCampaign;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('ec_user')->user();
        $pageTitle = 'My Dashboard';

        $campaigns = EcCampaign::query()
            ->where('ec_user_id', $user->id)
            ->latest('id')
            ->limit(8)
            ->get();

        $stats = [
            'total'    => EcCampaign::query()->where('ec_user_id', $user->id)->count(),
            'running'  => EcCampaign::query()->where('ec_user_id', $user->id)->where('status', 'running')->count(),
            'paused'   => EcCampaign::query()->where('ec_user_id', $user->id)->where('status', 'paused')->count(),
            'completed'=> EcCampaign::query()->where('ec_user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('emailcampaign.user.dashboard', compact('pageTitle', 'campaigns', 'stats'));
    }
}
