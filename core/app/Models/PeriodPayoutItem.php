<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodPayoutItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount'       => 'float',
        'rate_percent' => 'float',
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
}
