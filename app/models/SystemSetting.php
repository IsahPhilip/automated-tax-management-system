<?php
declare(strict_types=1);
namespace App\Models;

class SystemSetting extends BaseModel
{
    protected string $table = 'system_settings';
    protected string $primaryKey = 'setting_id';

    public function get(string $key,mixed $default=null): mixed { $r=$this->firstWhere(['setting_key'=>$key]); return $r===null?$default:$r['setting_value']; }
    public function set(string $key,mixed $value,?string $description=null,?int $updatedBy=null): bool { $r=$this->firstWhere(['setting_key'=>$key]); if($r)return $this->update((int)$r['setting_id'],['setting_value'=>(string)$value,'description'=>$description,'updated_by'=>$updatedBy]); $this->create(['setting_key'=>$key,'setting_value'=>(string)$value,'description'=>$description,'updated_by'=>$updatedBy]); return true; }
    public function allAsArray(): array { $out=[]; foreach($this->all('setting_key','ASC') as $r)$out[$r['setting_key']]=$r['setting_value']; return $out; }
}
