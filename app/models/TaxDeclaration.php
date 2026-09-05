<?php
declare(strict_types=1);
namespace App\Models;

class TaxDeclaration extends BaseModel
{
    protected string $table = 'tax_declarations';
    protected string $primaryKey = 'declaration_id';

    public function forTaxpayer(int $taxpayerId,?string $status=null): array { $c=['taxpayer_id'=>$taxpayerId]; if($status!==null)$c['status']=$status; return $this->where($c,'created_at'); }
    public function findByTaxpayer(int $taxpayerId,?string $status=null): array { return $this->forTaxpayer($taxpayerId,$status); }
    public function pendingReview(): array { return $this->where(['status'=>'SUBMITTED'],'submitted_at','ASC'); }
    public function submit(int $id): bool { return $this->update($id,['status'=>'SUBMITTED','submitted_at'=>date('Y-m-d H:i:s')]); }
    public function approve(int $id,int $reviewer): bool { return $this->update($id,['status'=>'APPROVED','reviewed_by'=>$reviewer,'reviewed_at'=>date('Y-m-d H:i:s')]); }
    public function reject(int $id,int $reviewer): bool { return $this->update($id,['status'=>'REJECTED','reviewed_by'=>$reviewer,'reviewed_at'=>date('Y-m-d H:i:s')]); }
    public function details(int $id): ?array
    {
        return $this->query(
            'SELECT d.*, t.taxpayer_number, t.taxpayer_type, t.first_name, t.last_name, t.business_name, t.email AS taxpayer_email,
                    ty.code AS tax_type_code, ty.name AS tax_type_name, p.period_name, p.start_date AS period_start, p.end_date AS period_end
             FROM tax_declarations d
             INNER JOIN taxpayers t   ON t.taxpayer_id = d.taxpayer_id
             LEFT  JOIN tax_types ty  ON ty.tax_type_id = d.tax_type_id
             LEFT  JOIN tax_periods p ON p.period_id = d.period_id
             WHERE d.declaration_id = ? LIMIT 1',
            'i',
            [$id]
        )->fetch_assoc() ?: null;
    }
    public function listDetailed(?int $taxpayerId = null, ?string $status = null, int $limit = 500): array
    {
        $sql = 'SELECT d.declaration_id, d.status, d.submitted_at, d.created_at, d.gross_income, d.annual_turnover,
                       t.taxpayer_number, t.first_name, t.last_name, t.business_name,
                       ty.code AS tax_type_code, p.period_name
                FROM tax_declarations d
                INNER JOIN taxpayers t   ON t.taxpayer_id = d.taxpayer_id
                LEFT  JOIN tax_types ty  ON ty.tax_type_id = d.tax_type_id
                LEFT  JOIN tax_periods p ON p.period_id = d.period_id';
        $types = '';
        $values = [];
        if ($taxpayerId !== null) { $sql .= ' WHERE d.taxpayer_id = ?'; $types .= 'i'; $values[] = $taxpayerId; }
        if ($status !== null)     { $sql .= ($values === [] ? ' WHERE' : ' AND') . ' d.status = ?'; $types .= 's'; $values[] = $status; }
        $sql .= ' ORDER BY d.created_at DESC LIMIT ' . max(1, $limit);
        return $this->query($sql, $types, $values)->fetch_all(MYSQLI_ASSOC);
    }
}
