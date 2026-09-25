<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFundPosition extends Model
{
    protected $table = 'crm_fund_positions';

    protected $guarded = ['id'];

    protected $casts = [
        'opened_on' => 'date',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(CrmStaff::class, 'managed_by');
    }
}
