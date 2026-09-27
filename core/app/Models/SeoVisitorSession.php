<?php

namespace App\Models;

use App\Support\Seo\SeoSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeoVisitorSession extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'latitude'         => 'float',
        'longitude'        => 'float',
        'page_count'       => 'int',
        'clicks'           => 'int',
        'duration_seconds' => 'int',
        'engaged_seconds'  => 'int',
        'is_bounce'        => 'bool',
        'started_at'       => 'datetime',
        'first_seen_at'    => 'datetime',
        'last_seen_at'     => 'datetime',
    ];

    public function pageViews(): HasMany
    {
        return $this->hasMany(SeoPageView::class, SeoSchema::sessionFk());
    }

    public function events(): HasMany
    {
        return $this->hasMany(SeoEvent::class, SeoSchema::sessionFk('seo_events'));
    }

    public function getIpAttribute()
    {
        return $this->attributes['ip_address'] ?? $this->attributes['ip'] ?? null;
    }

    public function getVisitorIdAttribute()
    {
        return $this->attributes['visitor_key'] ?? $this->attributes['visitor_id'] ?? null;
    }

    public function getLandingPageAttribute()
    {
        return $this->attributes['landing_path'] ?? $this->attributes['landing_page'] ?? null;
    }

    public function getExitPageAttribute()
    {
        return $this->attributes['exit_path'] ?? $this->attributes['exit_page'] ?? null;
    }

    public function getPageViewsAttribute()
    {
        return $this->attributes['page_count'] ?? $this->attributes['page_views'] ?? 0;
    }

    public function getFirstSeenAtAttribute()
    {
        return $this->attributes['started_at'] ?? $this->attributes['first_seen_at'] ?? $this->created_at;
    }

    public function getCampaignAttribute()
    {
        return $this->attributes['utm_campaign'] ?? $this->attributes['campaign'] ?? null;
    }

    public function getMediumAttribute()
    {
        return $this->attributes['utm_medium'] ?? $this->attributes['medium'] ?? null;
    }

    public function getLatAttribute()
    {
        return $this->attributes['latitude'] ?? null;
    }

    public function getLngAttribute()
    {
        return $this->attributes['longitude'] ?? null;
    }

    public function getDurationLabelAttribute(): string
    {
        $seconds = (int) ($this->engaged_seconds ?: $this->duration_seconds);
        return self::formatSeconds($seconds);
    }

    public static function formatSeconds(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);
        $s = $seconds % 60;
        if ($h > 0) {
            return sprintf('%dh %02dm %02ds', $h, $m, $s);
        }
        if ($m > 0) {
            return sprintf('%dm %02ds', $m, $s);
        }
        return sprintf('%ds', $s);
    }
}
