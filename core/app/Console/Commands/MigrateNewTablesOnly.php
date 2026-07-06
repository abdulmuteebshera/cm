<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateNewTablesOnly extends Command
{
    protected $signature = 'migrate:new-tables-only';

    protected $description = 'Run only safe CREATE-table migrations; skips alters on live deposit/withdraw/ticket tables';

    /**
     * Migrations that only create new tables (with hasTable guards where applicable).
     * Never includes Schema::table / drop / seed changes to existing client data.
     */
    protected array $safeMigrations = [
        'database/migrations/2026_06_22_000002_create_announcements_table.php',
        'database/migrations/2026_06_22_000003_create_leaderboards_table.php',
        'database/migrations/2026_06_22_000004_create_certificates_table.php',
        'database/migrations/2026_06_23_000001_create_portfolio_allocations_table.php',
        'database/migrations/2026_07_02_000001_create_job_posts_table.php',
        'database/migrations/2026_07_02_000002_create_job_applications_table.php',
    ];

    public function handle(): int
    {
        $this->info('Safe migration: new tables only.');
        $this->warn('Not running alter/drop migrations — live deposits, withdrawals, and tickets are untouched.');

        foreach ($this->safeMigrations as $path) {
            if (!file_exists(base_path($path))) {
                $this->warn("Skipping missing file: {$path}");
                continue;
            }

            $this->line("→ {$path}");
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
        }

        $this->info('Safe migrations finished.');

        return self::SUCCESS;
    }
}
