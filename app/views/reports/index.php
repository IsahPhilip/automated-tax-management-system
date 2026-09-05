<?php
use App\Models\TaxAssessment;

$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$summary = [];
try { $summary = (new TaxAssessment())->summary(); } catch (\Throwable) {}
?>
<div class="dashboard-shell">
    <h1>Reports</h1>
    <p class="muted">Select a report to generate (README §17). Every report supports viewing and printing.</p>

    <div class="dashboard-metrics">
        <div class="dashboard-metric"><span class="label">Total assessed</span><strong><?= $money($summary['assessed_amount'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Revenue collected</span><strong><?= $money($summary['collected_amount'] ?? 0) ?></strong></div>
        <div class="dashboard-metric"><span class="label">Outstanding</span><strong><?= $money($summary['outstanding_amount'] ?? 0) ?></strong></div>
    </div>

    <div class="dashboard-section">
        <h2>Available reports</h2>
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Revenue Report</h3>
                <p class="muted">Payments received within a selected period, including pending verification amounts.</p>
                <a class="button" href="<?= e(app_url('/reports/revenue')) ?>">Open revenue report</a>
            </div>
            <div class="dashboard-card">
                <h3>Taxpayer Report</h3>
                <p class="muted">Registered taxpayer base with active and business-entity breakdown.</p>
                <a class="button" href="<?= e(app_url('/reports/taxpayers')) ?>">Open taxpayer report</a>
            </div>
            <div class="dashboard-card">
                <h3>Assessment Report</h3>
                <p class="muted">Assessment totals — assessed amounts, collections and outstanding balances.</p>
                <a class="button" href="<?= e(app_url('/reports/assessments')) ?>">Open assessment report</a>
            </div>
            <div class="dashboard-card">
                <h3>Payment Report</h3>
                <p class="muted">Full payment register with date filters and CSV export.</p>
                <a class="button" href="<?= e(app_url('/reports/payments')) ?>">Open payment report</a>
            </div>
        </div>
    </div>
</div>
