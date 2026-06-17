<?php
$transcript = file_get_contents('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$lines = explode("\n", $transcript);
$outDir = __DIR__ . '/recovered';

function replayPatches(string $content, array $lines, string $pathFragment, int $afterLine = 0): array {
    $applied = 0;
    $missed = 0;
    foreach ($lines as $lineNum => $line) {
        if ($lineNum + 1 < $afterLine || !$line) continue;
        $obj = json_decode($line, true);
        if (!$obj) continue;
        foreach ($obj['message']['content'] ?? [] as $c) {
            if (($c['type'] ?? '') !== 'tool_use' || ($c['name'] ?? '') !== 'StrReplace') continue;
            $path = str_replace('\\', '/', $c['input']['path'] ?? '');
            if (!str_contains($path, $pathFragment)) continue;
            $old = $c['input']['old_string'] ?? '';
            $new = $c['input']['new_string'] ?? '';
            if ($old === '') continue;
            if (str_contains($content, $old)) {
                $content = str_replace($old, $new, $content);
                $applied++;
            } else {
                $missed++;
            }
        }
    }
    return [$content, $applied, $missed];
}

// Dashboard: start from line 67 Write, apply patches after line 67
$dashboard = file_get_contents("$outDir/dashboard_line_67.blade.php");
[$dashboard, $dApplied, $dMissed] = replayPatches($dashboard, $lines, 'user/dashboard.blade.php', 67);
file_put_contents("$outDir/dashboard_final.blade.php", $dashboard);
echo "Dashboard patches: applied=$dApplied missed=$dMissed size=" . strlen($dashboard) . "\n";

// CSS: start from original pre-quant (strip quant block from current if present)
$cssPath = 'C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css';
$css = file_get_contents($cssPath);
$pos = strpos($css, '/* ── Quant fund dashboard');
$css = $pos !== false ? substr($css, 0, $pos) : $css;
[$css, $cApplied, $cMissed] = replayPatches($css, $lines, 'custom.css', 18);
file_put_contents("$outDir/custom_final.css", $css);
echo "CSS patches: applied=$cApplied missed=$cMissed size=" . strlen($css) . "\n";

// Topbar: apply patch to current
$topbar = file_get_contents('C:/xampp/htdocs/portal/portal/core/resources/views/templates/invester/partials/topbar.blade.php');
[$topbar, $tApplied, $tMissed] = replayPatches($topbar, $lines, 'partials/topbar.blade.php', 69);
file_put_contents("$outDir/topbar_final.blade.php", $topbar);
echo "Topbar patches: applied=$tApplied missed=$tMissed\n";
