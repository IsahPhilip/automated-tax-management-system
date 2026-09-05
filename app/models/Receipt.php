<?php
declare(strict_types=1);
namespace App\Models;

class Receipt extends BaseModel
{
    protected string $table = 'receipts';
    protected string $primaryKey = 'receipt_id';

    public function findByNumber(string $number): ?array { return $this->firstWhere(['receipt_number'=>trim($number)]); }
    public function forTaxpayer(int $taxpayerId): array { return $this->where(['taxpayer_id'=>$taxpayerId],'issued_at'); }
    public function forPayment(int $paymentId): ?array { return $this->firstWhere(['payment_id'=>$paymentId]); }
    public function findByUser(int $userId): array { return $this->query("SELECT r.* FROM receipts r INNER JOIN taxpayers t ON t.taxpayer_id=r.taxpayer_id WHERE t.user_id=? ORDER BY r.issued_at DESC",'i',[$userId])->fetch_all(MYSQLI_ASSOC); }
}
