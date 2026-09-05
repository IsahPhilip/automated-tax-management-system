<?php
$declaration = $declaration ?? [];
$isStaffReview = $isStaffReview ?? false;
$existingAssessment = $existingAssessment ?? null;
$auditTrail = $auditTrail ?? [];
$isBusiness = ($declaration['tax_type_code'] ?? '') === 'CIT';
$money = static fn ($v) => '₦' . number_format((float) ($v ?? 0), 2);
$status = $declaration['status'] ?? 'DRAFT';
?>
<h1>Declaration #<?= (int) $declaration['declaration_id'] ?></h1>
<p class="muted">
    <?= e(trim(($declaration['business_name'] ?? '') !== '' ? $declaration['business_name'] : trim(($declaration['first_name'] ?? '') . ' ' . ($declaration['last_name'] ?? '')))) ?>
    · <?= e($declaration['tax_type_name'] ?? ($declaration['tax_type_code'] ?? '')) ?>
    · <?= e($declaration['period_name'] ?? '') ?>
    <span class="badge <?= match ($status) { 'APPROVED' => 'b-ok', 'REJECTED' => 'b-bad', 'SUBMITTED' => 'b-warn', default => 'b-info' } ?>" style="margin-left:8px"><?= e($status) ?></span>
</p>

<dl class="kv">
    <dt>Taxpayer number</dt><dd><?= e($declaration['taxpayer_number'] ?? '—') ?></dd>
    <dt>Tax type</dt><dd><?= e(($declaration['tax_type_code'] ?? '—') . ' — ' . ($declaration['tax_type_name'] ?? '')) ?></dd>
    <dt>Period</dt><dd><?= e($declaration['period_name'] ?? '—') ?> (<?= e($declaration['period_start'] ?? '') ?> â†’ <?= e($declaration['period_end'] ?? '') ?>)</dd>
</dl>

<h2><?= $isBusiness ? 'Business figures' : 'Income & deductions (PIT)' ?></h2>
<div class="cards">
    <?php if ($isBusiness): ?>
        <div class="card"><strong>Annual turnover</strong><div><?= $money($declaration['annual_turnover']) ?></div></div>
        <div class="card"><strong>Accounting profit</strong><div><?= $money($declaration['accounting_profit']) ?></div></div>
        <div class="card"><strong>Add-backs</strong><div><?= $money($declaration['taxable_add_backs']) ?></div></div>
        <div class="card"><strong>Allowable deductions</strong><div><?= $money($declaration['allowable_business_deductions']) ?></div></div>
        <div class="card"><strong>Tax loss relief</strong><div><?= $money($declaration['tax_loss_relief']) ?></div></div>
    <?php else: ?>
        <div class="card"><strong>Gross income</strong><div><?= $money($declaration['gross_income']) ?></div></div>
        <div class="card"><strong>Other income</strong><div><?= $money($declaration['other_income']) ?></div></div>
        <div class="card"><strong>Allowable expenses</strong><div><?= $money($declaration['allowable_expenses']) ?></div></div>
        <div class="card"><strong>Pension contribution</strong><div><?= $money($declaration['pension_contribution']) ?></div></div>
        <div class="card"><strong>Rent paid</strong><div><?= $money($declaration['annual_rent_paid']) ?></div></div>
        <div class="card"><strong>Mortgage interest</strong><div><?= $money($declaration['mortgage_interest']) ?></div></div>
        <div class="card"><strong>Life insurance premium</strong><div><?= $money($declaration['life_insurance_premium']) ?></div></div>
        <div class="card"><strong>Foreign tax paid</strong><div><?= $money($declaration['foreign_tax_paid']) ?></div></div>
    <?php endif; ?>
</div>

<?php if (!empty($declaration['other_information'])): ?>
    <h2>Additional information</h2>
    <p><?= nl2br(e((string) $declaration['other_information'])) ?></p>
<?php endif; ?>

<?php if ($existingAssessment): ?>
    <div class="flash flash-info">
        Assessment <strong><?= e($existingAssessment['assessment_number'] ?? '') ?></strong> exists for this declaration.
        <a class="button secondary" style="margin-left:10px" href="<?= e(app_url('/assessments/' . $existingAssessment['assessment_id'])) ?>">Open assessment</a>
    </div>
<?php endif; ?>

<?php if (!$isStaffReview && $status === 'DRAFT'): ?>
    <form method="post" action="<?= e(app_url('/taxpayer/declarations/' . $declaration['declaration_id'] . '/submit')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="actions"><button type="submit" class="button">Submit for review</button></div>
    </form>
<?php elseif ($isStaffReview && $status === 'SUBMITTED'): ?>
    <h2>Officer review</h2>
    <div class="actions" style="align-items:flex-start;gap:24px">
        <form method="post" action="<?= e(app_url('/declarations/' . $declaration['declaration_id'] . '/approve')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="button">Approve &amp; generate assessment</button>
        </form>
        <form method="post" action="<?= e(app_url('/declarations/' . $declaration['declaration_id'] . '/reject')) ?>" style="min-width:340px">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="rejection_reason">Rejection reason (required)</label>
            <textarea id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Explain what the taxpayer must correct…"></textarea>
            <div class="check-row"><button type="submit" class="button secondary">Reject declaration</button></div>
        </form>
    </div>
<?php endif; ?>

<?php if ($isStaffReview && $auditTrail !== []): ?>
    <h2>History</h2>
    <table>
        <thead><tr><th>When</th><th>Action</th><th>User ID</th><th>Description</th></tr></thead>
        <tbody>
        <?php foreach ($auditTrail as $log): ?>
            <tr><td><?= e($log['created_at'] ?? '') ?></td><td><?= e($log['action'] ?? '') ?></td><td><?= e((string) ($log['user_id'] ?? '')) ?></td><td><?= e($log['description'] ?? '') ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
