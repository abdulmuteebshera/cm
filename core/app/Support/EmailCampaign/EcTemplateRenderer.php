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
            $replacement = (string) ($value ?? '');
            $content = str_replace('{{' . $key . '}}', $replacement, $content);
            $content = str_replace('{{ ' . $key . ' }}', $replacement, $content);
        }

        return $content;
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
