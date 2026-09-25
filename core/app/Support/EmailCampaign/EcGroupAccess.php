<?php

namespace App\Support\EmailCampaign;

use App\Models\EmailCampaign\EcGroup;
use Illuminate\Database\Eloquent\Builder;

class EcGroupAccess
{
    /** Groups a campaign user may list and use in campaigns. */
    public static function queryForUser(int $ecUserId): Builder
    {
        return EcGroup::query()->where(function (Builder $q) use ($ecUserId): void {
            $q->where('ec_user_id', $ecUserId)
                ->orWhere(function (Builder $inner): void {
                    $inner->whereNotNull('ec_admin_id')->where('is_private', 0);
                });
        });
    }

    public static function userCanUse(int $ecUserId, int $groupId): bool
    {
        return static::queryForUser($ecUserId)->whereKey($groupId)->exists();
    }

    public static function userCanManage(int $ecUserId, EcGroup $group): bool
    {
        return (int) $group->ec_user_id === $ecUserId;
    }
}
