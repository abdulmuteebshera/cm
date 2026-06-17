<?php
// One-time helper: copy blade sources from compiled view ENDPATH if blade missing.
// Does NOT decompile — only useful when blade file exists. For recovery we copy from cache metadata.

$cacheDir = __DIR__ . '/storage/framework/views';
$targets = [];

foreach (glob($cacheDir . '/*.php') as $file) {
    $content = file_get_contents($file);
    if (!preg_match('/PATH (.+?) ENDPATH/s', $content, $m)) {
        continue;
    }
    $fullPath = $m[1];
    $fullPath = str_replace('\\', '/', $fullPath);
    if (!str_contains($fullPath, '/resources/views/')) {
        continue;
    }
    $rel = substr($fullPath, strpos($fullPath, '/resources/views/') + strlen('/resources/views/'));
    $want = [
        'admin/strategy/',
        'templates/invester/user/dashboard.blade.php',
        'templates/invester/partials/plan.blade.php',
        'templates/invester/partials/invest_history.blade.php',
        'templates/invester/plan.blade.php',
        'templates/invester/user/invest_statistics.blade.php',
        'templates/invester/user/invests.blade.php',
        'admin/plan/index.blade.php',
        'admin/partials/sidenav.blade.php',
        'components/confirmation-modal.blade.php',
        'templates/invester/layouts/app.blade.php',
    ];
    $ok = false;
    foreach ($want as $w) {
        if (str_contains($rel, $w)) {
            $ok = true;
            break;
        }
    }
    if (!$ok) {
        continue;
    }
    $targets[$rel] = $file;
}

foreach ($targets as $rel => $compiled) {
    echo "FOUND: $rel => $compiled\n";
}
