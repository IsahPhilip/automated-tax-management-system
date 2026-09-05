<?php
declare(strict_types=1);
namespace App\Models;

class TaxBand extends BaseModel
{
    protected string $table = 'tax_bands';
    protected string $primaryKey = 'band_id';

    public function forRule(int $ruleId): array { return $this->where(['rule_id'=>$ruleId],'band_order','ASC'); }
}
