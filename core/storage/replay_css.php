<?php
$lines = file('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$css = file_get_contents('C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css');
$pos = strpos($css, '/* ── Quant fund dashboard');
if ($pos !== false) $css = substr($css, 0, $pos);

$applied = 0; $missed = 0; $firstMiss = null;
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
        if ($old === '') continue;
        if (str_contains($css, $old)) {
            $css = str_replace($old, $new, $css);
            $applied++;
        } else {
            $missed++;
            if (!$firstMiss) $firstMiss = ['line' => $lineNum+1, 'old' => substr($old,0,120)];
        }
    }
}
file_put_contents(__DIR__.'/recovered/custom_final.css', $css);
echo "applied=$applied missed=$missed size=".strlen($css)."\n";
if ($firstMiss) echo "first miss line {$firstMiss['line']}: {$firstMiss['old']}\n";
