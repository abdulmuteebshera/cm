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

        $toEmail = strtolower(trim($toEmail));
        if (!PHPMailer::validateAddress($toEmail)) {
            throw new MailException('Invalid recipient address: ' . $toEmail);
        }

        $authEmail = strtolower(trim($settings->smtp_username));
        if (!PHPMailer::validateAddress($authEmail)) {
            throw new MailException('SMTP username must be a valid email address.');
        }

        $fromName = $this->sanitizeHeaderText($settings->from_name ?: 'Crownmaire Capital');

        $configuredFrom = strtolower(trim($settings->from_email));
        if (!PHPMailer::validateAddress($configuredFrom)) {
            $configuredFrom = $authEmail;
        }

        $replyTo = strtolower(trim($settings->reply_to ?: ''));
        if (!PHPMailer::validateAddress($replyTo)) {
            $replyTo = $configuredFrom !== $authEmail ? $configuredFrom : $authEmail;
        }

        // SMTP MAIL FROM / From header must match the authenticated mailbox on Gmail, cPanel, and most webmail hosts.
        $fromEmail = $authEmail;

        $subject   = $this->sanitizeHeaderText($subject);
        $htmlBody  = $this->normalizeUtf8($htmlBody);
        $textBody  = $textBody !== null && $textBody !== ''
            ? $this->normalizeUtf8($textBody)
            : $this->plainTextFromHtml($htmlBody);

        if ($subject === '') {
            throw new MailException('Email subject is empty.');
        }

        if ($htmlBody === '' && $textBody === '') {
            throw new MailException('Email body is empty.');
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $settings->smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $settings->smtp_username;
        $mail->Password   = $settings->smtp_password;
        $mail->Port       = (int) $settings->smtp_port;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 45;
        $mail->SMTPKeepAlive = false;

        if ($settings->smtp_encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($settings->smtp_encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAutoTLS = false;
            $mail->SMTPSecure  = false;
        }

        // Envelope sender (MAIL FROM) must match auth for Gmail and many webmail relays.
        $mail->Sender = $authEmail;
        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($replyTo, $fromName);

        $displayName = $toName !== null && trim($toName) !== ''
            ? $this->sanitizeHeaderText($toName)
            : '';
        $mail->addAddress($toEmail, $displayName);

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody;

        try {
            $mail->send();
        } catch (MailException $e) {
            throw new MailException($this->formatSendError($e, $mail));
        }
    }

    protected function sanitizeHeaderText(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', $value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;

        return trim($value);
    }

    protected function normalizeUtf8(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;
    }

    protected function plainTextFromHtml(string $html): string
    {
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = strip_tags($text ?? $html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    protected function formatSendError(MailException $e, PHPMailer $mail): string
    {
        $message = trim($e->getMessage());
        $detail  = trim($mail->ErrorInfo ?? '');

        if ($detail !== '' && !str_contains($message, $detail)) {
            $message .= ' ' . $detail;
        }

        if (str_contains(strtolower($message), 'data not accepted')) {
            $message .= ' Check that From email matches your SMTP login (Gmail: use the same address or a verified Send As alias).';
        }

        return $message;
    }
}
