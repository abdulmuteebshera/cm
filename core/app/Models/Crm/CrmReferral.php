<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmReferral extends Model
{
    protected $table = 'crm_referrals';

    protected $guarded = ['id'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(CrmStaff::class, 'staff_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'client_id');
    }
}
