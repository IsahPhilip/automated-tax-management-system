<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\AuditLog;

class AdminController extends BaseController
{
    public function index(): void
    {
        $this->dashboard();
    }

    private function adminOnly(): void
    {
        $this->requireRole([
            'SYSTEM_ADMINISTRATOR',
            'ADMIN',
            'SYSTEM ADMINISTRATOR',
        ]);
    }

    public function dashboard(): void
    {
        $this->adminOnly();

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

    public function users(): void
    {
        $this->adminOnly();

        $this->view('admin/users', [
            'users' => (new User())->all(),
        ]);
    }

    public function settings(): void
    {
        $this->adminOnly();

        $this->view('admin/settings', [
            'settings' => (new SystemSetting())->all(),
        ]);
    }

    public function auditLogs(): void
    {
        $this->adminOnly();

        $this->view('admin/audit-logs', [
            'logs' => (new AuditLog())->all(),
        ]);
    }
}
