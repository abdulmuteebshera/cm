<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    public function fullname(): Attribute
    {
        return new Attribute(
            get:fn() => $this->name,
        );
    }

    public function username(): Attribute
    {
        return new Attribute(
            get:fn() => $this->email,
        );
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == 0) {
                $html = '<span class="badge badge--success">' . trans("Open") . '</span>';
            } elseif ($this->status == 1) {
                $html = '<span class="badge badge--primary">' . trans("Answered") . '</span>';
            } elseif ($this->status == 2) {
                $html = '<span class="badge badge--warning">' . trans("Customer Reply") . '</span>';
            } elseif ($this->status == 3) {
                $html = '<span class="badge badge--dark">' . trans("Closed") . '</span>';
            }
            return $html;
        });
    }

    public function scopeClientTickets($query)
    {
        return $query->where('user_id', '>', 0);
    }

    public function scopeContactSubmissions($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', 0);
        });
    }

    public function isContactSubmission(): bool
    {
        return empty($this->user_id);
    }

    public static function inquiryReasons(): array
    {
        return ['Investment', 'Partnership', 'Other'];
    }

    public static function capitalRanges(): array
    {
        return [
            'Under $25,000',
            '$25,000 – $100,000',
            '$100,000 – $250,000',
            '$250,000 – $1,000,000',
            '$1,000,000 or more',
            'Prefer to discuss',
        ];
    }

    public function leadStatusBadge(): string
    {
        $map = [
            0 => ['New', 'success'],
            1 => ['Contacted', 'primary'],
            2 => ['Follow-up', 'warning'],
            3 => ['Closed', 'dark'],
        ];
        [$label, $color] = $map[(int) $this->status] ?? ['New', 'success'];

        return '<span class="badge badge--' . $color . '">' . e(trans($label)) . '</span>';
    }

    public function leadFields(): array
    {
        $messages = $this->relationLoaded('supportMessage')
            ? $this->supportMessage
            : $this->supportMessage()->orderBy('id')->get();
        $raw = (string) optional($messages->sortBy('id')->first())->message;

        $company = trim((string) $this->company_name);
        $phone   = trim((string) $this->phone);
        $capital = trim((string) $this->capital_range);
        $body    = [];

        foreach (preg_split("/\r\n|\n|\r/", $raw) ?: [] as $line) {
            if ($phone === '' && preg_match('/^Phone:\s*(.+)$/i', $line, $match)) {
                $phone = trim($match[1]);
                continue;
            }
            if ($company === '' && preg_match('/^Company:\s*(.+)$/i', $line, $match)) {
                $company = trim($match[1]);
                continue;
            }
            if ($capital === '' && preg_match('/^Capital range:\s*(.+)$/i', $line, $match)) {
                $capital = trim($match[1]);
                continue;
            }
            $body[] = $line;
        }

        return [
            'company' => $company !== '' ? $company : '—',
            'phone'   => $phone !== '' ? $phone : '—',
            'capital' => $capital !== '' ? $capital : '—',
            'message' => trim(implode("\n", $body)),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supportMessage()
    {
        return $this->hasMany(SupportMessage::class);
    }

}
