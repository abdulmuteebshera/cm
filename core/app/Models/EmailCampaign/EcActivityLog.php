<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;

class EcActivityLog extends Model
{
    protected $table = 'ec_activity_logs';

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'meta',
    ];

    protected $casts = ['meta' => 'array'];

    public static function record(
        string $actorType,
        ?int $actorId,
        string $action,
        ?string $description = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?array $meta = null
    ): self {
        return static::query()->create([
            'actor_type'   => $actorType,
            'actor_id'     => $actorId,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'meta'         => $meta,
        ]);
    }
}
