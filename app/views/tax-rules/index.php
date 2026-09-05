<?php
$rules = $rules ?? [];
$title = 'Tax Rules & Compliance Controls';
?>
<p class="muted">Review the active tax configuration, penalty framework, and interest-rate settings for the current tax period.</p>

<div class="actions" style="margin-bottom:18px;">
    <a class="button" href="<?= e(app_url('/admin/tax-rules/create')) ?>">Create tax rule</a>
    <a class="button secondary" href="<?= e(app_url('/admin/interest-rates')) ?>">Interest rate settings</a>
</div>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Status</th>
            <th>Effective</th>
            <th>Rate</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($rules === []): ?>
            <tr><td colspan="5">No rules have been configured yet.</td></tr>
        <?php else: ?>
            <?php foreach ($rules as $rule): ?>
                <?php
                    $name = $rule['rule_name'] ?? $rule['penalty_name'] ?? $rule['rate_name'] ?? 'Configuration';
                    $type = $rule['calculation_method'] ?? $rule['taxpayer_category'] ?? $rule['status'] ?? 'CONFIG';
                    $status = $rule['status'] ?? 'ACTIVE';
                    $effective = $rule['effective_from'] ?? $rule['created_at'] ?? '';
                    $rate = $rule['annual_interest_rate'] ?? $rule['percentage_rate'] ?? $rule['base_rate'] ?? $rule['rate_name'] ?? '';
                    if (is_numeric($rate)) {
                        $rate = number_format((float)$rate, 2) . '%';
                    }
                ?>
                <tr>
                    <td><?= e($name) ?></td>
                    <td><?= e((string)$type) ?></td>
                    <td><?= e((string)$status) ?></td>
                    <td><?= e((string)$effective) ?></td>
                    <td><?= e((string)$rate) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
