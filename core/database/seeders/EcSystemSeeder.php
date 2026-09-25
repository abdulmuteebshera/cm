<?php

namespace Database\Seeders;

use App\Models\EmailCampaign\EcAdmin;
use App\Models\EmailCampaign\EcMailSetting;
use App\Models\EmailCampaign\EcTemplate;
use App\Support\EmailCampaign\EcInstitutionalTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EcSystemSeeder extends Seeder
{
    public function run(): void
    {
        EcAdmin::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Email Campaign Admin',
                'password' => Hash::make('Qwerty123!@#'),
                'status'   => 1,
            ]
        );

        if (!EcMailSetting::query()->exists()) {
            EcMailSetting::query()->create([
                'from_email'       => 'noreply@crownmairecapital.com',
                'from_name'        => 'Crownmaire Capital',
                'reply_to'         => 'support@crownmairecapital.com',
                'smtp_host'        => 'smtp.example.com',
                'smtp_port'        => 587,
                'smtp_encryption'  => 'tls',
                'smtp_username'    => '',
                'smtp_password'    => '',
            ]);
        }

        EcTemplate::query()->updateOrCreate(
            ['name' => EcInstitutionalTemplate::SLUG_NAME],
            [
                'subject'   => EcInstitutionalTemplate::subject(),
                'body_html' => EcInstitutionalTemplate::bodyHtml(),
                'body_text' => EcInstitutionalTemplate::bodyText(),
                'status'    => 1,
            ]
        );

        // Retire legacy slug if present from earlier build
        EcTemplate::query()
            ->where('name', 'Institutional Investor Outreach')
            ->update(['name' => 'Institutional Investor Outreach (deprecated)', 'status' => 0]);

        EcTemplate::query()->firstOrCreate(
            ['name' => 'Default Outreach'],
            [
                'subject'   => 'Hello {{name}} — message from Crownmaire Capital',
                'body_html' => '<p>Dear {{name}},</p><p>Thank you for your interest in Crownmaire Capital.</p><p>Best regards,<br>Crownmaire Capital Team</p>',
                'body_text' => "Dear {{name}},\n\nThank you for your interest in Crownmaire Capital.\n\nBest regards,\nCrownmaire Capital Team",
                'status'    => 1,
            ]
        );
    }
}
