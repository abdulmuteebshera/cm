<?php

namespace App\Models\EmailCampaign;

use Illuminate\Database\Eloquent\Model;

class EcMailSetting extends Model
{
    protected $table = 'ec_mail_settings';

    protected $fillable = [
        'from_email',
        'from_name',
        'reply_to',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
    ];

    public static function current(): self
    {
        return static::query()->firstOrFail();
    }
}
