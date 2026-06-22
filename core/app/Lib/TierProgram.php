<?php

namespace App\Lib;

/**
 * Membership / loyalty tier program. A user's tier is decided by the amount
 * they have invested in strategies. Tier thresholds, facilities (benefits) and
 * the comparison matrix are all defined here so they are easy to edit later.
 */
class TierProgram
{
    /** Ordered list of tiers (lowest → highest). `min` is the qualifying invested amount. */
    public static function tiers(): array
    {
        return [
            ['key' => 'bronze',        'name' => 'Bronze',        'emoji' => '🥉', 'min' => 10000,   'color' => '#cd7f32'],
            ['key' => 'silver',        'name' => 'Silver',        'emoji' => '🥈', 'min' => 25000,   'color' => '#9ca3af'],
            ['key' => 'gold',          'name' => 'Gold',          'emoji' => '🥇', 'min' => 50000,   'color' => '#d4a017'],
            ['key' => 'platinum',      'name' => 'Platinum',      'emoji' => '💎', 'min' => 100000,  'color' => '#22a7c4'],
            ['key' => 'diamond',       'name' => 'Diamond',       'emoji' => '👑', 'min' => 250000,  'color' => '#8b5cf6'],
            ['key' => 'elite',         'name' => 'Elite',         'emoji' => '🏆', 'min' => 500000,  'color' => '#ef4444'],
            ['key' => 'institutional', 'name' => 'Institutional', 'emoji' => '🏛', 'min' => 1000000, 'color' => '#0ea5e9'],
        ];
    }

    /**
     * Facility comparison matrix. Each row has a `label` and a `values` array
     * aligned to the order of tiers() (7 entries:
     * Bronze, Silver, Gold, Platinum, Diamond, Elite, Institutional). A value may be:
     *   - true  → shown as an included checkmark
     *   - false → shown as not included
     *   - string → shown as-is (e.g. "10%", "Advanced")
     */
    public static function facilities(): array
    {
        return [
            ['label' => 'Investor Dashboard',              'values' => [true, true, true, true, true, true, true]],
            ['label' => 'Portfolio Analytics',             'values' => ['Basic', 'Basic', 'Advanced', 'Advanced', 'Premium', 'Premium+', 'Institutional']],
            ['label' => 'Management Fee Discount',         'values' => ['0%', '0%', '0%', '5%', '10%', '15%', '20%']],
            ['label' => 'Quarterly Performance Reports',   'values' => [false, true, true, true, true, true, true]],
            ['label' => 'Annual Reporting',                'values' => [false, true, true, true, true, true, true]],
            ['label' => 'Priority Support',                'values' => [false, false, true, true, true, true, true]],
            ['label' => 'Exclusive Investor Webinars',     'values' => [false, false, true, true, true, true, true]],
            ['label' => 'Early Access to New Strategies',  'values' => [false, false, false, true, true, true, true]],
            ['label' => 'Crownmaire Reserve Access',       'values' => [false, false, false, false, true, true, true]],
            ['label' => 'Dedicated Account Manager',       'values' => [false, false, false, false, true, true, true]],
            ['label' => 'Priority Allocation Rights',      'values' => [false, false, false, false, true, true, true]],
            ['label' => 'Diamond Club Status',             'values' => [false, false, false, false, true, true, true]],
            ['label' => 'Direct Portfolio Review',         'values' => [false, false, false, false, true, true, true]],
            ['label' => 'Founder Circle Membership',       'values' => [false, false, false, false, false, true, true]],
            ['label' => 'Dedicated Relationship Manager',  'values' => [false, false, false, false, false, true, true]],
            ['label' => 'First Access to New Products',    'values' => [false, false, false, false, false, true, true]],
            ['label' => 'Invitation-Only Opportunities',   'values' => [false, false, false, false, false, true, true]],
            ['label' => 'Institutional Reporting',         'values' => [false, false, false, false, false, false, true]],
            ['label' => 'Customized Investment Solutions', 'values' => [false, false, false, false, false, false, true]],
        ];
    }

    /**
     * Resolve a user's tier standing from their qualifying invested amount.
     * Returns the current tier (null if below the first threshold), the next
     * tier, progress % toward the next tier and the amount still needed.
     */
    public static function resolve(float $amount): object
    {
        $tiers        = self::tiers();
        $currentIndex = -1;

        foreach ($tiers as $i => $tier) {
            if ($amount >= $tier['min']) {
                $currentIndex = $i;
            }
        }

        $current = $currentIndex >= 0 ? $tiers[$currentIndex] : null;
        $next    = $tiers[$currentIndex + 1] ?? null;

        if ($next) {
            $base     = $current ? $current['min'] : 0;
            $span     = $next['min'] - $base;
            $progress = $span > 0 ? (($amount - $base) / $span) * 100 : 0;
            $needed   = max(0, $next['min'] - $amount);
        } else {
            $progress = 100;
            $needed   = 0;
        }

        return (object) [
            'amount'        => $amount,
            'current'       => $current,
            'current_index' => $currentIndex,
            'next'          => $next,
            'progress'      => round(min(100, max(0, $progress)), 2),
            'needed'        => round($needed, 2),
            'is_top'        => $next === null && $current !== null,
        ];
    }
}
