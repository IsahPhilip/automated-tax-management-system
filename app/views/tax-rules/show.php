<?php
$rule = $rule ?? [];
$bands = []; try { $bands = (new \App\Models\TaxBand())->forRule((int) ($rule['rule_id'] ?? 0)); } catch (\Throwable) {}
$money = static fn ($v) => $v === null || $v === '' ? '—' : '₦' . number_format((float) $v, 2);
?>
<h1><?= e($rule['rule_code'] ?? 'Rule') ?> — <?= e($rule['rule_name'] ?? '') ?></h1>
<p class="muted">
    Type: <?= e(str_replace('_', ' ', (string) ($rule['rule_type'] ?? '—'))) ?>
    <span class="badge <?= ($rule['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>" style="margin-left:8px"><?= e($rule['status'] ?? '') ?></span>
</p>
<dl class="kv">
    <dt>Effective period</dt><dd><?= e(substr((string) ($rule['effective_from'] ?? ''), 0, 10)) ?> â†’ <?= e($rule['effective_to'] ?: 'open') ?></dd>
    <dt>Base rate</dt><dd><?= $money($rule['base_rate'] ?? null) ?></dd>
    <dt>Fixed amount</dt><dd><?= $money($rule['base_amount'] ?? null) ?></dd>
    <dt>Minimum / maximum tax</dt><dd><?= $money($rule['minimum_tax'] ?? null) ?> / <?= $money($rule['maximum_tax'] ?? null) ?></dd>
</dl>

<h2>Bands &amp; components</h2>
<table>
    <thead><tr><th>#</th><th>Lower limit</th><th>Upper limit</th><th>Rate (%)</th><th>Fixed</th></tr></thead>
    <tbody>
    <?php if ($bands === []): ?><tr><td colspan="5">No band rows defined for this rule.</td></tr>
    <?php else: foreach ($bands as $b): ?>
        <tr><td><?= (int) ($b['band_order'] ?? 0) ?></td><td><?= $money($b['lower_limit']) ?></td><td><?= $b['upper_limit'] === null ? 'and above' : $money($b['upper_limit']) ?></td><td><?= number_format(((float) ($b['tax_rate'] ?? 0)) * 100, 2) ?>%</td><td><?= $money($b['fixed_amount']) ?></td></tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<div class="actions">
    <a class="button" href="<?= e(app_url('/tax-rules/' . ($rule['rule_id'] ?? 0) . '/edit')) ?>">Edit rule</a>
    <form method="post" action="<?= e(app_url('/tax-rules/' . ($rule['rule_id'] ?? 0) . '/deactivate')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button secondary" type="submit">Deactivate</button></form>
    <a class="button secondary" href="<?= e(app_url('/tax-rules')) ?>">All rules</a>
</div>
