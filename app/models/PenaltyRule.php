<?php
declare(strict_types=1);
namespace App\Models;

class PenaltyRule extends BaseModel
{
    protected string $table = 'penalty_rules';
    protected string $primaryKey = 'penalty_rule_id';

    public function activeFor(?int $taxTypeId,int $periodId,string $category='ALL'): array { return $this->query("SELECT * FROM penalty_rules WHERE period_id=? AND status='ACTIVE' AND (tax_type_id IS NULL OR tax_type_id=?) AND (taxpayer_category='ALL' OR taxpayer_category=?) ORDER BY penalty_rule_id",'iis',[$periodId,$taxTypeId,$category])->fetch_all(MYSQLI_ASSOC); }
}
