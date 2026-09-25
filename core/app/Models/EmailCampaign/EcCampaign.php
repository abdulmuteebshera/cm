<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcCampaign extends Model
{
    protected $table = 'ec_campaigns';

    protected $fillable = [
        'ec_user_id',
        'ec_admin_id',
        'ec_template_id',
        'ec_group_id',
        'name',
        'subject',
        'body_html',
        'body_text',
        'status',
        'send_delay_seconds',
        'sent_count',
        'failed_count',
        'total_recipients',
        'next_send_at',
        'started_at',
        'paused_at',
        'completed_at',
    ];

    protected $casts = [
        'send_delay_seconds' => 'integer',
        'sent_count'         => 'integer',
        'failed_count'       => 'integer',
        'total_recipients'   => 'integer',
        'next_send_at'       => 'datetime',
        'started_at'         => 'datetime',
        'paused_at'          => 'datetime',
        'completed_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(EcUser::class, 'ec_user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(EcAdmin::class, 'ec_admin_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EcTemplate::class, 'ec_template_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(EcGroup::class, 'ec_group_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EcCampaignRecipient::class, 'ec_campaign_id');
    }

    public function pendingCount(): int
    {
        return max(0, (int) $this->total_recipients - (int) $this->sent_count - (int) $this->failed_count);
    }

    public function remainingCount(): int
    {
        return $this->recipients()->where('status', 'pending')->count();
    }

    public function estimatedSecondsRemaining(): int
    {
        return $this->remainingCount() * (int) ($this->send_delay_seconds ?: 10);
    }

    public function syncTotals(): void
    {
        $this->total_recipients = $this->recipients()->count();
        $this->sent_count = $this->recipients()->where('status', 'sent')->count();
        $this->failed_count = $this->recipients()->whereIn('status', ['failed', 'skipped'])->count();
        $this->save();
    }
}
