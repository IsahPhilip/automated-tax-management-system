<?php
$report = $report ?? ['total_taxpayers' => 0, 'active_taxpayers' => 0, 'business_taxpayers' => 0];
$total = (int) ($report['total_taxpayers'] ?? 0);
$active = (int) ($report['active_taxpayers'] ?? 0);
$business = (int) ($report['business_taxpayers'] ?? 0);
$individuals = max(0, $total - $business);
$pct = static fn (int $n) => $total > 0 ? number_format($n * 100 / $total, 1) . '%' : '0%';
$title = 'Taxpayer Report';
?>
<h1>Taxpayer Report</h1>
<p class="muted">Registered taxpayer base as at <?= date('d M Y') ?>.</p>

<div class="grid">
    <div class="metric">Registered taxpayers<strong><?= $total ?></strong></div>
    <div class="metric">Active<strong><?= $active ?></strong></div>
    <div class="metric">Business entities<strong><?= $business ?></strong></div>
    <div class="metric">Individuals<strong><?= $individuals ?></strong></div>
</div>

<table style="max-width:720px;margin-top:10px">
    <thead><tr><th>Segment</th><th>Taxpayers</th><th>Share of register</th></tr></thead>
    <tbody>
        <tr><td>Active taxpayers</td><td><?= $active ?></td><td><?= $pct($active) ?></td></tr>
        <tr><td>Individual (INDIVIDUAL)</td><td><?= $individuals ?></td><td><?= $pct($individuals) ?></td></tr>
        <tr><td>Business / corporate (BUSINESS · CORPORATE)</td><td><?= $business ?></td><td><?= $pct($business) ?></td></tr>
        <tr><td><strong>Total registered</strong></td><td><strong><?= $total ?></strong></td><td><strong>100%</strong></td></tr>
    </tbody>
</table>

<div class="actions" style="margin-top:14px">
    <button type="button" class="button js-print">Print report</button>
    <a class="button secondary" href="<?= e(app_url('/taxpayers')) ?>">Browse taxpayer register</a>
    <a class="button secondary" href="<?= e(app_url('/reports')) ?>">Back to reports</a>
</div>
