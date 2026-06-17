<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPayoutItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount'       => 'float',
        'rate_percent' => 'float',
    ];

    public function planWeeklyReturn()
    {
        return $this->belongsTo(PlanWeeklyReturn::class, 'plan_weekly_return_id');
    }

    public function invest()
    {
        return $this->belongsTo(Invest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
