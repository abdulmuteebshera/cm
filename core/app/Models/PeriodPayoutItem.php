<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodPayoutItem extends Model
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected $casts = [
        'amount'            => 'float',
        'calculated_amount' => 'float',
        'rate_percent'      => 'float',
        'amount_edited'     => 'boolean',
        'compounded'        => 'boolean',
        'approved_at'       => 'datetime',
    ];

    public function planPeriodReturn()
    {
        return $this->belongsTo(PlanPeriodReturn::class, 'plan_period_return_id');
    }

    public function invest()
    {
        return $this->belongsTo(Invest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isFinalized(): bool
    {
        return $this->isApproved() || $this->isRejected();
    }

    public function isCompounded(): bool
    {
        return (bool) $this->compounded;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
