<?php $logs = array_slice($logs ?? [], 0, 300); ?>
<h1>Audit Trail</h1>
<p class="muted">Every important operation is recorded per README Â§18. Showing latest <?= count($logs) ?> entries.</p>

<div class="actions" style="margin-bottom:12px">
    <a class="button secondary" href="<?= e(app_url('/admin/audit-logs/export')) ?>">Export CSV</a>
</div>

<table>
    <thead><tr><th>ID</th><th>When</th><th>User</th><th>Action</th><th>Module</th><th>Record</th><th>Description</th><th>IP</th></tr></thead>
    <tbody>
    <?php if ($logs === []): ?><tr><td colspan="8">No entries recorded yet.</td></tr>
    <?php else: foreach ($logs as $l): ?>
        <tr>
            <td><?= (int) ($l['log_id'] ?? 0) ?></td>
            <td><?= e($l['created_at'] ?? '') ?></td>
            <td><?= (int) ($l['user_id'] ?? 0) ?></td>
            <td><strong><?= e($l['action'] ?? '') ?></strong></td>
            <td><?= e($l['module'] ?? '') ?></td>
            <td><?= e((string) ($l['record_id'] ?? '')) ?></td>
            <td style="max-width:340px"><?= e(mb_strimwidth((string) ($l['description'] ?? ''), 0, 90, '…')) ?></td>
            <td><?= e($l['ip_address'] ?? '') ?></td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
