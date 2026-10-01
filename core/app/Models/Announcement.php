<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];

    protected $casts = [
        'status'            => 'integer',
        'show_on_dashboard' => 'integer',
    ];

    public function formattedContent(): string
    {
        $html = e((string) $this->content);
        $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html) ?? $html;

        return nl2br($html);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOnDashboard($query)
    {
        return $query->active()->where('show_on_dashboard', 1);
    }
}
