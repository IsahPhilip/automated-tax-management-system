<?php
$assessment = $assessment ?? [];
$own = null;
try { $own = (new \App\Models\Taxpayer())->find((int) ($assessment['taxpayer_id'] ?? 0)); } catch (\Throwable $e) {}
$name = $own ? ((($own['business_name'] ?? '') ?: trim(($own['first_name'] ?? '') . ' ' . ($own['last_name'] ?? ''))) . ' (' . ($own['taxpayer_number'] ?? '') . ')') : ('#' . ($assessment['taxpayer_id'] ?? ''));
$methods = ['Cash', 'Bank Transfer', 'Card', 'Online'];
?>
<h1>Record payment</h1>
<p class="muted">For assessment <?= e($assessment['assessment_number'] ?? '') ?> · balance due
    <strong>₦<?= number_format((float) ($assessment['balance_due'] ?? 0), 2) ?></strong> · taxpayer <?= e($name) ?>
</p>

<form method="post" action="<?= e(app_url('/payments')) ?>" style="max-width:560px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="assessment_id" value="<?= (int) ($assessment['assessment_id'] ?? 0) ?>">

    <label for="amount">Amount (₦) *</label>
    <input id="amount" name="amount" type="number" min="0.01" max="<?= (float) ($assessment['balance_due'] ?? 0) ?>" step="0.01"
           value="<?= number_format((float) ($assessment['balance_due'] ?? 0), 2, '.', '') ?>" required>

    <label for="payment_method">Payment method *</label>
    <select id="payment_method" name="payment_method" required>
        <?php foreach ($methods as $m): ?><option value="<?= e($m) ?>"><?= e($m) ?></option><?php endforeach; ?>
    </select>

    <label for="transaction_reference">Transaction reference (optional)</label>
    <input id="transaction_reference" name="transaction_reference" maxlength="100" placeholder="Bank slip / gateway ref…">

    <label for="notes">Notes (optional)</label>
    <textarea id="notes" name="notes" rows="2"></textarea>

    <div class="actions">
        <button class="button" type="submit">Submit payment</button>
        <a class="button secondary" href="<?= e(app_url(function_exists('is_taxpayer') && is_taxpayer() ? '/taxpayer/assessments/' . ($assessment['assessment_id'] ?? 0) : '/assessments/' . ($assessment['assessment_id'] ?? 0))) ?>">Back</a>
    </div>
</form>
