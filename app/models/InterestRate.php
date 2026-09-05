<?php
declare(strict_types=1);
namespace App\Models;

class InterestRate extends BaseModel
{
    protected string $table = 'interest_rates';
    protected string $primaryKey = 'interest_rate_id';

    public function activeFor(?int $taxTypeId,int $periodId): ?array { return ($this->query("SELECT * FROM interest_rates WHERE period_id=? AND status='ACTIVE' AND (tax_type_id IS NULL OR tax_type_id=?) AND effective_from<=CURDATE() AND (effective_to IS NULL OR effective_to>=CURDATE()) ORDER BY CASE WHEN tax_type_id=? THEN 0 ELSE 1 END,effective_from DESC LIMIT 1",'iii',[$periodId,$taxTypeId,$taxTypeId])->fetch_assoc() ?: null); }
}
