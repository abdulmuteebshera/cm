<?php

namespace App\Console\Commands;

use App\Models\Invest;
use App\Models\PeriodPayoutItem;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncGordonWallet extends Command
{
    protected $signature = 'gordon:sync-wallet {--force : Skip confirmation}';

    protected $description = 'Set Gordon Henges to $110k invested, $22,800 pre-upgrade profit, $132,800 value, separate Oct 1 $6,300, and $600 withdrawn';

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
        $oct1Profit = 6300.0;
        $withdrawn = 600.0;

        DB::transaction(function () use ($user, $invest, $invested, $oldProfit, $oct1Profit, $withdrawn): void {
            $invest->initial_amount = $invested;
            $invest->amount = $invested + $oldProfit;
            $invest->paid = $oldProfit;
            $invest->net_interest = $oldProfit;
            $invest->last_time = '2026-10-01 00:00:00';
            $invest->next_time = '2027-01-01 00:00:00';
            $invest->save();

            $user->total_invests = $invested;
            $user->deposit_wallet = 0;
            $user->interest_wallet = $oct1Profit - $withdrawn;
            $user->save();

            $item = PeriodPayoutItem::query()
                ->where('user_id', $user->id)
                ->where('invest_id', $invest->id)
                ->where('amount', $oct1Profit)
                ->orderByDesc('id')
                ->first();

            if ($item) {
                $item->compounded = false;
                $item->save();
            }

            $oct1Trx = Transaction::query()
                ->where('user_id', $user->id)
                ->where('invest_id', $invest->id)
                ->where('remark', 'interest')
                ->where('amount', $oct1Profit)
                ->orderByDesc('id')
                ->first();

            if ($oct1Trx) {
                $oct1Trx->post_balance = $oct1Profit;
                $oct1Trx->details = '6,300.00 USD interest from Crownmaire Alpha (Payout Oct 01, 2026)';
                $oct1Trx->wallet_type = 'interest_wallet';
                $oct1Trx->save();
            }

            $withdrawTrx = Transaction::query()
                ->where('user_id', $user->id)
                ->where('remark', 'withdraw')
                ->where('amount', $withdrawn)
                ->orderByDesc('id')
                ->first();

            if ($withdrawTrx) {
                $withdrawTrx->post_balance = $oct1Profit - $withdrawn;
                $withdrawTrx->save();
            }

            $existingWithdraw = Withdrawal::query()
                ->where('user_id', $user->id)
                ->where('amount', $withdrawn)
                ->where('status', 1)
                ->first();

            if (!$existingWithdraw) {
                $trx = getTrx();

                $withdrawal = new Withdrawal();
                $withdrawal->method_id = 1;
                $withdrawal->user_id = $user->id;
                $withdrawal->amount = $withdrawn;
                $withdrawal->currency = 'USD';
                $withdrawal->rate = 1;
                $withdrawal->charge = 0;
                $withdrawal->management_fee = 0;
                $withdrawal->trx = $trx;
                $withdrawal->final_amount = $withdrawn;
                $withdrawal->after_charge = $withdrawn;
                $withdrawal->withdraw_information = [
                    [
                        'name'  => 'Beneficiary Name',
                        'type'  => 'text',
                        'value' => 'Gordon Henges',
                    ],
                ];
                $withdrawal->status = 1;
                $withdrawal->admin_feedback = 'Paid';
                $withdrawal->created_at = '2026-10-01 12:00:00';
                $withdrawal->updated_at = '2026-10-01 12:00:00';
                $withdrawal->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->invest_id = $invest->id;
                $transaction->amount = $withdrawn;
                $transaction->charge = 0;
                $transaction->post_balance = $oct1Profit - $withdrawn;
                $transaction->trx_type = '-';
                $transaction->trx = $trx;
                $transaction->details = '600.00 USD withdrawn';
                $transaction->remark = 'withdraw';
                $transaction->wallet_type = 'interest_wallet';
                $transaction->created_at = '2026-10-01 12:00:00';
                $transaction->updated_at = '2026-10-01 12:00:00';
                $transaction->save();
            }

            $originalInvestTrx = Transaction::query()
                ->where('user_id', $user->id)
                ->where('invest_id', $invest->id)
                ->where('remark', 'invest')
                ->orderBy('id')
                ->first();

            if ($originalInvestTrx && (float) $originalInvestTrx->amount > 110000) {
                $originalInvestTrx->amount = 100000;
                $originalInvestTrx->save();
            }
        });

        $invest->refresh();
        $user->refresh();

        $this->info('invested=' . $invest->initial_amount);
        $this->info('current=' . $invest->amount);
        $this->info('pre_upgrade_profit=' . $invest->paid);
        $this->info('next=' . $invest->next_time);
        $this->info('interest_wallet=' . $user->interest_wallet);

        return self::SUCCESS;
    }
}
