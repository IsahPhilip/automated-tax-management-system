<?php
$logs = $logs ?? [];
$title = 'Audit Logs';
?>
<p class="muted">Recent system events and administrator actions.</p>
<table>
    <thead>
        <tr>
            <th>Time</th>
            <th>Action</th>
            <th>Module</th>
            <th>Record</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($logs === []): ?>
            <tr><td colspan="4">No audit logs found.</td></tr>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= e($log['created_at'] ?? '') ?></td>
                    <td><?= e($log['action'] ?? '') ?></td>
                    <td><?= e($log['module'] ?? '') ?></td>
                    <td><?= e((string)($log['record_id'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
