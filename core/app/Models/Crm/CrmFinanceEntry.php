<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFinanceEntry extends Model
{
    protected $table = 'crm_finance_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(CrmStaff::class, 'created_by');
    }
}
