<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPeriodReturn extends Model
{
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected $casts = [
        'return_percent' => 'float',
        'total_payout'   => 'float',
        'period_start'   => 'date',
        'period_end'     => 'date',
        'payout_date'    => 'date',
        'approved_at'    => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payoutItems()
    {
        return $this->hasMany(PeriodPayoutItem::class, 'plan_period_return_id');
    }

    public function periodLabel(): string
    {
        if ($this->payout_date) {
            return 'Payout ' . $this->payout_date->format('M d, Y');
        }

        return $this->plan
            ? $this->plan->periodLabel((int) $this->year, (int) $this->period_index)
            : 'P' . $this->period_index . ' ' . $this->year;
    }

    public function isApprovable(): bool
    {
        return $this->payout_status === self::STATUS_PENDING;
    }

    public function scopePending($query)
    {
        return $query->where('payout_status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('payout_status', self::STATUS_APPROVED);
    }
}
