<?php
declare(strict_types=1);
namespace App\Models;

class TaxpayerDocument extends BaseModel
{
    protected string $table = 'taxpayer_documents';
    protected string $primaryKey = 'document_id';

    public function forTaxpayer(int $taxpayerId): array { return $this->where(['taxpayer_id'=>$taxpayerId],'created_at'); }
    public function verify(int $documentId,int $verifiedBy,string $status='VERIFIED'): bool { return $this->update($documentId,['verification_status'=>$status,'verified_by'=>$verifiedBy,'verified_at'=>date('Y-m-d H:i:s')]); }
}
