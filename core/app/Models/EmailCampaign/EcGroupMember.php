<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcGroupMember extends Model
{
    protected $table = 'ec_group_members';

    protected $fillable = ['ec_group_id', 'email', 'name', 'merge_data'];

    protected $casts = ['merge_data' => 'array'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(EcGroup::class, 'ec_group_id');
    }
}
