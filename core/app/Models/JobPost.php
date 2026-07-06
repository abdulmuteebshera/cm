<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
