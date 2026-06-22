<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    use GlobalStatus;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Privacy-masked display name. Keeps the first two characters and hides
     * the rest, e.g. "Michael" => "Mi****".
     */
    public function maskedName(): Attribute
    {
        return Attribute::make(
            get: fn() => self::maskName($this->name),
        );
    }

    public static function maskName(?string $name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return '****';
        }

        $visible = mb_substr($name, 0, 2);

        return $visible . '****';
    }
}
