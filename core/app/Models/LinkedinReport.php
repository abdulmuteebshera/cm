<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinReport extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'report_date' => 'date',
        'summary'     => 'array',
    ];
}
