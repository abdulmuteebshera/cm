<?php

namespace App\Models;

use App\Support\Seo\SeoSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta'        => 'array',
        'occurred_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(SeoVisitorSession::class, SeoSchema::sessionFk('seo_events'));
    }

    public function pageView(): BelongsTo
    {
        return $this->belongsTo(SeoPageView::class, SeoSchema::pageViewFk());
    }

    public function getTargetUrlAttribute()
    {
        return $this->attributes['href'] ?? $this->attributes['target_url'] ?? null;
    }

    public function getOccurredAtAttribute($value)
    {
        if ($value) {
            return $this->asDateTime($value);
        }
        return $this->created_at;
    }
}
