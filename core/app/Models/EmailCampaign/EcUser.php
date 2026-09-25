<?php

namespace App\Models\EmailCampaign;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcUser extends Authenticatable
{
    protected $table = 'ec_users';

    protected $fillable = ['name', 'username', 'email', 'password', 'status', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'status'         => 'integer',
        'last_login_at'  => 'datetime',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(EcGroup::class, 'ec_user_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EcCampaign::class, 'ec_user_id');
    }
}
