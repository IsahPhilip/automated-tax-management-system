<?php
$taxTypes = $taxTypes ?? [];
$taxPeriods = $taxPeriods ?? [];
$staffMode = function_exists('is_staff') && is_staff();
$taxpayerOptions = $staffMode ? (new \App\Models\Taxpayer())->all('taxpayer_id', 'ASC') : [];
?>
<h1>New tax declaration</h1>
<p class="muted">Provide income information for the selected tax type and period. Fields marked * are required.</p>

<form method="post" action="<?= e(app_url('/taxpayer/declarations')) ?>" style="max-width:760px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <?php if ($staffMode): ?>
        <label for="taxpayer_id">Taxpayer *</label>
        <select id="taxpayer_id" name="taxpayer_id" required>
            <option value="">Select taxpayer…</option>
            <?php foreach ($taxpayerOptions as $tp): ?>
                <option value="<?= (int) $tp['taxpayer_id'] ?>">
                    <?= e(($tp['taxpayer_number'] ?? '') . ' · ' . (($tp['business_name'] ?? '') ?: trim(($tp['first_name'] ?? '') . ' ' . ($tp['last_name'] ?? '')))) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <div class="cards" style="margin-top:16px">
        <div>
            <label for="tax_type_id">Tax type *</label>
            <select id="tax_type_id" name="tax_type_id" required>
                <option value="">Select…</option>
                <?php foreach ($taxTypes as $tt): ?>
                    <option value="<?= (int) $tt['tax_type_id'] ?>"><?= e($tt['code'] . ' — ' . $tt['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="tax_period_id">Tax period *</label>
            <select id="tax_period_id" name="tax_period_id" required>
                <option value="">Select…</option>
                <?php foreach ($taxPeriods as $tp2): ?>
                    <option value="<?= (int) $tp2['period_id'] ?>"><?= e($tp2['period_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <h2>Amounts (₦)</h2>
    <div class="grid">
        <div><label for="gross_income">Gross income</label><input id="gross_income" name="gross_income" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="other_income">Other income</label><input id="other_income" name="other_income" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="allowable_expenses">Allowable expenses</label><input id="allowable_expenses" name="allowable_expenses" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="claimed_deductions">Claimed deductions total</label><input id="claimed_deductions" name="claimed_deductions" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="pension_contribution">Pension contribution</label><input id="pension_contribution" name="pension_contribution" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="annual_rent_paid">Annual rent paid</label><input id="annual_rent_paid" name="annual_rent_paid" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="mortgage_interest">Mortgage interest</label><input id="mortgage_interest" name="mortgage_interest" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="life_insurance_premium">Life insurance premium</label><input id="life_insurance_premium" name="life_insurance_premium" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="annual_turnover">Business annual turnover</label><input id="annual_turnover" name="annual_turnover" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="accounting_profit">Accounting profit</label><input id="accounting_profit" name="accounting_profit" type="number" step="0.01" value="0"></div>
        <div><label for="taxable_add_backs">Taxable add-backs</label><input id="taxable_add_backs" name="taxable_add_backs" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="allowable_business_deductions">Allowable business deductions</label><input id="allowable_business_deductions" name="allowable_business_deductions" type="number" min="0" step="0.01" value="0"></div>
        <div><label for="tax_loss_relief">Tax loss relief</label><input id="tax_loss_relief" name="tax_loss_relief" type="number" min="0" step="0.01" value="0"></div>
    </div>

    <label for="other_information">Other information</label>
    <textarea id="other_information" name="other_information" rows="3"></textarea>

    <div class="actions">
        <button type="submit" class="button">Save declaration</button>
        <a class="button secondary" href="<?= e(app_url('/declarations')) ?>">Cancel</a>
    </div>
</form>
