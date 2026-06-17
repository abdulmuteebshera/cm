<?php
$lines = file('C:/Users/hp/.cursor/projects/c-xampp-htdocs-portal-portal/agent-transcripts/4a62434d-6af5-49f3-bdff-7895ce39a677/4a62434d-6af5-49f3-bdff-7895ce39a677.jsonl');
foreach ($lines as $i => $line) {
    if (!str_contains($line, 'userPortfolioGrowthChart') && !str_contains($line, 'userStrategyChartsByPlan')) continue;
    echo "line ".($i+1).": found\n";
}
