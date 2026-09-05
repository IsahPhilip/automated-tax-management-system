<?php
declare(strict_types=1);
namespace App\Models;

class TaxAssessment extends BaseModel
{
    protected string $table = 'tax_assessments';
    protected string $primaryKey = 'assessment_id';

    public function findByNumber(string $number): ?array { return $this->firstWhere(['assessment_number'=>trim($number)]); }
    public function forTaxpayer(int $taxpayerId,?string $status=null): array { $c=['taxpayer_id'=>$taxpayerId]; if($status!==null)$c['status']=$status; return $this->where($c,'assessed_at'); }
    public function pendingReview(): array { return $this->where(['status'=>'UNDER_REVIEW'],'assessed_at','ASC'); }
    public function approve(int $id,int $approvedBy): bool { return $this->update($id,['status'=>'APPROVED','approved_by'=>$approvedBy,'approved_at'=>date('Y-m-d H:i:s')]); }
    public function issue(int $id): bool { return $this->update($id,['status'=>'ISSUED']); }
    public function cancel(int $id): bool { return $this->update($id,['status'=>'CANCELLED']); }
    public function findByTaxpayer(int $taxpayerId,?string $status=null): array { return $this->forTaxpayer($taxpayerId,$status); }
    public function recordPayment(int $id,float $amount): bool { $a=$this->find($id); if(!$a)return false; $paid=(float)$a['amount_paid']+$amount; $balance=max(0,(float)$a['total_liability']-$paid); $status=$balance<=0?'PAID':($paid>0?'PARTIALLY_PAID':$a['status']); return $this->update($id,['amount_paid'=>number_format($paid,2,'.',''),'balance_due'=>number_format($balance,2,'.',''),'status'=>$status]); }
    public function overdue(): array { return $this->query("SELECT * FROM tax_assessments WHERE due_date<CURDATE() AND balance_due>0 AND status IN ('ISSUED','PARTIALLY_PAID','APPROVED') ORDER BY due_date")->fetch_all(MYSQLI_ASSOC); }
    public function summary(): array
    {
        $row = $this->query(
            'SELECT COUNT(*) total_assessments,
                    COALESCE(SUM(total_liability),0) assessed_amount,
                    COALESCE(SUM(amount_paid),0) collected_amount,
                    COALESCE(SUM(balance_due),0) outstanding_amount
             FROM tax_assessments WHERE status NOT IN (\'DRAFT\',\'CANCELLED\',\'REJECTED\')'
        )->fetch_assoc() ?: [];
        return array_map('round', array_map('floatval', $row));
    }

    /** Sum of balances still collectible for live statuses (officer dashboard). */
    public function outstandingBalance(): float
    {
        $row = $this->query(
            "SELECT COALESCE(SUM(balance_due),0) balance FROM tax_assessments
             WHERE status IN ('ISSUED','PARTIALLY_PAID','APPROVED')"
        )->fetch_assoc() ?: [];
        return (float) ($row['balance'] ?? 0);
    }
}
