<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\TaxDeclaration;
use App\Models\Taxpayer;
use App\Models\TaxType;
use App\Models\TaxPeriod;

class DeclarationController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $model = new TaxDeclaration();

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());
            $items = $taxpayer
                ? $model->findByTaxpayer((int) $taxpayer['taxpayer_id'])
                : [];
        } else {
            $items = $model->all();
        }

        $this->view('declarations/index', ['declarations' => $items]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->view('declarations/create', [
            'taxTypes' => (new TaxType())->allActive(),
            'taxPeriods' => (new TaxPeriod())->allActive(),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        require_once PROJECT_ROOT . '/app/helpers/validation.php';

        $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());

        if (!$taxpayer && function_exists('is_taxpayer') && is_taxpayer()) {
            $this->abort(422, 'Your taxpayer profile has not been created.');
        }

        $data = $this->input();
        $data['taxpayer_id'] = $data['taxpayer_id'] ?? ($taxpayer['taxpayer_id'] ?? null);
        $data['submitted_by'] = auth_id();

        validate_or_throw($data, [
            'taxpayer_id' => ['required', 'integer'],
            'tax_type_id' => ['required', 'integer'],
            'tax_period_id' => ['required', 'integer'],
        ]);

        if (function_exists('is_taxpayer') && is_taxpayer() &&
            (int) $data['taxpayer_id'] !== (int) $taxpayer['taxpayer_id']) {
            $this->abort(403, 'You cannot submit a declaration for another taxpayer.');
        }

        $id = (new TaxDeclaration())->createDraft($data);

        if (!$id) {
            $this->abort(500, 'Unable to create declaration.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'TAX_DECLARATION', (int) $id, 'Tax declaration created.');
        }

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $this->redirectTo('/taxpayer/declarations/' . $id);
        }

        $this->redirectTo('/declarations/' . $id);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $declaration = (new TaxDeclaration())->find($id);

        if (!$declaration) {
            $this->abort(404, 'Declaration not found.');
        }

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());

            if (!$taxpayer || (int) $declaration['taxpayer_id'] !== (int) $taxpayer['taxpayer_id']) {
                $this->abort(403, 'You are not authorized to view this declaration.');
            }
        }

        $this->view('declarations/show', ['declaration' => $declaration]);
    }

    public function submit(int $id): void
    {
        $this->requireAuth();

        $model = new TaxDeclaration();
        $declaration = $model->find($id);

        if (!$declaration) {
            $this->abort(404, 'Declaration not found.');
        }

        $model->submit($id, (int) auth_id());

        if (function_exists('audit_log')) {
            audit_log('SUBMIT', 'TAX_DECLARATION', $id, 'Tax declaration submitted.');
        }

        if (function_exists('flash_success')) {
            flash_success('Declaration submitted successfully.');
        }

        $this->redirectTo('/declarations/' . $id);
    }

    // -----------------------------------------------------------------
    // Officer workflows (README §8): review queue, detail review,
    // approval (generates assessment) and rejection (records reason).
    // -----------------------------------------------------------------

    public function staffIndex(): void
    {
        $this->staffOnly();

        $status = trim((string) ($_GET['status'] ?? ''));

        $this->view('declarations/index', [
            'declarations' => (new TaxDeclaration())->listDetailed(null, $status !== '' ? strtoupper($status) : null),
            'statusFilter' => $status !== '' ? strtoupper($status) : '',
            'pendingReviewCount' => count((new TaxDeclaration())->pendingReview()),
        ]);
    }

    public function staffShow(int $id): void
    {
        $this->staffOnly();

        $declaration = (new TaxDeclaration())->details($id);
        if (!$declaration) {
            $this->abort(404, 'Declaration not found.');
        }

        $existingAssessment = null;
        foreach ((new \App\Models\TaxAssessment())->where(['declaration_id' => $id]) as $row) {
            $existingAssessment = $row;
        }

        $this->view('declarations/show', [
            'declaration' => $declaration,
            'isStaffReview' => true,
            'existingAssessment' => $existingAssessment,
            'auditTrail' => function_exists('audit_log') ? (new \App\Models\AuditLog())->forRecord('TAX_DECLARATION', $id) : [],
        ]);
    }

    public function approve(int $id): void
    {
        $this->staffOnly();

        $declarations = new TaxDeclaration();
        $declaration = $declarations->details($id);
        if (!$declaration) {
            $this->abort(404, 'Declaration not found.');
        }
        if (!in_array(($declaration['status'] ?? ''), ['SUBMITTED'], true)) {
            if (function_exists('flash_error')) {
                flash_error('Only submitted declarations can be approved.');
            }
            $this->redirectTo('/declarations/' . $id);
        }

        $service = new \App\Services\TaxAssessmentService();
        $assessment = $service->calculateFromDeclaration($id, (int) auth_id());
        if (!$assessment || empty($assessment['assessment_id'])) {
            $this->abort(422, 'The assessment could not be generated from this declaration.');
        }

        $assessmentModel = new \App\Models\TaxAssessment();
        $assessmentModel->approve((int) $assessment['assessment_id'], (int) auth_id());
        $declarations->approve($id, (int) auth_id());

        if (function_exists('audit_log')) {
            audit_log('APPROVE', 'TAX_DECLARATION', $id, 'Declaration approved and assessment ' . ($assessment['assessment_number'] ?? '') . ' generated.');
        }

        $taxpayerUser = (new Taxpayer())->find((int) $declaration['taxpayer_id'])['user_id'] ?? null;
        if ($taxpayerUser && class_exists(\App\Services\NotificationService::class)) {
            try {
                (new \App\Services\NotificationService())->taxAssessmentReady(
                    (int) $taxpayerUser,
                    (string) ($assessment['assessment_number'] ?? ''),
                    (float) ($assessment['total_liability'] ?? 0)
                );
            } catch (\Throwable $e) {
                error_log('[ATMS] Assessment notification failed: ' . $e->getMessage());
            }
        }

        if (function_exists('flash_success')) {
            flash_success('Declaration approved. Assessment ' . ($assessment['assessment_number'] ?? '') . ' is ready to issue.');
        }

        $this->redirectTo('/assessments/' . (int) $assessment['assessment_id']);
    }

    public function reject(int $id): void
    {
        $this->staffOnly();

        $reason = trim((string) $this->input('rejection_reason', ''));
        if ($reason === '') {
            if (function_exists('flash_error')) {
                flash_error('A rejection reason is required.');
            }
            $this->redirectTo('/declarations/' . $id);
        }

        (new TaxDeclaration())->reject($id, (int) auth_id());

        if (function_exists('audit_log')) {
            audit_log('REJECT', 'TAX_DECLARATION', $id, 'Rejection reason: ' . $reason);
        }

        if (function_exists('flash_warning')) {
            flash_warning('Declaration rejected. Reason recorded.');
        }

        $this->redirectTo('/declarations/' . $id);
    }

    private function staffOnly(): void
    {
        $this->requireRole([
            1, 2,
            'SYSTEM_ADMINISTRATOR', 'SYSTEM ADMINISTRATOR', 'ADMIN',
            'REVENUE_OFFICER', 'REVENUE OFFICER', 'REVENUE/TAX OFFICER',
            'TAX_OFFICER', 'TAX OFFICER',
        ]);
    }
}
