<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\InterestRate;

class InterestRateController extends BaseController
{
    public function index(): void
    {
        $this->requireRole(1);
        $this->view('tax-rules/index', ['rules' => (new InterestRate())->all()]);
    }

    public function store(): void
    {
        $this->requireRole(1);

        $data = $_POST;
        $periodId = (int)($data['period_id'] ?? 1);
        $userId = auth_id() ?? 1;

        $record = [
            'tax_type_id' => isset($data['tax_type_id']) && $data['tax_type_id'] !== '' ? (int)$data['tax_type_id'] : null,
            'period_id' => $periodId,
            'rate_name' => trim((string)($data['rate_name'] ?? 'Late Tax Payment Interest')),
            'cbr_mpr_rate' => isset($data['cbr_mpr_rate']) && $data['cbr_mpr_rate'] !== '' ? (float)$data['cbr_mpr_rate'] : null,
            'statutory_spread_rate' => isset($data['statutory_spread_rate']) && $data['statutory_spread_rate'] !== '' ? (float)$data['statutory_spread_rate'] : null,
            'annual_interest_rate' => isset($data['annual_interest_rate']) && $data['annual_interest_rate'] !== '' ? (float)$data['annual_interest_rate'] : 0.0,
            'effective_from' => trim((string)($data['effective_from'] ?? date('Y-m-d'))),
            'effective_to' => isset($data['effective_to']) && $data['effective_to'] !== '' ? trim((string)$data['effective_to']) : null,
            'status' => strtoupper((string)($data['status'] ?? 'ACTIVE')),
            'created_by' => $userId,
        ];

        $model = new InterestRate();
        $id = $model->create($record);

        if (!$id) {
            $this->abort(500, 'Unable to create interest rate.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'INTEREST_RATE', $id, 'Interest rate created.');
        }

        $this->redirectTo('/admin/interest-rates');
    }
}
