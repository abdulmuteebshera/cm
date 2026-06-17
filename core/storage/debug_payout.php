<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$invest = App\Models\Invest::find(15);
$period = App\Models\PlanPeriodReturn::find(2);
$preview = App\Lib\StrategyPayoutService::previewPeriodPayout($period);
echo "Invest created: {$invest->created_at}\n";
echo "Period: {$period->period_start} to {$period->period_end}\n";
echo "Rate: {$period->return_percent}%\n";
echo "Preview total: {$preview['total']}\n";
foreach ($preview['lines'] as $line) {
    echo "  amount={$line['amount']} invest={$line['invest']->amount}\n";
}

$plan = $period->plan;
$breakdown = App\Lib\StrategyPayoutService::calculateInvestPeriodReturnBreakdown(
    $invest,
    $plan,
    12,
    $period->period_start,
    $period->period_end
);
echo "breakdown main={$breakdown['main']} extra={$breakdown['extra']} total={$breakdown['total']}\n";
$elig = App\Lib\StrategyPayoutService::investPeriodEligibility($invest, $period->period_start, $period->period_end);
echo "eligibility: " . json_encode($elig) . "\n";
echo "Simple 12%: " . (10000 * 0.12) . "\n";
