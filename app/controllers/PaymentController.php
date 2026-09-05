<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Payment;
use App\Models\TaxAssessment;

class PaymentController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $model = new Payment();

        $payments = function_exists('is_taxpayer') && is_taxpayer()
            ? $model->findByUser((int) auth_id())
            : $model->all();

        $this->view('payments/index', ['payments' => $payments]);
    }

    public function create(?int $assessmentId = null): void
    {
        $this->requireAuth();

        $assessmentId = $assessmentId ?? (int) ($_GET['assessment'] ?? 0);

        $assessment = $assessmentId > 0 ? (new TaxAssessment())->find($assessmentId) : null;

        if (!$assessment) {
            $this->abort(404, 'Assessment not found.');
        }

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $own = (new \App\Models\Taxpayer())->findByUserId((int) auth_id());
            if (!$own || (int) $own['taxpayer_id'] !== (int) $assessment['taxpayer_id']) {
                $this->abort(403, 'You are not authorized to pay this assessment.');
            }
        }

        $this->view('payments/create', ['assessment' => $assessment]);
    }

    public function store(): void
    {
        $this->requireAuth();

        require_once PROJECT_ROOT . '/app/helpers/validation.php';

        $data = $this->input();
        $data['recorded_by'] = auth_id();

        validate_or_throw($data, [
            'assessment_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required'],
        ]);

        $assessment = (new TaxAssessment())->find((int) $data['assessment_id']);

        if (!$assessment) {
            $this->abort(404, 'Assessment not found.');
        }

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $own = (new \App\Models\Taxpayer())->findByUserId((int) auth_id());
            if (!$own || (int) $own['taxpayer_id'] !== (int) $assessment['taxpayer_id']) {
                $this->abort(403, 'You cannot record payments against another taxpayer.');
            }
        }

        $data['payment_reference'] = trim((string) ($data['payment_reference'] ?? ''))
            ?: ('PAY-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(5))));
        $data['taxpayer_id'] = (int) $assessment['taxpayer_id'];
        $data['amount'] = number_format(min((float) $data['amount'], max(0.01, (float) ($assessment['balance_due'] ?? 0))), 2, '.', '');
        $data['status'] = 'PENDING';
        $data['payment_date'] = date('Y-m-d H:i:s');

        $id = (new Payment())->create($data);

        if (!$id) {
            $this->abort(500, 'Payment could not be recorded.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'PAYMENT', (int) $id, 'Payment recorded.');
        }

        if (function_exists('flash_success')) {
            flash_success('Payment recorded successfully.');
        }

        $this->redirectTo('/payments/' . $id);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $payment = (new Payment())->find($id);

        if (!$payment) {
            $this->abort(404, 'Payment not found.');
        }

        $this->view('payments/show', ['payment' => $payment]);
    }

    public function verify(int $id): void
    {
        $this->staffOnly();

        $payments = new Payment();
        $payment = $payments->find($id);
        if (!$payment) {
            $this->abort(404, 'Payment not found.');
        }

        if (!$payments->verify((int) $id, (int) auth_id())) {
            if (function_exists('flash_error')) {
                flash_error('This payment has already been verified or cannot be verified.');
            }
            $this->redirectTo('/payments/' . $id);
        }

        $receiptId = null;
        try {
            $receipts = new \App\Models\Receipt();
            $existing = $receipts->forPayment((int) $id);
            if (!$existing) {
                do {
                    $number = 'RCT-' . date('Y') . '-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT)
                        . ($receipts->findByNumber($number) !== null ? strtoupper(substr(bin2hex(random_bytes(2)), 0, 3)) : '');
                } while ($receipts->findByNumber($number) !== null);

                $receiptId = $receipts->create([
                    'receipt_number' => $number,
                    'payment_id' => (int) $id,
                    'taxpayer_id' => (int) $payment['taxpayer_id'],
                    'amount' => (float) $payment['amount'],
                    'issued_at' => date('Y-m-d H:i:s'),
                    'generated_by' => (int) auth_id(),
                ]);
            } else {
                $receiptId = (int) $existing['receipt_id'];
            }
        } catch (\Throwable $e) {
            error_log('[ATMS] Receipt generation failed for payment #' . $id . ': ' . $e->getMessage());
        }

        if (function_exists('audit_log')) {
            audit_log('VERIFY', 'PAYMENT', (int) $id, 'Payment verified and receipt generated.');
        }

        if (function_exists('flash_success')) {
            flash_success('Payment verified. Balance updated and receipt issued.');
        }

        $this->redirectTo($receiptId ? '/receipts/' . $receiptId : '/payments/' . $id);
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
