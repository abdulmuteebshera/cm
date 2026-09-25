<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmPitchDeck extends Model
{
    protected $table = 'crm_pitch_decks';

    protected $guarded = ['id'];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(CrmStaff::class, 'uploaded_by');
    }
}
