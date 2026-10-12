<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinkedinProspect extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'do_not_contact' => 'boolean',
        'outreach_date'  => 'date',
        'redraft_on'     => 'date',
        'researched_at'  => 'datetime',
        'response_at'    => 'datetime',
        'qualified_at'   => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(LinkedinMessage::class, 'prospect_id');
    }

    public static function normalizeProfileUrl(string $url): string
    {
        $url = trim($url);
        $bare = strtok($url, '?') ?: $url;
        $bare = strtok($bare, '#') ?: $bare;

        if (!preg_match('#^https?://((www|[a-z]{2})\.)?linkedin\.com/in/[A-Za-z0-9\-_%]+/?$#i', $bare)) {
            throw new \InvalidArgumentException('Use a public LinkedIn profile URL, such as https://www.linkedin.com/in/name');
        }

        $parts = parse_url($bare);
        $host = strtolower($parts['host'] ?? 'www.linkedin.com');
        if ($host === 'linkedin.com') {
            $host = 'www.linkedin.com';
        }
        $path = rtrim($parts['path'] ?? '', '/');

        return 'https://'.$host.$path;
    }

    public function statusLabel(): string
    {
        return match ($this->outreach_status) {
            'new'              => 'Queued',
            'needs_title'      => 'Needs a role',
            'not_a_fit'        => 'Not a fit',
            'too_small'        => 'Below $20k fit',
            'redraft'          => 'Rewrite tomorrow',
            'pending_approval' => 'Needs approval',
            'approved'         => 'Ready to send',
            'sent'             => 'Invite sent',
            'accepted'         => 'Connected',
            'replied'          => 'Replied',
            'qualified'        => 'Qualified',
            'declined'         => 'Declined',
            'opted_out'        => 'Opted out',
            default            => ucfirst(str_replace('_', ' ', (string) $this->outreach_status)),
        };
    }

    public function statusClass(): string
    {
        return match ($this->outreach_status) {
            'pending_approval' => 'warning',
            'approved', 'accepted', 'replied' => 'primary',
            'qualified' => 'success',
            'declined', 'opted_out' => 'danger',
            'not_a_fit', 'needs_title', 'too_small' => 'dark',
            default => 'info',
        };
    }

    public function canEditRecord(): bool
    {
        return !$this->do_not_contact && in_array($this->outreach_status, ['new', 'needs_title', 'not_a_fit', 'too_small', 'redraft'], true);
    }

    public function canDelete(): bool
    {
        return $this->outreach_date === null
            && !$this->messages()->where('status', 'sent')->exists()
            && in_array($this->outreach_status, ['new', 'needs_title', 'not_a_fit', 'too_small', 'redraft', 'pending_approval', 'approved'], true);
    }
}
