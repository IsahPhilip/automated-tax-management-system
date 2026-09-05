<?php
declare(strict_types=1);
namespace App\Models;

class Role extends BaseModel
{
    protected string $table = 'roles';
    protected string $primaryKey = 'role_id';

    public function findByName(string $name): ?array { return $this->firstWhere(['role_name'=>$name]); }
}
