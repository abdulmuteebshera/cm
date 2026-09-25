<?php

namespace App\Models\Crm;

use App\Support\CrmOfficerProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmClient extends Model
{
    protected $table = 'crm_clients';

    protected $guarded = ['id'];

    protected $casts = [
        'expected_aum'             => 'decimal:8',
        'committed_aum'            => 'decimal:8',
        'upfront_commission'       => 'decimal:8',
        'retention_commission_ytd' => 'decimal:8',
        'next_follow_up'           => 'date',
        'funded_on'                => 'date',
        'last_activity_at'         => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(CrmStaff::class, 'owner_staff_id');
    }

    public function stageLabel(): string
    {
        return CrmOfficerProgram::leadStages()[$this->stage] ?? ucfirst(str_replace('_', ' ', (string) $this->stage));
    }

    public function isFunded(): bool
    {
        return in_array($this->stage, ['funded', 'active'], true);
    }
}
