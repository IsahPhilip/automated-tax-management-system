<?php
declare(strict_types=1);
namespace App\Models;

class TaxRule extends BaseModel
{
    protected string $table = 'tax_rules';
    protected string $primaryKey = 'rule_id';

    public function activeFor(int $taxTypeId,int $periodId): ?array { return ($this->query("SELECT * FROM tax_rules WHERE tax_type_id=? AND period_id=? AND status='ACTIVE' AND effective_from<=CURDATE() AND (effective_to IS NULL OR effective_to>=CURDATE()) ORDER BY effective_from DESC,rule_id DESC LIMIT 1",'ii',[$taxTypeId,$periodId])->fetch_assoc() ?: null); }
    public function forTaxTypeAndPeriod(int $taxTypeId,int $periodId): array { return $this->where(['tax_type_id'=>$taxTypeId,'period_id'=>$periodId],'effective_from'); }
    public function bands(int $ruleId): array { return (new TaxBand())->forRule($ruleId); }
    public function deactivate(int $id): bool { return $this->update($id,['status'=>'INACTIVE']); }
}
