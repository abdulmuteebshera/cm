<?php

namespace App\Lib;

use App\Models\Certificate;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Issues and manages member certificates. A welcome certificate is created when
 * an account is created, and a strategy membership certificate is created the
 * first time a user invests in a given strategy.
 */
class CertificateService
{
    /**
     * Ensure the user has a welcome certificate. Returns it.
     */
    public static function ensureWelcome(User $user): Certificate
    {
        $certificate = Certificate::where('user_id', $user->id)->where('type', 'welcome')->first();

        if ($certificate) {
            return $certificate;
        }

        return self::create($user, 'welcome', null, $user->created_at);
    }

    /**
     * Ensure the user has a membership certificate for the given strategy/plan.
     * Only strategy plans receive certificates; one certificate per plan.
     */
    public static function ensureForPlan(User $user, Plan $plan, $issuedAt = null): ?Certificate
    {
        $certificate = Certificate::where('user_id', $user->id)
            ->where('type', 'investment')
            ->where('plan_id', $plan->id)
            ->first();

        if ($certificate) {
            return $certificate;
        }

        return self::create($user, 'investment', $plan, $issuedAt);
    }

    /**
     * Generate any missing certificates for the user (welcome + every strategy
     * they have invested in). Safe to call repeatedly.
     */
    public static function syncForUser(User $user): void
    {
        self::ensureWelcome($user);

        $invests = Invest::where('user_id', $user->id)->with('plan')->get();

        foreach ($invests as $invest) {
            if ($invest->plan) {
                self::ensureForPlan($user, $invest->plan, $invest->created_at);
            }
        }
    }

    private static function create(User $user, string $type, ?Plan $plan, $issuedAt = null): Certificate
    {
        $certificate                     = new Certificate();
        $certificate->user_id            = $user->id;
        $certificate->type               = $type;
        $certificate->plan_id            = $plan?->id;
        $certificate->strategy_name      = $plan?->name;
        $certificate->certificate_number = self::generateNumber($type);
        $certificate->uid                = self::generateUid();
        $certificate->issued_at          = $issuedAt ?: now();
        $certificate->save();

        return $certificate;
    }

    private static function generateNumber(string $type): string
    {
        $prefix = $type === 'welcome' ? 'WLC' : 'STR';

        do {
            $number = 'CMC-' . $prefix . '-' . strtoupper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }

    private static function generateUid(): string
    {
        do {
            $uid = Str::lower(Str::random(32));
        } while (Certificate::where('uid', $uid)->exists());

        return $uid;
    }
}
