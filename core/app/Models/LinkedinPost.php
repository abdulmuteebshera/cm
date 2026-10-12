<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinPost extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_for' => 'date',
        'published_at'  => 'datetime',
    ];

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_approval' => 'Needs approval',
            'approved'         => 'Approved',
            'published'        => 'Published',
            'rejected'         => 'Rejected',
            'failed'           => 'Failed',
            default            => ucfirst((string) $this->status),
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'pending_approval' => 'warning',
            'approved'         => 'primary',
            'published'        => 'success',
            'rejected', 'failed' => 'danger',
            default            => 'dark',
        };
    }
}
