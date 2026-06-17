<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanStrategyReport extends Model
{
    protected $fillable = [
        'plan_id',
        'year',
        'file_path',
        'original_name',
        'uploaded_by',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function displayName(): string
    {
        if ($this->original_name) {
            return $this->original_name;
        }

        $planName = $this->plan?->name ?? 'Strategy';

        return $planName . ' ' . $this->year . ' Report.pdf';
    }
}
