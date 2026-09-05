<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\Taxpayer;
use App\Models\TaxDeclaration;
use App\Models\TaxAssessment;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $this->redirectTo('/taxpayer/dashboard');
        }

        if (function_exists('is_admin') && is_admin()) {
            $this->redirectTo('/admin');
        }

        $this->redirectTo('/staff/dashboard');
    }

    public function taxpayer(): void
    {
        $this->requireRole('TAXPAYER');

        $userId = function_exists('auth_id') ? auth_id() : 0;
        $taxpayerModel = new Taxpayer();
        $declarationModel = new TaxDeclaration();
        $assessmentModel = new TaxAssessment();

        $taxpayer = $taxpayerModel->findByUserId($userId);
        $declarations = $taxpayer ? $declarationModel->findByTaxpayer((int) $taxpayer['taxpayer_id']) : [];
        $assessments = $taxpayer ? $assessmentModel->findByTaxpayer((int) $taxpayer['taxpayer_id']) : [];
        $payments = $taxpayer ? (new \App\Models\Payment())->forTaxpayer((int) $taxpayer['taxpayer_id']) : [];
        $receipts = $taxpayer ? (new \App\Models\Receipt())->forTaxpayer((int) $taxpayer['taxpayer_id']) : [];

        $outstanding = 0.0;
        foreach ($assessments as $row) {
            if (!in_array(($row['status'] ?? ''), ['CANCELLED', 'DRAFT'], true)) {
                $outstanding += (float) ($row['balance_due'] ?? 0);
            }
        }
        $currentPeriod = (new \App\Models\TaxPeriod())->current();

        $this->view('dashboard/taxpayer', [
            'taxpayer' => $taxpayer,
            'declarations' => array_slice($declarations, 0, 6),
            'assessments' => array_slice($assessments, 0, 6),
            'payments' => array_slice($payments, 0, 5),
            'receipts' => array_slice($receipts, 0, 5),
            'outstanding' => round($outstanding, 2),
            'currentPeriod' => $currentPeriod,
        ]);
    }

    public function staff(): void
    {
        $this->requireRole([
            1, 2,
            'SYSTEM_ADMINISTRATOR', 'SYSTEM ADMINISTRATOR', 'ADMIN',
            'REVENUE_OFFICER', 'REVENUE OFFICER', 'REVENUE/TAX OFFICER',
            'TAX_OFFICER', 'TAX OFFICER',
        ]);

        $declarations = new \App\Models\TaxDeclaration();
        $assessments = new \App\Models\TaxAssessment();

        $revenueRow = (new \App\Models\Payment())->revenueSummary(null, null);

        $this->view('dashboard/officer', [
            'user' => function_exists('auth_user') ? auth_user() : null,
            'stats' => [
                'taxpayers' => (new \App\Models\Taxpayer())->count(),
                'pending_declarations' => count($declarations->pendingReview()),
                'pending_assessments' => count($assessments->where(['status' => 'APPROVED'])),
                'outstanding' => $assessments->outstandingBalance(),
                'collected' => (float) ($revenueRow['total_revenue'] ?? 0),
                'payments_pending' => (float) ($revenueRow['pending_amount'] ?? 0),
            ],
            'recentDeclarations' => $declarations->listDetailed(null, null, 8),
        ]);
    }

    public function admin(): void
    {
        $this->requireRole([
            'SYSTEM_ADMINISTRATOR',
            'ADMIN',
            'SYSTEM ADMINISTRATOR',
        ]);

        $summary = (new \App\Models\TaxAssessment())->summary();

        $this->view('dashboard/admin', [
            'user' => function_exists('auth_user') ? auth_user() : null,
            'stats' => [
                'users' => (new \App\Models\User())->count(),
                'taxpayers' => (new \App\Models\Taxpayer())->count(),
                'declarations_pending' => (new \App\Models\TaxDeclaration())->count(['status' => 'SUBMITTED']),
                'assessed_amount' => (float) ($summary['assessed_amount'] ?? 0),
                'collected_amount' => (float) ($summary['collected_amount'] ?? 0),
                'outstanding_amount' => (float) ($summary['outstanding_amount'] ?? 0),
            ],
        ]);
    }
}
