<?php declare(strict_types=1); $scripts = ['/assets/js/taxpayer.js']; ?>
<div class="page-head">
    <div>
        <h1>Register Taxpayer</h1>
        <p class="muted">Create a taxpayer profile for declarations, assessments, payments, and receipts.</p>
    </div>
    <a class="button secondary" href="<?= e(app_url('/taxpayers')) ?>">Back to taxpayers</a>
</div>

<form class="taxpayer-form" action="<?= e(app_url('/taxpayers')) ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <section class="form-section">
        <h2>Taxpayer Category</h2>
        <div class="field-grid three">
            <label>
                <span>Type</span>
                <select name="taxpayer_type" required>
                    <option value="INDIVIDUAL">Individual</option>
                    <option value="BUSINESS">Business</option>
                    <option value="CORPORATE">Corporate</option>
                </select>
            </label>
            <label>
                <span>Annual turnover</span>
                <input name="annual_turnover" type="number" min="0" step="0.01" value="0.00">
            </label>
            <label>
                <span>Fixed assets value</span>
                <input name="fixed_assets_value" type="number" min="0" step="0.01" value="0.00">
            </label>
        </div>
    </section>

    <section class="form-section">
        <h2>Identity</h2>
        <div class="field-grid">
            <label>
                <span>First name</span>
                <input name="first_name" type="text" maxlength="100">
            </label>
            <label>
                <span>Last name</span>
                <input name="last_name" type="text" maxlength="100">
            </label>
            <label class="wide">
                <span>Business / corporate name</span>
                <input name="business_name" type="text" maxlength="200">
            </label>
            <label>
                <span>Identification type</span>
                <input name="identification_type" type="text" maxlength="50" placeholder="NIN, BVN, CAC, Passport">
            </label>
            <label>
                <span>Identification number</span>
                <input name="identification_number" type="text" maxlength="100">
            </label>
            <label>
                <span>Date of birth</span>
                <input name="date_of_birth" type="date">
            </label>
        </div>
    </section>

    <section class="form-section">
        <h2>Contact</h2>
        <div class="field-grid">
            <label>
                <span>Email</span>
                <input name="email" type="email" maxlength="150" required>
            </label>
            <label>
                <span>Phone</span>
                <input name="phone" type="tel" maxlength="30" required>
            </label>
            <label>
                <span>City</span>
                <input name="city" type="text" maxlength="100">
            </label>
            <label>
                <span>State</span>
                <input name="state" type="text" maxlength="100">
            </label>
            <label>
                <span>Country</span>
                <input name="country" type="text" maxlength="100" value="Nigeria">
            </label>
            <label class="wide">
                <span>Address</span>
                <textarea name="address" rows="4"></textarea>
            </label>
        </div>
    </section>

    <div class="form-actions">
        <button class="button" type="submit">Create taxpayer</button>
        <a class="button secondary" href="<?= e(app_url('/taxpayers')) ?>">Cancel</a>
    </div>
</form>
