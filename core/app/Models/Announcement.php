<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];
}
