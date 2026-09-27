<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoPageView extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'duration_seconds' => 'int',
        'scroll_depth'     => 'int',
        'entered_at'       => 'datetime',
        'left_at'          => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(SeoVisitorSession::class, 'seo_visitor_session_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(SeoEvent::class, 'seo_page_view_id');
    }

    public function getTitleAttribute()
    {
        return $this->attributes['page_title'] ?? null;
    }

    public function getExitedAtAttribute()
    {
        return $this->left_at;
    }

    public function getDurationLabelAttribute(): string
    {
        return SeoVisitorSession::formatSeconds((int) $this->duration_seconds);
    }
}
