<?php
declare(strict_types=1);
namespace App\Models;
use InvalidArgumentException;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use Throwable;

require_once PROJECT_ROOT . '/config/database.php';

abstract class BaseModel
{
    protected mysqli $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() { $this->db = db(); }

    public function find(int|string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
        $type = is_int($id) ? 'i' : 's';
        $stmt->bind_param($type, $id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function all(string $orderBy = '', string $direction = 'DESC'): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy !== '') {
            $this->assertIdentifier($orderBy);
            $direction = strtoupper($direction);
            $direction = in_array($direction, ['ASC','DESC'], true) ? $direction : 'DESC';
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function where(array $conditions, string $orderBy = '', string $direction = 'DESC', ?int $limit = null): array
    {
        [$where, $types, $values] = $this->buildWhere($conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$where}";
        if ($orderBy !== '') {
            $this->assertIdentifier($orderBy);
            $direction = strtoupper($direction);
            $direction = in_array($direction, ['ASC','DESC'], true) ? $direction : 'DESC';
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }
        if ($limit !== null) { $sql .= ' LIMIT ?'; $types .= 'i'; $values[] = $limit; }
        $stmt = $this->db->prepare($sql); $this->bindValues($stmt, $types, $values); $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function firstWhere(array $conditions, string $orderBy = ''): ?array
    { return ($this->where($conditions, $orderBy, 'DESC', 1)[0] ?? null); }

    public function count(array $conditions = []): int
    {
        if ($conditions === []) return (int)$this->db->query("SELECT COUNT(*) AS total FROM {$this->table}")->fetch_assoc()['total'];
        [$where,$types,$values] = $this->buildWhere($conditions);
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE {$where}");
        $this->bindValues($stmt,$types,$values); $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public function exists(array $conditions): bool { return $this->count($conditions) > 0; }

    public function create(array $data): int
    {
        if ($data === []) throw new InvalidArgumentException('Cannot create an empty record.');
        $columns = array_keys($data); foreach ($columns as $c) $this->assertIdentifier($c);
        $marks = implode(', ', array_fill(0,count($columns),'?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (".implode(', ',$columns).") VALUES ({$marks})");
        $values = array_values($data); $this->bindValues($stmt,$this->inferTypes($values),$values); $stmt->execute();
        return (int)$this->db->insert_id;
    }

    public function update(int|string $id, array $data): bool
    {
        if ($data === []) return false;
        $sets=[];$values=[]; foreach($data as $column=>$value){$this->assertIdentifier($column);$sets[]="$column = ?";$values[]=$value;}
        $values[]=$id; $types=$this->inferTypes(array_slice($values,0,-1)).(is_int($id)?'i':'s');
        $stmt=$this->db->prepare("UPDATE {$this->table} SET ".implode(', ',$sets)." WHERE {$this->primaryKey} = ?");
        $this->bindValues($stmt,$types,$values);$stmt->execute();return $stmt->affected_rows>=0;
    }

    public function delete(int|string $id): bool
    {
        $stmt=$this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $type=is_int($id)?'i':'s';$stmt->bind_param($type,$id);$stmt->execute();return $stmt->affected_rows>0;
    }

    protected function query(string $sql,string $types='',array $values=[]): mysqli_result|bool
    { $stmt=$this->db->prepare($sql);if($types!=='')$this->bindValues($stmt,$types,$values);$stmt->execute();return $stmt->get_result(); }

    protected function execute(string $sql,string $types='',array $values=[]): bool
    { $stmt=$this->db->prepare($sql);if($types!=='')$this->bindValues($stmt,$types,$values);$stmt->execute();return true; }

    protected function transaction(callable $callback): mixed
    { $this->db->begin_transaction();try{$r=$callback($this->db);$this->db->commit();return $r;}catch(Throwable $e){$this->db->rollback();throw $e;} }

    protected function buildWhere(array $conditions): array
    {
        $parts=[];$types='';$values=[];
        foreach($conditions as $column=>$value){
            $this->assertIdentifier($column);
            if($value===null){$parts[]="$column IS NULL";continue;}
            if(is_array($value)){if(!$value){$parts[]='1 = 0';continue;}$parts[]="$column IN (".implode(', ',array_fill(0,count($value),'?')).')';foreach($value as $v){$types.=$this->inferType($v);$values[]=$v;}continue;}
            $parts[]="$column = ?";$types.=$this->inferType($value);$values[]=$value;
        }
        if(!$parts)throw new InvalidArgumentException('At least one WHERE condition is required.');
        return [implode(' AND ',$parts),$types,$values];
    }

    protected function bindValues(mysqli_stmt $stmt,string $types,array $values): void
    { if($types==='')return;$refs=[$types];foreach($values as $k=>$v)$refs[]=&$values[$k];$stmt->bind_param(...$refs); }
    protected function inferTypes(array $values): string { return implode('',array_map(fn($v)=>$this->inferType($v),$values)); }
    protected function inferType(mixed $value): string { return is_int($value)?'i':(is_float($value)?'d':'s'); }
    protected function assertIdentifier(string $identifier): void { if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/',$identifier))throw new InvalidArgumentException("Invalid SQL identifier: {$identifier}"); }
}
