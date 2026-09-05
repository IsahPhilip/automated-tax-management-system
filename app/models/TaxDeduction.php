<?php
declare(strict_types=1);
namespace App\Models;

class TaxDeduction extends BaseModel
{
    protected string $table = 'tax_deductions';
    protected string $primaryKey = 'deduction_id';

    public function forRule(int $ruleId): array { return $this->where(['rule_id'=>$ruleId,'status'=>'ACTIVE'],'name','ASC'); }
}
