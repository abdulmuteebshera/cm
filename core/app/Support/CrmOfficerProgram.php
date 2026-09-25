<?php

namespace App\Support;

class CrmOfficerProgram
{
    /** Upfront commission on newly funded capital */
    public const UPFRONT_RATE = 2.0;

    /** Annual retention if capital stays invested */
    public const RETENTION_RATE = 1.0;

    public static function leadStages(): array
    {
        return [
            'lead'               => 'New Lead',
            'contacted'          => 'Contacted',
            'meeting_done'       => 'Meeting Done',
            'proposal_sent'      => 'Proposal Sent',
            'investment_pending' => 'Investment Pending',
            'funded'             => 'Funded / Active',
            'lost_funding'       => 'Lost Funding',
            'closed'             => 'Closed',
        ];
    }

    public static function rankingTiers(): array
    {
        return [
            ['slug' => 'associate',   'label' => 'Associate',   'aum' => 500_000],
            ['slug' => 'advisor',     'label' => 'Advisor',     'aum' => 1_000_000],
            ['slug' => 'senior',      'label' => 'Senior Advisor', 'aum' => 2_500_000],
            ['slug' => 'principal',   'label' => 'Principal',   'aum' => 5_000_000],
            ['slug' => 'director',    'label' => 'Director',    'aum' => 10_000_000],
            ['slug' => 'vp',          'label' => 'Vice President', 'aum' => 25_000_000],
            ['slug' => 'evp',         'label' => 'Executive VP', 'aum' => 50_000_000],
            ['slug' => 'managing',    'label' => 'Managing Director', 'aum' => 100_000_000],
        ];
    }

    public static function tierForAum(float $aum): array
    {
        $current = ['slug' => 'trainee', 'label' => 'Trainee', 'aum' => 0];
        $next    = self::rankingTiers()[0];

        foreach (self::rankingTiers() as $i => $tier) {
            if ($aum >= $tier['aum']) {
                $current = $tier;
                $next    = self::rankingTiers()[$i + 1] ?? null;
            } else {
                $next = $tier;
                break;
            }
        }

        if ($aum >= 100_000_000) {
            $next = null;
        }

        $progress = 100;
        if ($next) {
            $base = $current['aum'];
            $span = max(1, $next['aum'] - $base);
            $progress = min(100, round((($aum - $base) / $span) * 100, 1));
        }

        return [
            'current'  => $current,
            'next'     => $next,
            'progress' => $progress,
            'aum'      => $aum,
        ];
    }

    public static function upfrontCommission(float $amount): float
    {
        return round($amount * (self::UPFRONT_RATE / 100), 2);
    }

    public static function retentionCommission(float $amount): float
    {
        return round($amount * (self::RETENTION_RATE / 100), 2);
    }
}
