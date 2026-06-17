<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('username', 'muteeb')->first();
if (!$user) { echo "no user\n"; exit; }
echo "User {$user->id} interest_wallet={$user->interest_wallet}\n";
echo "PeriodPayoutItems: " . App\Models\PeriodPayoutItem::where('user_id', $user->id)->count() . "\n";
echo "Invests:\n";
foreach (App\Models\Invest::where('user_id', $user->id)->get() as $i) {
    echo "  #{$i->id} plan={$i->plan_id} amount={$i->amount} paid={$i->paid} status={$i->status}\n";
}
echo "PlanPeriodReturns:\n";
foreach (App\Models\PlanPeriodReturn::with('plan')->get() as $r) {
    echo "  #{$r->id} {$r->plan->name} {$r->periodLabel()} status={$r->payout_status} pct={$r->return_percent}\n";
}
