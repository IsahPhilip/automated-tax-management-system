<?php
declare(strict_types=1);
namespace App\Models;

class TaxCredit extends BaseModel
{
    protected string $table = 'tax_credits';
    protected string $primaryKey = 'credit_id';

    public function forRule(int $ruleId): array { return $this->where(['rule_id'=>$ruleId,'status'=>'ACTIVE'],'name','ASC'); }
    public function findByCode(string $code): ?array { return $this->firstWhere(['credit_code'=>strtoupper(trim($code))]); }
}
