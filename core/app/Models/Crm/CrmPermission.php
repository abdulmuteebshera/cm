<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CrmPermission extends Model
{
    protected $table = 'crm_permissions';

    protected $guarded = ['id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(CrmRole::class, 'crm_role_permission', 'crm_permission_id', 'crm_role_id');
    }
}
