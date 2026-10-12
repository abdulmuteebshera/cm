<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkedinMessage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
        'sent_at'     => 'datetime',
    ];

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(LinkedinProspect::class, 'prospect_id');
    }

    public function kindLabel(): string
    {
        return $this->kind === 'follow_up' ? 'Follow-up' : 'Connection request';
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending_approval', 'approved'], true);
    }
}
