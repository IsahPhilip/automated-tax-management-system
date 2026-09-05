<?php
declare(strict_types=1);
namespace App\Models;

class Payment extends BaseModel
{
    protected string $table = 'payments';
    protected string $primaryKey = 'payment_id';

    public function findByReference(string $reference): ?array { return $this->firstWhere(['payment_reference'=>trim($reference)]); }
    public function forTaxpayer(int $taxpayerId): array { return $this->where(['taxpayer_id'=>$taxpayerId],'payment_date'); }
    public function forAssessment(int $assessmentId): array { return $this->where(['assessment_id'=>$assessmentId],'payment_date'); }
    public function pendingVerification(): array { return $this->where(['status'=>'PENDING'],'payment_date','ASC'); }
    public function verify(int $id,int $verifiedBy): bool { $p=$this->find($id); if(!$p||!in_array($p['status'],['PENDING','PROCESSING'],true))return false; $ok=$this->update($id,['status'=>'VERIFIED','verified_by'=>$verifiedBy,'verified_at'=>date('Y-m-d H:i:s')]); if(!$ok)return false; $a=new TaxAssessment(); return $a->recordPayment((int)$p['assessment_id'],(float)$p['amount']); }
    public function revenueSummary(?string $from = null, ?string $to = null): array
    {
        $sql = "SELECT COUNT(*) payment_count,
                       COALESCE(SUM(CASE WHEN status IN ('VERIFIED','SUCCESS') THEN amount ELSE 0 END),0) total_revenue,
                       COALESCE(SUM(CASE WHEN status='PENDING' THEN amount ELSE 0 END),0) pending_amount
                FROM payments WHERE 1=1";
        $types = '';
        $values = [];
        if ($from !== null && $from !== '') { $sql .= ' AND payment_date >= ?'; $types .= 's'; $values[] = $from . ' 00:00:00'; }
        if ($to   !== null && $to   !== '') { $sql .= ' AND payment_date <= ?'; $types .= 's'; $values[] = $to . ' 23:59:59'; }
        $row = $this->query($sql, $types, $values)->fetch_assoc() ?: [];
        return [
            'payment_count' => (int) ($row['payment_count'] ?? 0),
            'total_revenue' => (float) ($row['total_revenue'] ?? 0),
            'pending_amount' => (float) ($row['pending_amount'] ?? 0),
        ];
    }
    public function findByUser(int $userId): array { return $this->query("SELECT p.* FROM payments p INNER JOIN taxpayers t ON t.taxpayer_id=p.taxpayer_id WHERE t.user_id=? ORDER BY p.payment_date DESC",'i',[$userId])->fetch_all(MYSQLI_ASSOC); }
}
