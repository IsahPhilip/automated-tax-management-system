<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\AuditLog;

class AuditLogController extends BaseController
{
    private function adminOnly(): void { $this->requireRole(['SYSTEM_ADMINISTRATOR','ADMIN','SYSTEM ADMINISTRATOR']); }

    public function index(): void {
        $this->adminOnly();
        $this->view('audit-logs/index',['logs' => (new AuditLog())->all()]);
    }

    public function show(int $id): void {
        $this->adminOnly();
        $log = (new AuditLog())->find($id); if (!$log) $this->abort(404, 'Audit log entry not found.');
        $this->view('audit-logs/show',['log'=>$log]);
    }

    public function export(): never {
        $this->adminOnly();
        $logs = (new AuditLog())->all();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="atms-audit-log-'.date('Y-m-d').'.csv"');
        $out=fopen('php://output','wb');
        fputcsv($out,['ID','User ID','Action','Module','Record ID','Description','IP Address','Created At']);
        foreach($logs as $log) {
            fputcsv($out,[
                $log['audit_log_id']??'',$log['user_id']??'',$log['action']??'',
                $log['module']??'',$log['record_id']??'',$log['description']??'',
                $log['ip_address']??'',$log['created_at']??''
            ]);
        }
        fclose($out); exit;
    }
}
