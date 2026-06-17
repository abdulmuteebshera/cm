<?php
$lines = file('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
$cssPath = 'C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css';
$css = file_get_contents($cssPath);
$pos = strpos($css, '/* ── Quant fund dashboard');
if ($pos !== false) {
    $css = substr($css, 0, $pos);
}
$css = rtrim(str_replace("\r\n", "\n", $css)) . "\n";

$applied = 0;
$missed = 0;
foreach ($lines as $lineNum => $line) {
    if (!$line) continue;
    $obj = json_decode($line, true);
    if (!$obj) continue;
    foreach ($obj['message']['content'] ?? [] as $c) {
        if (($c['type'] ?? '') !== 'tool_use' || ($c['name'] ?? '') !== 'StrReplace') continue;
        $path = str_replace('\\', '/', $c['input']['path'] ?? '');
        if (!str_contains($path, 'custom.css')) continue;
        $old = str_replace("\r\n", "\n", $c['input']['old_string'] ?? '');
        $new = str_replace("\r\n", "\n", $c['input']['new_string'] ?? '');
        if ($old === '') continue;
        if (str_contains($css, $old)) {
            $css = str_replace($old, $new, $css);
            $applied++;
        } else {
            $missed++;
        }
    }
}
file_put_contents('C:/xampp/htdocs/portal/portal/assets/templates/invester/css/custom.css', $css);
echo "CSS replay: applied=$applied missed=$missed final_size=" . strlen($css) . "\n";
