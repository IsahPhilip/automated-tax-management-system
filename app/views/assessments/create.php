<h1>Generate assessment</h1>
<p class="muted">Choose an approved or submitted declaration; the engine computes PIT/CIT using configured rules.</p>

<form method="post" action="<?= e(app_url('/assessments/store')) ?>" style="max-width:520px">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="declaration_id">Declaration ID *</label>
    <input id="declaration_id" name="declaration_id" type="number" min="1" required placeholder="e.g. 4">
    <div class="muted" style="margin-top:8px">
        Tip: open a declaration from the review queue and use its “Approveâ€ button — that generates the assessment automatically.
    </div>
    <div class="actions"><button type="submit" class="button">Calculate &amp; save draft</button></div>
</form>
