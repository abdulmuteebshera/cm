<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'enabled'                 => 'boolean',
        'require_approval'        => 'boolean',
        'posting_enabled'         => 'boolean',
        'daily_connection_limit'  => 'integer',
        'post_interval_days'      => 'integer',
        'client_secret'           => 'encrypted',
        'access_token'            => 'encrypted',
        'refresh_token'           => 'encrypted',
        'token_expires_at'        => 'datetime',
        'connected_at'            => 'datetime',
        'last_daily_run_at'       => 'datetime',
    ];

    public static function current(): self
    {
        $existing = static::query()->orderBy('id')->first();
        if ($existing) {
            return $existing;
        }

        return static::query()->create([
            'enabled'                => true,
            'require_approval'       => true,
            'daily_connection_limit' => 20,
            'posting_enabled'        => true,
            'post_interval_days'     => 3,
        ]);
    }

    public function isConnected(): bool
    {
        return $this->access_token !== null && $this->member_id !== null;
    }

    public function audienceNeedles(): array
    {
        $defaults = [
            'founder',
            'co-founder',
            'cofounder',
            'owner',
            'business owner',
            'ceo',
            'chief executive',
            'managing director',
            'proprietor',
            'president',
            'chairman',
            'chairwoman',
            'family office',
            'managing partner',
            'general partner',
        ];

        $extra = preg_split('/[,\\n]+/', (string) $this->audience_titles) ?: [];
        $extra = array_filter(array_map(static fn ($title) => strtolower(trim($title)), $extra));

        return array_values(array_unique(array_merge($defaults, $extra)));
    }
}
