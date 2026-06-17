<?php
$lines = file('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$obj = json_decode($lines[17], true);
foreach ($obj['message']['content'] as $c) {
    if (($c['type'] ?? '') === 'tool_use' && str_contains($c['input']['path'] ?? '', 'custom.css')) {
        echo "OLD:\n" . ($c['input']['old_string'] ?? '') . "\n\nNEW (first 500):\n" . substr($c['input']['new_string'] ?? '', 0, 500) . "\n";
    }
}
