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

        if (empty($settings->smtp_host) || str_contains(strtolower($settings->smtp_host), 'example.com')) {
            throw new MailException('SMTP is not configured. Go to Email Campaign Admin → SMTP / Sender and save real Gmail (or other) credentials.');
        }

        if (empty($settings->smtp_username) || ($settings->smtp_password === null || $settings->smtp_password === '')) {
            throw new MailException('SMTP username or password is missing. Re-save mail settings with your app password.');
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $settings->smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings->smtp_username;
        $mail->Password   = $settings->smtp_password;
        $mail->Port       = (int) $settings->smtp_port;
        $mail->CharSet    = 'UTF-8';
        $mail->Encoding   = 'base64';
        $mail->Timeout    = 30;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => true,
                'verify_peer_name'  => true,
                'allow_self_signed' => false,
            ],
        ];

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
