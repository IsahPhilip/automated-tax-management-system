<?php
$users = $users ?? [];
$title = 'System Users';
?>
<p class="muted">Manage staff and administrative accounts for the tax system.</p>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users === []): ?>
            <tr><td colspan="4">No users found.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></td>
                    <td><?= e($user['email'] ?? '') ?></td>
                    <td><?= e($user['role_name'] ?? $user['role_id'] ?? '') ?></td>
                    <td><?= e($user['status'] ?? 'ACTIVE') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
