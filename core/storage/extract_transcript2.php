<?php
$transcript = file_get_contents('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$lines = explode("\n", $transcript);
$outDir = __DIR__ . '/recovered';
if (!is_dir($outDir)) mkdir($outDir);

// Extract specific line Write for dashboard
foreach ([67, 247, 292] as $lineNo) {
    $obj = json_decode($lines[$lineNo - 1] ?? '', true);
    foreach ($obj['message']['content'] ?? [] as $c) {
        if (($c['type'] ?? '') === 'tool_use' && ($c['name'] ?? '') === 'Write') {
            $path = $c['input']['path'] ?? '';
            if (str_contains($path, 'dashboard.blade.php')) {
                file_put_contents("$outDir/dashboard_line_{$lineNo}.blade.php", $c['input']['contents']);
                echo "dashboard line $lineNo: " . strlen($c['input']['contents']) . " bytes\n";
            }
        }
    }
}

// Extract topbar StrReplace from line 69
$obj = json_decode($lines[68] ?? '', true);
foreach ($obj['message']['content'] ?? [] as $c) {
    if (($c['type'] ?? '') === 'tool_use' && str_contains($c['input']['path'] ?? '', 'topbar.blade.php')) {
        file_put_contents("$outDir/topbar_patch.txt", json_encode($c['input'], JSON_PRETTY_PRINT));
        echo "topbar patch found\n";
    }
}

// Replay all StrReplace on custom.css from transcript onto current custom.css
$cssPath = 'C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css';
$css = file_get_contents($cssPath);
// Strip our partial quant block and replay from original base (first 490 lines before quant was added)
$baseCss = file_get_contents('C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css');
// Find original end before quant block - use line 490 marker
$pos = strpos($baseCss, '/* ── Quant fund dashboard');
if ($pos !== false) {
    $css = substr($baseCss, 0, $pos);
} else {
    $css = $baseCss;
}

$replacements = 0;
foreach ($lines as $lineNum => $line) {
    if (!$line) continue;
    $obj = json_decode($line, true);
    if (!$obj) continue;
    foreach ($obj['message']['content'] ?? [] as $c) {
        if (($c['type'] ?? '') !== 'tool_use' || ($c['name'] ?? '') !== 'StrReplace') continue;
        $path = str_replace('\\', '/', $c['input']['path'] ?? '');
        if (!str_contains($path, 'custom.css')) continue;
        $old = $c['input']['old_string'] ?? '';
        $new = $c['input']['new_string'] ?? '';
        $all = !empty($c['input']['replace_all']);
        if ($old === '' || $new === '') continue;
        if ($all) {
            $css = str_replace($old, $new, $css);
        } elseif (str_contains($css, $old)) {
            $css = str_replace($old, $new, $css);
            $replacements++;
        }
    }
}

file_put_contents("$outDir/custom_replayed.css", $css);
echo "custom.css replay: $replacements replacements applied, " . strlen($css) . " bytes\n";
