<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmRole extends Model
{
    protected $table = 'crm_roles';

    protected $guarded = ['id'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(CrmPermission::class, 'crm_role_permission', 'crm_role_id', 'crm_permission_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(CrmStaff::class, 'crm_role_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
