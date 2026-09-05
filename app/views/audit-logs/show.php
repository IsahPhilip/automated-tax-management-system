<?php $log = $log ?? []; ?>
<h1>Audit entry #<?= (int) ($log['log_id'] ?? 0) ?></h1>
<p class="muted"><?= e($log['created_at'] ?? '') ?></p>
<dl class="kv">
    <dt>User ID</dt><dd><?= (int) ($log['user_id'] ?? 0) ?></dd>
    <dt>Action / module</dt><dd><?= e(($log['action'] ?? '—') . ' · ' . ($log['module'] ?? '—')) ?></dd>
    <dt>Record</dt><dd><?= e((string) ($log['record_id'] ?? '—')) ?></dd>
    <dt>IP address</dt><dd><?= e($log['ip_address'] ?? '—') ?></dd>
    <dt>User agent</dt><dd><?= e(mb_strimwidth((string) ($log['user_agent'] ?? '—'), 0, 120, '…')) ?></dd>
    <dt>Description</dt><dd><?= nl2br(e((string) ($log['description'] ?? ''))) ?></dd>
</dl>
<div class="actions"><a class="button secondary" href="<?= e(app_url('/admin/audit-logs')) ?>">Back to trail</a></div>
