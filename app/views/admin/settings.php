<?php
$settings = $settings ?? [];
$title = 'System Settings';
?>
<p class="muted">These values drive the default business configuration for the application. Update only the fields that are meant to be changed in your deployment.</p>

<form method="post" action="<?= e(app_url('/admin/settings')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <table>
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($settings as $row): ?>
                <tr>
                    <td><strong><?= e($row['setting_key'] ?? '') ?></strong></td>
                    <td>
                        <input type="text" name="settings[<?= e($row['setting_key'] ?? '') ?>]" value="<?= e((string)($row['setting_value'] ?? '')) ?>" style="width:100%; min-width:220px;">
                    </td>
                    <td><?= e($row['description'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="actions" style="margin-top:16px;">
        <button type="submit" class="button">Save settings</button>
        <a class="button secondary" href="<?= e(app_url('/admin')) ?>">Back to admin</a>
    </div>
</form>
