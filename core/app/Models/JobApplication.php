<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $guarded = ['id'];

    public const STATUS_PENDING     = 0;
    public const STATUS_REVIEWED    = 1;
    public const STATUS_SHORTLISTED = 2;
    public const STATUS_REJECTED    = 3;

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function statusBadge(): string
    {
        $badges = [
            self::STATUS_PENDING     => '<span class="badge badge--warning">' . trans('Pending') . '</span>',
            self::STATUS_REVIEWED    => '<span class="badge badge--primary">' . trans('Reviewed') . '</span>',
            self::STATUS_SHORTLISTED => '<span class="badge badge--success">' . trans('Shortlisted') . '</span>',
            self::STATUS_REJECTED    => '<span class="badge badge--danger">' . trans('Rejected') . '</span>',
        ];

        return $badges[$this->application_status] ?? $badges[self::STATUS_PENDING];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING     => 'Pending',
            self::STATUS_REVIEWED    => 'Reviewed',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_REJECTED    => 'Rejected',
        ];
    }

    public function resumePath(): string
    {
        return assetFilesystemPath(getFilePath('jobResume') . '/' . $this->resume);
    }
}
