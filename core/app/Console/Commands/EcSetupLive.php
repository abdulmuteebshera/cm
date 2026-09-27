<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class EcSetupLive extends Command
{
    protected $signature = 'ec:setup-live {--seed : Create default admin, SMTP row, and templates}';

    protected $description = 'Create email campaign tables only (safe on live DB — no portal/user/deposit changes)';

    protected array $ecMigrations = [
        'database/migrations/2026_09_26_000001_create_email_campaign_tables.php',
        'database/migrations/2026_09_26_000002_ec_admin_owns_groups_campaigns.php',
        'database/migrations/2026_09_26_000003_ec_groups_visibility.php',
    ];

    public function handle(): int
    {
        $this->info('Email campaign: new tables only (ec_*).');
        $this->warn('Does not modify users, deposits, withdrawals, tickets, or other live portal tables.');

        foreach ($this->ecMigrations as $path) {
            if (!file_exists(base_path($path))) {
                $this->error("Missing migration: {$path}");
                return self::FAILURE;
            }

            $this->line("→ {$path}");
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
        }

        if ($this->option('seed')) {
            $this->line('→ Seeding EcSystemSeeder (ec admin + mail settings + templates)');
            Artisan::call('db:seed', [
                '--class' => 'EcSystemSeeder',
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
        }

        $this->info('Email campaign schema ready.');

        return self::SUCCESS;
    }
}
