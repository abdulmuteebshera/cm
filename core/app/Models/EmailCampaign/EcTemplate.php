<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcTemplate extends Model
{
    protected $table = 'ec_templates';

    protected $fillable = ['name', 'subject', 'body_html', 'body_text', 'status'];

    protected $casts = ['status' => 'integer'];

    public function campaigns(): HasMany
    {
        return $this->hasMany(EcCampaign::class, 'ec_template_id');
    }
}
