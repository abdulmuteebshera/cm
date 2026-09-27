<?php

namespace App\Support\EmailCampaign;

use App\Models\EmailCampaign\EcCampaign;
use App\Models\EmailCampaign\EcCampaignRecipient;
use App\Models\EmailCampaign\EcGroupMember;

class EcCampaignRecipients
{
    /**
     * @return 'added'|'duplicate'
     */
    public static function addOne(
        EcCampaign $campaign,
        string $email,
        ?string $name = null,
        ?array $mergeData = null
    ): string {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'duplicate';
        }

        $exists = EcCampaignRecipient::query()
            ->where('ec_campaign_id', $campaign->id)
            ->where('email', $email)
            ->exists();

        if ($exists) {
            return 'duplicate';
        }

        EcCampaignRecipient::query()->create([
            'ec_campaign_id' => $campaign->id,
            'email'          => $email,
            'name'           => $name,
            'merge_data'     => $mergeData,
            'status'         => 'pending',
        ]);

        return 'added';
    }

    /**
     * @param array<int, array{email: string, name: ?string, merge_data: ?array}> $rows
     * @return array{added: int, duplicates: int}
     */
    public static function addMany(EcCampaign $campaign, array $rows): array
    {
        $added = 0;
        $duplicates = 0;

        foreach ($rows as $row) {
            $result = static::addOne(
                $campaign,
                $row['email'],
                $row['name'] ?? null,
                $row['merge_data'] ?? null
            );
            if ($result === 'added') {
                $added++;
            } else {
                $duplicates++;
            }
        }

        $campaign->syncTotals();

        return ['added' => $added, 'duplicates' => $duplicates];
    }

    public static function syncFromGroup(EcCampaign $campaign): array
    {
        if (!$campaign->ec_group_id) {
            return ['added' => 0, 'duplicates' => 0];
        }

        $members = EcGroupMember::query()->where('ec_group_id', $campaign->ec_group_id)->get();
        $rows = $members->map(fn (EcGroupMember $m) => [
            'email'      => $m->email,
            'name'       => $m->name,
            'merge_data' => $m->merge_data,
        ])->all();

        return static::addMany($campaign, $rows);
    }

}
