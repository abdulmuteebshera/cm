<?php

namespace App\Http\Controllers\EmailCampaign\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign\EcActivityLog;
use App\Models\EmailCampaign\EcMailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailSettingController extends Controller
{
    public function edit()
    {
        $pageTitle = 'Outbound Mail (SMTP)';
        $settings = EcMailSetting::query()->first();

        if (!$settings) {
            $settings = EcMailSetting::query()->create([
                'from_email'      => '',
                'from_name'       => 'Crownmaire Capital',
                'smtp_host'       => '',
                'smtp_port'       => 587,
                'smtp_encryption' => 'tls',
                'smtp_username'   => '',
                'smtp_password'   => '',
            ]);
        }

        return view('emailcampaign.admin.mail_settings', compact('pageTitle', 'settings'));
    }

    public function update(Request $request)
    {
        $settings = EcMailSetting::query()->firstOrFail();

        $data = $request->validate([
            'from_email'       => 'required|email',
            'from_name'        => 'required|string|max:120',
            'reply_to'         => 'nullable|email',
            'smtp_host'        => 'required|string|max:255',
            'smtp_port'        => 'required|integer|min:1|max:65535',
            'smtp_encryption'  => 'required|in:none,tls,ssl',
            'smtp_username'    => 'required|string|max:255',
            'smtp_password'    => 'nullable|string',
        ]);

        if ($data['smtp_password'] === null || $data['smtp_password'] === '') {
            unset($data['smtp_password']);
        }

        $settings->fill($data);
        $settings->save();

        EcActivityLog::record(
            'admin',
            Auth::guard('ec_admin')->id(),
            'mail_settings.updated',
            'Updated SMTP / sender settings'
        );

        return back()->withNotify([['success', 'Mail settings saved. All user campaigns will send through this account.']]);
    }
}
