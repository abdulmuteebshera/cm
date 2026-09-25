<?php

namespace App\Models\EmailCampaign;

use Illuminate\Foundation\Auth\User as Authenticatable;

class EcAdmin extends Authenticatable
{
    protected $table = 'ec_admins';

    protected $fillable = ['username', 'password', 'name', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'status' => 'integer',
    ];
}
