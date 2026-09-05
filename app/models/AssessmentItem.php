<?php
declare(strict_types=1);
namespace App\Models;

class AssessmentItem extends BaseModel
{
    protected string $table = 'assessment_items';
    protected string $primaryKey = 'item_id';

    public function forAssessment(int $assessmentId): array { return $this->query("SELECT ai.*,tb.band_order,tb.lower_limit,tb.upper_limit FROM assessment_items ai LEFT JOIN tax_bands tb ON tb.band_id=ai.band_id WHERE ai.assessment_id=? ORDER BY ai.item_id",'i',[$assessmentId])->fetch_all(MYSQLI_ASSOC); }
    public function totalsByType(int $assessmentId): array { return $this->query("SELECT item_type,COALESCE(SUM(calculated_amount),0) total FROM assessment_items WHERE assessment_id=? GROUP BY item_type ORDER BY item_type",'i',[$assessmentId])->fetch_all(MYSQLI_ASSOC); }
}
