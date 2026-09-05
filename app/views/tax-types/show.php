<?php $t = $taxType ?? []; ?>
<h1><?= e($t['code'] ?? 'Tax type') ?> — <?= e($t['name'] ?? '') ?></h1>
<p class="muted">
    Engine: <?= e($t['calculation_method'] ?? '—') ?>
    <span class="badge <?= ($t['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>" style="margin-left:8px"><?= e($t['status'] ?? '') ?></span>
</p>
<dl class="kv"><dt>Description</dt><dd><?= nl2br(e((string) ($t['description'] ?? '—'))) ?></dd></dl>
<div class="actions">
    <a class="button secondary" href="<?= e(app_url('/tax-types/' . ($t['tax_type_id'] ?? 0) . '/edit')) ?>">Edit</a>
    <a class="button secondary" href="<?= e(app_url('/tax-rules?tax_type=' . ($t['tax_type_id'] ?? 0))) ?>">View related rules</a>
    <a class="button secondary" href="<?= e(app_url('/tax-types')) ?>">Back to list</a>
</div>
