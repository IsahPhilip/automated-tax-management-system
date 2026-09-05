<?php $r = $rule ?? []; ?>
<h1>Edit rule <?= e($r['rule_code'] ?? '') ?></h1>
<form method="post" action="<?= e(app_url('/tax-rules/' . ($r['rule_id'] ?? 0) . '/update')) ?>" style="max-width:640px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="tax_type_id">Tax type ID *</label><input id="tax_type_id" name="tax_type_id" type="number" min="1" required value="<?= (int) ($r['tax_type_id'] ?? 1) ?>">
    <label for="tax_period_id">Period ID *</label><input id="tax_period_id" name="tax_period_id" type="number" min="1" required value="<?= (int) ($r['period_id'] ?? 1) ?>">
    <label for="rule_code">Rule code *</label><input id="rule_code" name="rule_code" maxlength="100" required value="<?= e($r['rule_code'] ?? '') ?>">
    <label for="rule_name">Rule name *</label><input id="rule_name" name="rule_name" maxlength="255" required value="<?= e($r['rule_name'] ?? '') ?>">
    <label for="rule_type">Rule type *</label>
    <select id="rule_type" name="rule_type" required>
        <?php foreach (['PROGRESSIVE_BANDS', 'FLAT_RATE', 'TURNOVER_BRACKETS', 'FIXED_AMOUNT'] as $rt): ?>
            <option value="<?= $rt ?>" <?= (($r['rule_type'] ?? '') === $rt ? 'selected' : '') ?>><?= str_replace('_', ' ', ucfirst(strtolower($rt))) ?></option>
        <?php endforeach; ?>
    </select>
    <label for="rate">Rate (%)</label><input id="rate" name="rate" type="number" min="0" step="0.0001" value="<?= e((string) ($r['rate'] ?? '')) ?>">
    <label for="fixed_amount">Fixed amount (₦)</label><input id="fixed_amount" name="fixed_amount" type="number" min="0" step="0.01" value="<?= e((string) ($r['fixed_amount'] ?? '')) ?>">
    <label for="effective_from">Effective from *</label><input id="effective_from" name="effective_from" type="date" required value="<?= e(substr((string) ($r['effective_from'] ?? date('Y-m-d')), 0, 10)) ?>">
    <label for="status">Status</label>
    <select id="status" name="status"><option value="ACTIVE" <?= (($r['status'] ?? '') === 'ACTIVE' ? 'selected' : '') ?>>Active</option><option value="INACTIVE" <?= (($r['status'] ?? '') !== 'ACTIVE' ? 'selected' : '') ?>>Inactive</option></select>
    <div class="actions"><button class="button" type="submit">Save changes</button>
        <a class="button secondary" href="<?= e(app_url('/tax-rules/' . ($r['rule_id'] ?? 0))) ?>">Cancel</a></div>
</form>
