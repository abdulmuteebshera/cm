<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class PortfolioAllocation extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('percentage')->orderBy('id');
    }
}
