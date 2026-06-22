<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isWelcome(): bool
    {
        return $this->type === 'welcome';
    }

    public function title(): string
    {
        return $this->isWelcome() ? 'Certificate of Membership' : 'Certificate of Strategy Membership';
    }
}
