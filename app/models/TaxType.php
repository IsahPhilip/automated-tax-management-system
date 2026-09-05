<?php
declare(strict_types=1);
namespace App\Models;

class TaxType extends BaseModel
{
    protected string $table = 'tax_types';
    protected string $primaryKey = 'tax_type_id';

    public function findByCode(string $code): ?array { return $this->firstWhere(['code'=>strtoupper(trim($code))]); }
    public function active(): array { return $this->where(['status'=>'ACTIVE'],'name','ASC'); }
    public function allActive(): array { return $this->active(); }
    public function deactivate(int $id): bool { return $this->update($id,['status'=>'INACTIVE']); }
}
