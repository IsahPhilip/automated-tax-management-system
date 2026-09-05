<?php $n = $notification ?? []; ?>
<h1><?= e($n['title'] ?? 'Notification') ?></h1>
<p class="muted">
    <?= e($n['created_at'] ?? '') ?>
    <span class="badge b-info" style="margin-left:8px"><?= e($n['notification_type'] ?? 'INFO') ?></span>
</p>
<div class="card"><p style="margin:0"><?= nl2br(e((string) ($n['message'] ?? ''))) ?></p></div>
<div class="actions"><a class="button secondary" href="<?= e(app_url('/notifications')) ?>">Back to inbox</a></div>
