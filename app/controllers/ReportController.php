<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\Payment;
use App\Models\Taxpayer;
use App\Models\TaxAssessment;

class ReportController extends BaseController
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
        $this->staffOnly();
        $this->view('reports/index', ['isReport' => true]);
    }

    public function revenue(): void
    {
        $this->staffOnly();

        $model = new Payment();
        // Accept date range from query string (filter form) or posted body.
        $from = trim((string) ($_GET['from'] ?? $this->input('from', '')));
        $to = trim((string) ($_GET['to'] ?? $this->input('to', '')));

        $this->view('reports/revenue', [
            'isReport' => true,
            'report' => $model->revenueSummary($from, $to),
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function payments(): void
    {
        $this->staffOnly();

        $from = trim((string) ($_GET['from'] ?? $this->input('from', '')));
        $to = trim((string) ($_GET['to'] ?? $this->input('to', '')));

        $filtered = array_values(array_filter(
            (new Payment())->all('payment_date', 'DESC'),
            static function (array $p) use ($from, $to): bool {
                $date = substr((string) ($p['payment_date'] ?? ''), 0, 10);
                return ($from === '' || $date >= $from) && ($to === '' || $date <= $to);
            }
        ));

        $total = 0.0;
        foreach ($filtered as $p) {
            if (($p['status'] ?? '') !== 'PENDING') {
                $total += (float) ($p['amount'] ?? 0);
            }
        }

        $this->view('reports/payments', [
            'isReport' => true,
            'report' => ['rows' => array_slice($filtered, 0, 300), 'total' => round($total, 2), 'count' => count($filtered)],
            'from' => $from,
            'to' => $to,
        ]);
    }

    /** CSV download of the payment register (README §17 "Export"). */
    public function exportPayments(): never
    {
        $this->staffOnly();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="atms-payments-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'wb');
        fputcsv($out, ['Payment ID', 'Reference', 'Assessment ID', 'Taxpayer ID', 'Amount', 'Method', 'Date', 'Status']);
        foreach ((new Payment())->all('payment_date', 'DESC') as $p) {
            fputcsv($out, [
                $p['payment_id'] ?? '',
                $p['payment_reference'] ?? '',
                $p['assessment_id'] ?? '',
                $p['taxpayer_id'] ?? '',
                $p['amount'] ?? '',
                $p['payment_method'] ?? '',
                $p['payment_date'] ?? '',
                $p['status'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }

    public function taxpayers(): void
    {
        $this->staffOnly();

        $this->view('reports/taxpayers', [
            'isReport' => true,
            'report' => (new Taxpayer())->summary(),
        ]);
    }

    public function assessments(): void
    {
        $this->staffOnly();

        $this->view('reports/assessments', [
            'isReport' => true,
            'report' => (new TaxAssessment())->summary(),
        ]);
    }
}
