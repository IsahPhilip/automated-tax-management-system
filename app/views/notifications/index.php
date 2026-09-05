<?php $notifications = $notifications ?? []; ?>
<h1>Notifications</h1>
<p class="muted"><?= (int) ($unreadCount ?? 0) ?> unread.</p>

<?php if (($unreadCount ?? 0) > 0): ?>
<form method="post" action="<?= e(app_url('/notifications/read-all')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="actions"><button class="button secondary" type="submit">Mark all as read</button></div>
</form>
<?php endif; ?>

<table>
    <thead><tr><th>When</th><th>Type</th><th>Title</th><th>Message</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if ($notifications === []): ?><tr><td colspan="6">No notifications yet.</td></tr>
    <?php else: foreach ($notifications as $n): ?>
        <tr>
            <td><?= e($n['created_at'] ?? '') ?></td>
            <td><span class="badge b-info"><?= e($n['notification_type'] ?? 'INFO') ?></span></td>
            <td><strong><?= e($n['title'] ?? '') ?></strong></td>
            <td style="max-width:420px"><?= e(mb_strimwidth((string) ($n['message'] ?? ''), 0, 110, '…')) ?></td>
            <td><span class="badge <?= ((int) ($n['is_read'] ?? 0)) === 1 ? 'b-ok' : 'b-warn' ?>"><?= ((int) ($n['is_read'] ?? 0)) === 1 ? 'Read' : 'Unread' ?></span></td>
            <td style="display:flex;gap:8px">
                <?php if (!empty($n['notification_id'])): ?><a class="button secondary" href="<?= e(app_url('/notifications/' . $n['notification_id'])) ?>">Open</a><?php endif; ?>
            </td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>

<?php if (function_exists('is_staff') && is_staff()): ?>
<div class="actions"><a class="button" href="<?= e(app_url('/notifications/send')) ?>">Send a notification</a></div>
<?php endif; ?>
