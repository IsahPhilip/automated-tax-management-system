<?php $taxTypes = $taxTypes ?? []; ?>
<h1>Tax Types</h1>
<p class="muted">Tax categories and their calculation engines.</p>
<div class="actions" style="margin-bottom:14px"><a class="button" href="<?= e(app_url('/tax-types/create')) ?>">New tax type</a></div>

<table>
    <thead><tr><th>Code</th><th>Name</th><th>Calculation</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if ($taxTypes === []): ?><tr><td colspan="5">No tax types configured.</td></tr>
    <?php else: foreach ($taxTypes as $tt): ?>
        <tr>
            <td><strong><?= e($tt['code'] ?? '') ?></strong></td>
            <td><?= e($tt['name'] ?? '') ?></td>
            <td><?= e($tt['calculation_method'] ?? '') ?></td>
            <td><span class="badge <?= ($tt['status'] ?? '') === 'ACTIVE' ? 'b-ok' : 'b-bad' ?>"><?= e($tt['status'] ?? '') ?></span></td>
            <td style="display:flex;gap:8px">
                <a class="button secondary" href="<?= e(app_url('/tax-types/' . $tt['tax_type_id'])) ?>">View</a>
                <a class="button secondary" href="<?= e(app_url('/tax-types/' . $tt['tax_type_id'] . '/edit')) ?>">Edit</a>
                <?php if (($tt['status'] ?? '') === 'ACTIVE'): ?>
                <form method="post" action="<?= e(app_url('/tax-types/' . $tt['tax_type_id'] . '/deactivate')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="button secondary" type="submit">Deactivate</button></form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
