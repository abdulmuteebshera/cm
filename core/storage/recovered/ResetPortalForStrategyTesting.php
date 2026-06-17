<?php

namespace App\Console\Commands;

use App\Models\Invest;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPortalForStrategyTesting extends Command
{
    protected $signature = 'portal:reset-strategy-test {--deposit=100000 : Deposit wallet balance for Muteeb}';

    protected $description = 'Remove plans/strategies/investments, delete all users except Muteeb, and fund Muteeb for testing';

    public function handle(): int
    {
        $keepUsername = 'muteeb';
        $depositAmount = (float) $this->option('deposit');

        $muteeb = User::where('username', $keepUsername)->orWhere('firstname', 'Muteeb')->first();

        if (!$muteeb) {
            $this->error('Muteeb user account not found.');
            return self::FAILURE;
        }

        if (!$this->confirm("This will DELETE all plans, investments, and users except {$muteeb->username} (id {$muteeb->id}). Continue?")) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        DB::beginTransaction();

        try {
            DB::table('quarterly_payout_items')->delete();
            DB::table('quarterly_payout_batches')->delete();
            DB::table('plan_weekly_returns')->delete();
            DB::table('schedule_invests')->delete();
            DB::table('invests')->delete();
            DB::table('plans')->delete();

            $otherUserIds = User::where('id', '!=', $muteeb->id)->pluck('id');

            if ($otherUserIds->isNotEmpty()) {
                $ids = $otherUserIds->all();
                DB::table('transactions')->whereIn('user_id', $ids)->delete();
                DB::table('deposits')->whereIn('user_id', $ids)->delete();
                DB::table('withdrawals')->whereIn('user_id', $ids)->delete();
                DB::table('support_tickets')->whereIn('user_id', $ids)->delete();
                DB::table('support_messages')->whereIn('support_ticket_id', function ($q) use ($ids) {
                    $q->select('id')->from('support_tickets')->whereIn('user_id', $ids);
                })->delete();
                DB::table('admin_notifications')->whereIn('user_id', $ids)->delete();
                DB::table('user_logins')->whereIn('user_id', $ids)->delete();
                DB::table('device_tokens')->whereIn('user_id', $ids)->delete();
                User::whereIn('id', $ids)->delete();
            }

            DB::table('transactions')->where('user_id', $muteeb->id)->delete();
            DB::table('deposits')->where('user_id', $muteeb->id)->delete();
            DB::table('withdrawals')->where('user_id', $muteeb->id)->delete();

            $muteeb->deposit_wallet   = $depositAmount;
            $muteeb->interest_wallet  = 0;
            $muteeb->total_invests    = 0;
            $muteeb->team_invests     = 0;
            $muteeb->save();

            DB::commit();

            $this->info("Done. Kept user: {$muteeb->username} (id {$muteeb->id})");
            $this->info("Deposit wallet: {$depositAmount} | Interest wallet: 0");
            $this->info('All plans, strategies, and investments removed.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
