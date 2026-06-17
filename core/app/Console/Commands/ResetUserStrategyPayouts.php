<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use App\Models\User;
use Illuminate\Console\Command;

class ResetUserStrategyPayouts extends Command
{
    protected $signature = 'portal:reset-user-payouts
        {username=muteeb : Username to reset}
        {--force : Skip confirmation}';

    protected $description = 'Reverse all approved strategy period payouts for a user and regenerate pending period returns from weekly data';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))
            ->orWhere('firstname', 'Muteeb')
            ->first();

        if (!$user) {
            $this->error('User not found.');
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("Reset ALL strategy payouts for {$user->username} (id {$user->id})?")) {
            return self::SUCCESS;
        }

        $reversed = StrategyPayoutService::resetUserApprovedPayouts($user);

        $user->refresh();
        $this->info("Reversed {$reversed} approved period payout(s).");
        $this->info("Interest wallet: {$user->interest_wallet}");
        $this->info('Period returns re-synced from approved weekly performance — submit & approve again in admin.');

        return self::SUCCESS;
    }
}
