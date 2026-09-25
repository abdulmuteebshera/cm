<?php

namespace App\Support\EmailCampaign;

use App\Models\EmailCampaign\EcMailSetting;
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

class EcMailSender
{
    /**
     * @throws MailException
     */
    public function send(string $toEmail, ?string $toName, string $subject, string $htmlBody, ?string $textBody = null): void
    {
        $settings = EcMailSetting::current();

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $settings->smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings->smtp_username;
        $mail->Password   = $settings->smtp_password;
        $mail->Port       = (int) $settings->smtp_port;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';

        if ($settings->smtp_encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($settings->smtp_encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAutoTLS = false;
            $mail->SMTPSecure  = false;
        }

        $fromEmail = $settings->from_email;
        $fromName  = $settings->from_name ?: 'Crownmaire Capital';
        $replyTo   = $settings->reply_to ?: $fromEmail;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($replyTo, $fromName);
        $mail->addAddress($toEmail, $toName ?: '');

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $htmlBody));

        $mail->addCustomHeader('X-Mailer', 'Crownmaire-EmailCampaign');
        $mail->addCustomHeader('Precedence', 'bulk');
        $mail->addCustomHeader('List-Unsubscribe', '<mailto:' . $replyTo . '?subject=unsubscribe>');

        $mail->send();
    }
}
