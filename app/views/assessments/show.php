<?php
$assessment = $assessment ?? [];
$isTaxpayerPortal = function_exists('is_taxpayer') && is_taxpayer();
$scripts = ['/assets/js/assessment.js'];
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$status = $assessment['status'] ?? '';
$items = [];
try { $items = (new \App\Models\AssessmentItem())->forAssessment((int) ($assessment['assessment_id'] ?? 0)); } catch (\Throwable $e) {}
?>
<h1><?= e($assessment['assessment_number'] ?? 'Assessment') ?></h1>
<p class="muted">
    Raised <?= e($assessment['assessed_at'] ?? '') ?>
    · Due <strong><?= e($assessment['due_date'] ?? '—') ?></strong>
    <span class="badge <?= match ($status) { 'PAID' => 'b-ok', 'ISSUED','APPROVED' => 'b-info', 'PARTIALLY_PAID' => 'b-warn', default => 'b-info' } ?>" style="margin-left:8px"><?= e($status ?: '—') ?></span>
</p>

<h2>How this assessment was calculated</h2>
<dl class="kv">
    <dt>Taxable income</dt><dd><?= $money($assessment['taxable_income']) ?></dd>
    <dt>Gross tax</dt><dd><?= $money($assessment['gross_tax']) ?></dd>
    <dt>Deductions applied</dt><dd>− <?= $money($assessment['total_deductions']) ?></dd>
    <dt>Credits applied</dt><dd>− <?= $money($assessment['tax_credits']) ?></dd>
    <dt>Penalties</dt><dd>+ <?= $money($assessment['penalties']) ?></dd>
    <dt>Interest</dt><dd>+ <?= $money($assessment['interest']) ?></dd>
    <dt>Development levy</dt><dd>+ <?= $money($assessment['development_levy']) ?></dd>
    <dt style="border-top:1px solid var(--line);padding-top:10px">Total liability</dt><dd style="border-top:1px solid var(--line);padding-top:10px"><strong style="font-size:20px;color:var(--brand-dark)"><?= $money($assessment['total_liability']) ?></strong></dd>
</dl>

<?php if ($items !== []): ?>
    <h2>Line-by-line breakdown</h2>
    <button type="button" class="button secondary js-toggle-breakdown">Hide breakdown</button>
    <table class="js-breakdown-table">
        <thead><tr><th>Description</th><th>Taxable amount</th><th>Rate</th><th>Tax</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['description'] ?? '') ?></td>
                <td><?= $money($item['taxable_amount']) ?></td>
                <td><?= number_format(((float) ($item['rate'] ?? 0)) * 100, 1) ?>%</td>
                <td><?= $money($item['calculated_amount']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2>Payment position</h2>
<dl class="kv">
    <dt>Amount paid</dt><dd><?= $money($assessment['amount_paid']) ?></dd>
    <dt>Balance due</dt><dd><strong><?= $money($assessment['balance_due']) ?></strong></dd>
</dl>

<div class="actions">
    <?php if (!$isTaxpayerPortal): ?>
        <?php if ($status === 'DRAFT'): ?>
            <form method="post" action="<?= e(app_url('/assessments/' . $assessment['assessment_id'] . '/approve')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button" type="submit">Approve</button></form>
        <?php elseif ($status === 'APPROVED'): ?>
            <form method="post" action="<?= e(app_url('/assessments/' . $assessment['assessment_id'] . '/issue')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button" type="submit">Issue to taxpayer</button></form>
        <?php endif; ?>
        <form method="post" action="<?= e(app_url('/assessments/' . $assessment['assessment_id'] . '/cancel')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button secondary" type="submit">Cancel</button></form>
    <?php endif; ?>
    <?php if (!$isTaxpayerPortal || in_array($status, ['ISSUED', 'PARTIALLY_PAID'], true)): ?>
        <a class="button" href="<?= e(app_url('/payments/create?assessment=' . $assessment['assessment_id'])) ?>">Record payment</a>
    <?php endif; ?>
</div>
