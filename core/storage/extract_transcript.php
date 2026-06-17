<?php
$transcript = file_get_contents('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$lines = explode("\n", $transcript);
$targets = [
    'dashboard.blade.php' => null,
    'sidebar.blade.php' => null,
    'topbar.blade.php' => null,
    'custom.css' => null,
    'PrepareStrategyPayoutTest.php' => null,
    'ResetPortalForStrategyTesting.php' => null,
    'invests.blade.php' => null,
];

foreach ($lines as $lineNum => $line) {
    if (!$line) continue;
    $obj = json_decode($line, true);
    if (!$obj) continue;
    foreach ($obj['message']['content'] ?? [] as $c) {
        if (($c['type'] ?? '') !== 'tool_use') continue;
        $path = $c['input']['path'] ?? '';
        $contents = $c['input']['contents'] ?? $c['input']['new_string'] ?? null;
        if (!$path || !$contents) continue;
        foreach (array_keys($targets) as $key) {
            if (str_contains(str_replace('\\', '/', $path), $key)) {
                if (($c['name'] ?? '') === 'Write') {
                    $targets[$key] = ['line' => $lineNum + 1, 'path' => $path, 'contents' => $contents, 'op' => 'Write'];
                } elseif (($c['name'] ?? '') === 'StrReplace' && !str_contains($key, '.css')) {
                    // keep Write only for blade/php; css uses last StrReplace chain - skip for now
                }
            }
        }
    }
}

$outDir = __DIR__ . '/recovered';
if (!is_dir($outDir)) mkdir($outDir);

foreach ($targets as $key => $data) {
    if (!$data) {
        echo "MISSING: $key\n";
        continue;
    }
    $safe = str_replace(['/', '\\', ':'], '_', $key);
    file_put_contents("$outDir/$safe", $data['contents']);
    echo "OK line {$data['line']}: $key (" . strlen($data['contents']) . " bytes)\n";
}
