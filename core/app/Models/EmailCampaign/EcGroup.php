<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcGroup extends Model
{
    protected $table = 'ec_groups';

    protected $fillable = ['ec_user_id', 'ec_admin_id', 'name', 'description', 'is_private'];

    protected $casts = [
        'is_private' => 'integer',
    ];

    public function isSharedAdminGroup(): bool
    {
        return $this->ec_admin_id !== null && (int) $this->is_private === 0;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(EcUser::class, 'ec_user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(EcAdmin::class, 'ec_admin_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(EcGroupMember::class, 'ec_group_id');
    }
}
