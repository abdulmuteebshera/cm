<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanWeeklyReturn extends Model
{
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected $casts = [
        'return_percent' => 'float',
        'total_payout'   => 'float',
        'approved_at'    => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payoutItems()
    {
        return $this->hasMany(WeeklyPayoutItem::class, 'plan_weekly_return_id');
    }

    public function weekLabel(): string
    {
        return 'W' . $this->week . ' ' . $this->year;
    }

    public function isApprovable(): bool
    {
        return $this->payout_status === self::STATUS_PENDING && $this->return_percent != 0;
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
