<?php

namespace App\Support\EmailCampaign;

class EcTemplateRenderer
{
    public static function render(string $content, array $vars): string
    {
        foreach ($vars as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                continue;
            }
            $replacement = self::sanitizeMergeValue((string) ($value ?? ''));
            $content = str_replace('{{' . $key . '}}', $replacement, $content);
            $content = str_replace('{{ ' . $key . ' }}', $replacement, $content);
        }

        return $content;
    }

    /** Merge fields in HTML templates (prevents broken MIME / invalid markup). */
    public static function renderHtml(string $content, array $vars): string
    {
        $safe = [];
        foreach ($vars as $key => $value) {
            if (!is_scalar($value) && $value !== null) {
                continue;
            }
            $safe[$key] = htmlspecialchars(
                self::sanitizeMergeValue((string) ($value ?? '')),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        }

        return self::render($content, $safe);
    }

    protected static function sanitizeMergeValue(string $value): string
    {
        $value = str_replace(["\r", "\n"], ' ', $value);

        return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value);
    }

    public static function varsForRecipient(?string $name, string $email, ?array $merge = null): array
    {
        $vars = [
            'name'  => $name ?: 'Valued Client',
            'email' => $email,
        ];

        if (is_array($merge)) {
            foreach ($merge as $k => $v) {
                if (is_scalar($v) || $v === null) {
                    $vars[(string) $k] = $v;
                }
            }
        }

        return $vars;
    }
}
