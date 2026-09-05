<?php
declare(strict_types=1);
namespace App\Models;

class TaxPeriod extends BaseModel
{
    protected string $table = 'tax_periods';
    protected string $primaryKey = 'period_id';

    public function open(): array { return $this->where(['status'=>'OPEN'],'start_date','DESC'); }
    public function allActive(): array { return $this->open(); }
    public function current(): ?array { $d=date('Y-m-d'); return ($this->query("SELECT * FROM tax_periods WHERE start_date<=? AND end_date>=? ORDER BY start_date DESC LIMIT 1",'ss',[$d,$d])->fetch_assoc() ?: null); }
}
