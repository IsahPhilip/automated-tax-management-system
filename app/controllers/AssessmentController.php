<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\TaxAssessment;
use App\Models\Taxpayer;
use App\Services\TaxAssessmentService;

class AssessmentController extends BaseController
{
    private function staffOnly(): void
    {
        $this->requireRole([
            1,
            2,
            'SYSTEM_ADMINISTRATOR',
            'SYSTEM ADMINISTRATOR',
            'ADMIN',
            'REVENUE_OFFICER',
            'REVENUE OFFICER',
            'REVENUE/TAX OFFICER',
            'TAX_OFFICER',
            'TAX OFFICER',
        ]);
    }

    public function index(): void
    {
        $this->requireAuth();

        $model = new TaxAssessment();

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());
            $items = $taxpayer
                ? $model->findByTaxpayer((int) $taxpayer['taxpayer_id'])
                : [];
        } else {
            $items = $model->all();
        }

        $this->view('assessments/index', ['assessments' => $items]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $assessment = (new TaxAssessment())->find($id);

        if (!$assessment) {
            $this->abort(404, 'Assessment not found.');
        }

        $this->view('assessments/show', ['assessment' => $assessment]);
    }

    public function create(): void
    {
        $this->staffOnly();

        $this->view('assessments/create');
    }

    public function calculate(): void
    {
        $this->staffOnly();

        $declarationId = (int) $this->input('declaration_id', 0);

        if ($declarationId <= 0) {
            $this->abort(422, 'A valid declaration is required.');
        }

        $service = new TaxAssessmentService();
        $assessment = $service->calculateFromDeclaration($declarationId, (int) auth_id());

        if (!$assessment) {
            $this->abort(422, 'The assessment could not be calculated.');
        }

        if (function_exists('audit_log')) {
            audit_log('CALCULATE', 'TAX_ASSESSMENT', (int) ($assessment['assessment_id'] ?? 0), 'Tax assessment calculated.');
        }

        $this->json([
            'success' => true,
            'message' => 'Assessment calculated successfully.',
            'data' => $assessment,
        ], 201);
    }

    public function approve(int $id): void
    {
        $this->staffOnly();

        $model = new TaxAssessment();

        if (!$model->approve($id, (int) auth_id())) {
            $this->abort(422, 'Assessment could not be approved.');
        }

        if (function_exists('audit_log')) {
            audit_log('APPROVE', 'TAX_ASSESSMENT', $id, 'Tax assessment approved.');
        }

        if (function_exists('flash_success')) {
            flash_success('Assessment approved successfully.');
        }

        $this->redirectTo('/assessments/' . $id);
    }

    public function store(): void
    {
        $this->staffOnly();

        $declarationId = (int) $this->input('declaration_id', 0);
        if ($declarationId <= 0) {
            $this->abort(422, 'A declaration is required to generate an assessment.');
        }

        $declaration = (new TaxDeclaration())->details($declarationId);
        if (!$declaration) {
            $this->abort(404, 'Declaration not found.');
        }

        $service = new TaxAssessmentService();
        $assessment = $service->calculateFromDeclaration($declarationId, (int) auth_id());
        if (!$assessment || empty($assessment['assessment_id'])) {
            $this->abort(422, 'The assessment could not be calculated.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'TAX_ASSESSMENT', (int) $assessment['assessment_id'], 'Assessment created from declaration #' . $declarationId . '.');
        }

        $this->redirectTo('/assessments/' . (int) $assessment['assessment_id']);
    }

    public function issue(int $id): void
    {
        $this->staffOnly();

        $model = new TaxAssessment();
        $assessment = $model->find($id);
        if (!$assessment) {
            $this->abort(404, 'Assessment not found.');
        }
        if (!in_array(($assessment['status'] ?? ''), ['APPROVED'], true)) {
            if (function_exists('flash_error')) {
                flash_error('Only approved assessments can be issued.');
            }
            $this->redirectTo('/assessments/' . $id);
        }

        $dueDate = $assessment['due_date'] ?? null;
        $model->update((int) $id, array_filter([
            'status' => 'ISSUED',
            'due_date' => $dueDate ?: date('Y-m-d', strtotime('+30 days')),
        ], static fn ($v) => $v !== null));

        $taxpayerUser = (new Taxpayer())->find((int) $assessment['taxpayer_id'])['user_id'] ?? null;
        if ($taxpayerUser && class_exists(\App\Services\NotificationService::class)) {
            try {
                (new \App\Services\NotificationService())->taxAssessmentReady(
                    (int) $taxpayerUser,
                    (string) ($assessment['assessment_number'] ?? ''),
                    (float) ($assessment['total_liability'] ?? 0)
                );
            } catch (\Throwable $e) {
                error_log('[ATMS] Issue notification failed: ' . $e->getMessage());
            }
        }

        if (function_exists('audit_log')) {
            audit_log('ISSUE', 'TAX_ASSESSMENT', (int) $id, 'Assessment issued to taxpayer.');
        }

        if (function_exists('flash_success')) {
            flash_success('Assessment issued successfully.');
        }

        $this->redirectTo('/assessments/' . $id);
    }

    public function cancel(int $id): void
    {
        $this->staffOnly();

        $model = new TaxAssessment();
        if (!$model->find($id)) {
            $this->abort(404, 'Assessment not found.');
        }
        if (!$model->cancel((int) $id)) {
            $this->abort(500, 'Unable to cancel assessment.');
        }

        if (function_exists('audit_log')) {
            audit_log('CANCEL', 'TAX_ASSESSMENT', (int) $id, 'Assessment cancelled.');
        }

        if (function_exists('flash_warning')) {
            flash_warning('Assessment cancelled.');
        }

        $this->redirectTo('/assessments/' . $id);
    }

    public function taxpayerIndex(): void
    {
        $this->requireAuth();

        $items = [];
        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());
            $items = $taxpayer ? (new TaxAssessment())->findByTaxpayer((int) $taxpayer['taxpayer_id']) : [];
        }

        $this->view('assessments/index', ['assessments' => $items]);
    }

    public function taxpayerShow(int $id): void
    {
        $this->requireAuth();

        $assessment = (new TaxAssessment())->find($id);
        if (!$assessment) {
            $this->abort(404, 'Assessment not found.');
        }

        $taxpayer = function_exists('is_taxpayer') && is_taxpayer()
            ? (new Taxpayer())->findByUserId((int) auth_id())
            : null;

        if ($taxpayer && ((int) $assessment['taxpayer_id'] !== (int) $taxpayer['taxpayer_id'])) {
            $this->abort(403, 'You are not authorized to view this assessment.');
        }

        $this->view('assessments/show', ['assessment' => $assessment]);
    }
}
