<?php
declare(strict_types=1);
namespace App\Models;

class AuditLog extends BaseModel
{
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'log_id';

    public function record(?int $userId,string $action,string $module,?int $recordId=null,?string $description=null): int { return $this->create(['user_id'=>$userId,'action'=>$action,'module'=>$module,'record_id'=>$recordId,'description'=>$description,'ip_address'=>$_SERVER['REMOTE_ADDR']??null,'user_agent'=>$_SERVER['HTTP_USER_AGENT']??null]); }
    public function forRecord(string $module,int $recordId): array { return $this->where(['module'=>$module,'record_id'=>$recordId],'created_at'); }
    public function recent(int $limit=100): array { return array_slice($this->all('created_at','DESC'),0,max(1,min($limit,500))); }
}
