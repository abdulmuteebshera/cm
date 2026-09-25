<?php

namespace App\Models\Crm;

use App\Support\CrmAdminBridge;
use App\Support\CrmOfficerProgram;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmStaff extends Authenticatable
{
    protected $table = 'crm_staff';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'is_super'      => 'boolean',
        'lifetime_aum'  => 'float',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(CrmRole::class, 'crm_role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(CrmPermission::class, 'crm_staff_permission', 'crm_staff_id', 'crm_permission_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(CrmClient::class, 'owner_staff_id');
    }

    public function isSuper(): bool
    {
        return (bool) $this->is_super;
    }

    public function isManager(): bool
    {
        return ($this->role->portal ?? null) === 'manager' || ($this->role->slug ?? null) === 'manager';
    }

    public function portal(): string
    {
        if ($this->isSuper()) {
            return 'crm';
        }

        return $this->role->portal ?? 'crm';
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuper()) {
            return true;
        }

        // Per-manager Admin Desk modules: personal assignment only (not shared role defaults)
        if ($this->isManager() && str_starts_with($slug, 'admin.')) {
            $this->loadMissing('permissions');
            return $this->permissions->contains('slug', $slug);
        }

        // Personal grants — additive
        $this->loadMissing('permissions');
        if ($this->permissions->contains('slug', $slug)) {
            return true;
        }

        $this->loadMissing('role.permissions');

        return (bool) optional($this->role)->permissions?->contains('slug', $slug);
    }

    public function adminDeskPermissionSlugs(): array
    {
        if ($this->isSuper()) {
            return array_keys(CrmAdminBridge::permissionMap());
        }

        $this->loadMissing('permissions', 'role.permissions');

        if ($this->isManager()) {
            return $this->permissions->pluck('slug')
                ->filter(fn ($s) => str_starts_with($s, 'admin.'))
                ->values()
                ->all();
        }

        $fromRole = optional($this->role)->permissions?->pluck('slug')->all() ?? [];
        $fromSelf = $this->permissions->pluck('slug')->all();

        return array_values(array_intersect(
            array_unique(array_merge($fromRole, $fromSelf)),
            array_keys(CrmAdminBridge::permissionMap())
        ));
    }

    public function fundedAum(): float
    {
        return (float) $this->clients()->whereIn('stage', ['funded', 'active'])->sum('committed_aum');
    }

    public function rankingSnapshot(): array
    {
        $aum = max((float) $this->lifetime_aum, $this->fundedAum());

        return CrmOfficerProgram::tierForAum($aum);
    }

    public function canAccessPortal(string $portal): bool
    {
        if ($this->isSuper()) {
            return true;
        }

        return $this->portal() === $portal;
    }

    public function homeRoute(): string
    {
        return match ($this->portal()) {
            'manager' => 'crm.manager.dashboard',
            'agent'   => 'crm.agent.dashboard',
            'trader'  => 'crm.trader.dashboard',
            'finance' => 'crm.finance.dashboard',
            default   => 'crm.dashboard',
        };
    }

    public function loginRoute(): string
    {
        return match ($this->portal()) {
            'manager' => 'crm.manager.login',
            'agent'   => 'crm.agent.login',
            'trader'  => 'crm.trader.login',
            'finance' => 'crm.finance.login',
            default   => 'crm.login',
        };
    }
}
