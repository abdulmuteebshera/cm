<?php

namespace App\Console\Commands;

use App\Lib\StrategyPayoutService;
use App\Models\Invest;
use App\Models\PeriodPayoutItem;
use App\Models\Plan;
use App\Models\PlanPeriodReturn;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncGordonWallet extends Command
{
    protected $signature = 'gordon:sync-wallet {--force : Skip confirmation}';

    protected $description = 'Set Gordon Henges to $110k invested, $22,800 compounded profit, $132,800 value, empty wallets, and reopen the Oct 1 payout';

    public function handle(): int
    {
        $user = User::query()
            ->where('username', 'triphenges')
            ->orWhere('email', 'trip.henges1@gmail.com')
            ->orWhere(function ($query) {
                $query->where('firstname', 'Gordon')->where('lastname', 'Henges');
            })
            ->first();

        if (!$user) {
            $this->error('Gordon Henges was not found.');
            return self::FAILURE;
        }

        $invest = Invest::query()
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id')
            ->first();

        if (!$invest) {
            $this->error('No active investment found for Gordon.');
            return self::FAILURE;
        }

        if (!$this->option('force') && !$this->confirm("Update {$user->username} (user {$user->id}, invest {$invest->id})?")) {
            return self::SUCCESS;
        }

        $invested = 110000.0;
        $oldProfit = 22800.0;

        DB::transaction(function () use ($user, $invest, $invested, $oldProfit): void {
            $item = PeriodPayoutItem::query()
                ->where('user_id', $user->id)
                ->where('invest_id', $invest->id)
                ->orderByDesc('id')
                ->first();

            if ($item && $item->isApproved()) {
                if ($item->transaction_id) {
                    Transaction::where('id', $item->transaction_id)->delete();
                } else {
                    Transaction::query()
                        ->where('user_id', $user->id)
                        ->where('invest_id', $invest->id)
                        ->where('remark', 'interest')
                        ->where('amount', $item->amount)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->delete();
                }

                $invest->return_rec_time = max(0, (int) $invest->return_rec_time - 1);

                $item->status = PeriodPayoutItem::STATUS_PENDING;
                $item->compounded = false;
                $item->approved_at = null;
                $item->approved_by = null;
                $item->transaction_id = null;
                $item->save();

                $period = PlanPeriodReturn::find($item->plan_period_return_id);
                if ($period) {
                    StrategyPayoutService::refreshPeriodStatusFromItems($period);
                }
            }

            $invest->initial_amount = $invested;
            $invest->amount = $invested + $oldProfit;
            $invest->paid = $oldProfit;
            $invest->net_interest = $oldProfit;
            $invest->save();

            $plan = Plan::find($invest->plan_id);
            if ($plan) {
                StrategyPayoutService::syncInvestNextPayoutTime($invest, $plan);
            }

            $user->total_invests = $invested;
            $user->deposit_wallet = 0;
            $user->interest_wallet = 0;
            $user->save();
        });

        $invest->refresh();
        $user->refresh();
        $item = PeriodPayoutItem::query()
            ->where('user_id', $user->id)
            ->where('invest_id', $invest->id)
            ->orderByDesc('id')
            ->first();

        $this->info('invested=' . $invest->initial_amount);
        $this->info('current=' . $invest->amount);
        $this->info('pre_upgrade_profit=' . $invest->paid);
        $this->info('next=' . $invest->next_time);
        $this->info('interest_wallet=' . $user->interest_wallet);
        $this->info('oct1=' . ($item?->amount ?? 'none') . ' ' . ($item?->status ?? 'missing'));

        return self::SUCCESS;
    }
}
