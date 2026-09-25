<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcCampaignRecipient extends Model
{
    protected $table = 'ec_campaign_recipients';

    protected $fillable = [
        'ec_campaign_id',
        'email',
        'name',
        'merge_data',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'merge_data' => 'array',
        'sent_at'    => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EcCampaign::class, 'ec_campaign_id');
    }
}
