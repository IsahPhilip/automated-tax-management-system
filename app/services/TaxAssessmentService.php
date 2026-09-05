<?php
declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class TaxAssessmentService {
    public function assess(array $declaration): array {
        $type=strtoupper((string)($declaration['tax_type_code']??$declaration['tax_type']??''));
        return match($type){ 'PIT','PERSONAL_INCOME_TAX','PAYE'=>$this->pit($declaration), 'CIT','CORPORATE_INCOME_TAX'=>$this->cit($declaration), default=>throw new InvalidArgumentException('Unsupported tax type.') };
    }
    public function pit(array $data): array { require_once __DIR__.'/PersonalIncomeTaxService.php'; return (new PersonalIncomeTaxService())->calculate($data); }
    public function cit(array $data): array { require_once __DIR__.'/CorporateTaxService.php'; return (new CorporateTaxService())->calculate($data); }
    public function calculatePenalties(array $assessment,?string $dueDate=null,?string $asOfDate=null): array { require_once __DIR__.'/PenaltyService.php'; return (new PenaltyService())->calculate($assessment,$dueDate,$asOfDate); }
    public function calculateInterest(float $principal,?string $dueDate=null,?string $asOfDate=null): array { require_once __DIR__.'/InterestService.php'; return (new InterestService())->calculate($principal,$dueDate,$asOfDate); }

    /** README §9 engine: declaration → calculation → persisted DRAFT assessment (+items). */
    public function calculateFromDeclaration(int $declarationId, int $assessedBy): ?array
    {
        $declaration = (new \App\Models\TaxDeclaration())->details($declarationId);
        if (!$declaration) return null;
        if (strtoupper((string) ($declaration['tax_type_code'] ?? '')) === '') return null;

        $input = [
            'gross_income' => (float) ($declaration['gross_income'] ?? 0),
            'allowable_expenses' => (float) ($declaration['allowable_expenses'] ?? 0),
            'claimed_deductions' => (float) ($declaration['claimed_deductions'] ?? 0),
            'pension_contribution' => (float) ($declaration['pension_contribution'] ?? 0),
            'annual_rent_paid' => (float) ($declaration['annual_rent_paid'] ?? 0),
            'mortgage_interest' => (float) ($declaration['mortgage_interest'] ?? 0),
            'life_insurance_premium' => (float) ($declaration['life_insurance_premium'] ?? 0),
            'foreign_tax_paid' => (float) ($declaration['foreign_tax_paid'] ?? 0),
            'annual_turnover' => (float) ($declaration['annual_turnover'] ?? 0),
            'fixed_assets_value' => (float) ($declaration['fixed_assets_value'] ?? 0),
            'accounting_profit' => (float) ($declaration['accounting_profit'] ?? 0),
            'taxable_add_backs' => (float) ($declaration['taxable_add_backs'] ?? 0),
            'allowable_business_deductions' => (float) ($declaration['allowable_business_deductions'] ?? 0),
            'tax_loss_relief' => (float) ($declaration['tax_loss_relief'] ?? 0),
            'tax_period_id' => (int) ($declaration['period_id'] ?? 0),
        ];

        $calculated = $this->assess($input);
        if (!$calculated) return null;

        // README §5: add penalties/interest where applicable before final liability.
        $periodEnd = (string) ($declaration['period_end'] ?? date('Y-m-d'));
        $penalty = $this->calculatePenalties(['tax_payable' => $calculated['tax_payable']], $periodEnd, date('Y-m-d'));
        $interest = $this->calculateInterest(max(0.0, $calculated['tax_payable']), $periodEnd, date('Y-m-d'));
        $totalLiability = round((float) $calculated['tax_payable'] + (float) ($penalty['penalty'] ?? 0) + (float) ($interest['interest'] ?? 0), 2);

        $model = new \App\Models\TaxAssessment();
        $year = substr((string) ($declaration['period_start'] ?? date('Y')), 0, 4);
        $assessmentNumber = sprintf('ASS-%s-%06d', $year, $declarationId);
        if ($model->findByNumber($assessmentNumber) !== null) {
            $assessmentNumber .= '-' . strtoupper(bin2hex(random_bytes(2)));
        }

        $assessmentId = $model->create([
            'assessment_number' => $assessmentNumber,
            'taxpayer_id' => (int) $declaration['taxpayer_id'],
            'declaration_id' => $declarationId,
            'tax_type_id' => (int) $declaration['tax_type_id'],
            'period_id' => (int) $declaration['period_id'],
            'taxable_income' => (float) ($calculated['chargeable_income'] ?? $calculated['taxable_profit'] ?? 0),
            'assessable_profit' => (float) ($calculated['taxable_profit'] ?? $calculated['chargeable_income'] ?? 0),
            'gross_tax' => (float) ($calculated['tax_before_credits'] ?? 0),
            'total_deductions' => (float) (($calculated['deductions']['total'] ?? 0)),
            'tax_credits' => (float) ($calculated['credits'] ?? 0),
            'penalties' => (float) ($penalty['penalty'] ?? 0),
            'interest' => (float) ($interest['interest'] ?? 0),
            'development_levy' => 0.0,
            'total_liability' => $totalLiability,
            'amount_paid' => 0.0,
            'balance_due' => $totalLiability,
            'due_date' => date('Y-m-d', strtotime($periodEnd . ' +30 days')),
            'status' => 'DRAFT',
            'assessed_by' => $assessedBy,
            'assessed_at' => date('Y-m-d H:i:s'),
        ]);
        if (!$assessmentId) return null;
        $this->persistItems($assessmentId, $calculated);
        $row = $model->find($assessmentId);
        return $row ?: ['assessment_id' => $assessmentId, 'assessment_number' => $assessmentNumber];
    }

    /** Persist calculation breakdown rows so the taxpayer sees how tax was derived (README §10). */
    private function persistItems(int $assessmentId, array $calculated): void
    {
        try {
            $items = new \App\Models\AssessmentItem();

            foreach (($calculated['bands'] ?? []) as $band) {
                $income = (float) ($calculated['chargeable_income'] ?? 0);
                if ($income <= (float) $band['lower']) continue;
                $upper = $band['upper'] === null ? null : (float) $band['upper'];
                $slice = min($income, (float) ($upper ?? $income)) - (float) $band['lower'];
                if ($slice <= 0) continue;
                $items->create([
                    'assessment_id' => $assessmentId,
                    'band_id' => null,
                    'item_type' => 'PIT_BAND',
                    'description' => sprintf('Band %s - %s @ %.1f%%', number_format((float) $band['lower']), $upper === null ? 'and above' : number_format((float) $upper), ((float) $band['rate']) * 100),
                    'taxable_amount' => round($slice, 2),
                    'rate' => (float) $band['rate'],
                    'calculated_amount' => round($slice * (float) $band['rate'], 2),
                ]);
            }

            if (($calculated['tax_type'] ?? '') === 'CIT') {
                $items->create([
                    'assessment_id' => $assessmentId,
                    'band_id' => null,
                    'item_type' => 'CIT_RULE',
                    'description' => sprintf('Corporate rate %.0f%% on taxable profit', ((float) ($calculated['rate'] ?? 0)) * 100),
                    'taxable_amount' => (float) ($calculated['taxable_profit'] ?? 0),
                    'rate' => (float) ($calculated['rate'] ?? 0),
                    'calculated_amount' => (float) ($calculated['tax_before_credits'] ?? 0),
                ]);
            }
        } catch (\Throwable $e) {
            error_log('[ATMS] Assessment item persistence failed: ' . $e->getMessage());
        }
    }
}