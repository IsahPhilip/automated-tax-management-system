<?php $taxTypes = $taxTypes ?? []; $taxPeriods = $taxPeriods ?? []; ?>
<h1>Create tax rule</h1>
<p class="muted">Rules are read by the assessment engine from the database at calculation time (README Â§9).</p>
<form method="post" action="<?= e(app_url('/tax-rules')) ?>" style="max-width:640px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="tax_type_id">Tax type *</label>
    <select id="tax_type_id" name="tax_type_id" required>
        <option value="">Select…</option>
        <?php foreach ($taxTypes as $tt): ?><option value="<?= (int) $tt['tax_type_id'] ?>"><?= e($tt['code'] . ' — ' . $tt['name']) ?></option><?php endforeach; ?>
    </select>
    <label for="tax_period_id">Period *</label>
    <select id="tax_period_id" name="tax_period_id" required>
        <option value="">Select…</option>
        <?php foreach ($taxPeriods as $tp): ?><option value="<?= (int) $tp['period_id'] ?>"><?= e($tp['period_name']) ?></option><?php endforeach; ?>
    </select>
    <label for="rule_code">Rule code *</label><input id="rule_code" name="rule_code" maxlength="100" required placeholder="PIT_BANDS_2026">
    <label for="rule_name">Rule name *</label><input id="rule_name" name="rule_name" maxlength="255" required>
    <label for="rule_type">Rule type *</label>
    <select id="rule_type" name="rule_type" required>
        <option value="PROGRESSIVE_BANDS">Progressive bands (PIT)</option>
        <option value="FLAT_RATE">Flat rate</option>
        <option value="TURNOVER_BRACKETS">Turnover brackets (CIT)</option>
        <option value="FIXED_AMOUNT">Fixed amount</option>
    </select>
    <label for="rate">Rate (% , optional)</label><input id="rate" name="rate" type="number" min="0" step="0.0001">
    <label for="fixed_amount">Fixed amount (₦, optional)</label><input id="fixed_amount" name="fixed_amount" type="number" min="0" step="0.01">
    <label for="effective_from">Effective from *</label><input id="effective_from" name="effective_from" type="date" required value="<?= date('Y-m-d') ?>">
    <label for="status">Status</label>
    <select id="status" name="status"><option value="ACTIVE">Active</option><option value="INACTIVE">Inactive</option></select>
    <div class="actions"><button class="button" type="submit">Create rule</button>
        <a class="button secondary" href="<?= e(app_url('/tax-rules')) ?>">Cancel</a></div>
</form>
