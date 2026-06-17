<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanMonthlyReturn extends Model
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

    public function monthLabel(): string
    {
        return \Carbon\Carbon::create((int) $this->year, (int) $this->month, 1)->format('M Y');
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
