<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use GlobalStatus;

    public const MODE_LEGACY   = 0;
    public const MODE_STRATEGY = 1;

    public const FREQ_QUARTERLY   = 'quarterly';
    public const FREQ_SEMI_ANNUAL = 'semi_annual';
    public const FREQ_YEARLY      = 'yearly';

    public static function payoutFrequencies(): array
    {
        return [
            self::FREQ_QUARTERLY   => 'Quarterly',
            self::FREQ_SEMI_ANNUAL => '6 Months',
            self::FREQ_YEARLY      => 'Yearly',
        ];
    }

    public function isStrategy(): bool
    {
        return (int) ($this->plan_mode ?? 0) === self::MODE_STRATEGY;
    }

    public function payoutFrequencyLabel(): string
    {
        return self::payoutFrequencies()[$this->payout_frequency ?? self::FREQ_QUARTERLY] ?? 'Quarterly';
    }

    public function periodLabel(int $year, int $periodIndex): string
    {
        return match ($this->payout_frequency ?? self::FREQ_QUARTERLY) {
            self::FREQ_SEMI_ANNUAL => 'H' . $periodIndex . ' ' . $year,
            self::FREQ_YEARLY      => (string) $year,
            default                => 'Q' . $periodIndex . ' ' . $year,
        };
    }

    public function invests()
    {
        return $this->hasMany(Invest::class);
    }

    public function weeklyReturns()
    {
        return $this->hasMany(PlanWeeklyReturn::class);
    }

    public function monthlyReturns()
    {
        return $this->hasMany(PlanMonthlyReturn::class);
    }

    public function usesMonthlyPerformanceTracking(): bool
    {
        return in_array(
            $this->payout_frequency ?? self::FREQ_QUARTERLY,
            [self::FREQ_SEMI_ANNUAL, self::FREQ_YEARLY],
            true
        );
    }

    public function periodReturns()
    {
        return $this->hasMany(PlanPeriodReturn::class);
    }

    public function strategyReports()
    {
        return $this->hasMany(PlanStrategyReport::class);
    }

    public function timeSetting()
    {
        return $this->belongsTo(TimeSetting::class);
    }
}
