<?php

namespace App\Models;

use App\Support\Seo\SeoSchema;
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
        'exited_at'        => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(SeoVisitorSession::class, SeoSchema::sessionFk());
    }

    public function events(): HasMany
    {
        return $this->hasMany(SeoEvent::class, SeoSchema::pageViewFk());
    }

    public function getTitleAttribute()
    {
        return $this->attributes['page_title'] ?? $this->attributes['title'] ?? null;
    }

    public function getExitedAtAttribute()
    {
        return $this->attributes['left_at'] ?? $this->attributes['exited_at'] ?? null;
    }

    public function getDurationLabelAttribute(): string
    {
        return SeoVisitorSession::formatSeconds((int) $this->duration_seconds);
    }
}
