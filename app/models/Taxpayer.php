<?php
declare(strict_types=1);
namespace App\Models;

class Taxpayer extends BaseModel
{
    protected string $table = 'taxpayers';
    protected string $primaryKey = 'taxpayer_id';

    public function findByNumber(string $number): ?array { return $this->firstWhere(['taxpayer_number'=>trim($number)]); }
    public function findByUserId(int $userId): ?array { return $this->firstWhere(['user_id'=>$userId]); }
    public function search(string $term, ?string $type=null): array { $term='%'.trim($term).'%'; $sql="SELECT * FROM taxpayers WHERE (taxpayer_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR business_name LIKE ? OR email LIKE ? OR phone LIKE ?)"; $types='ssssss'; $v=[$term,$term,$term,$term,$term,$term]; if($type!==null){$sql.=' AND taxpayer_type = ?';$types.='s';$v[]=$type;} $sql.=' ORDER BY last_name,first_name,business_name'; return $this->query($sql,$types,$v)->fetch_all(MYSQLI_ASSOC); }
    public function outstandingLiability(int $taxpayerId): float { $r=$this->query("SELECT COALESCE(SUM(balance_due),0) balance FROM tax_assessments WHERE taxpayer_id=? AND status NOT IN ('CANCELLED','DRAFT','REJECTED')",'i',[$taxpayerId]); return (float)$r->fetch_assoc()['balance']; }

    public function paginate(int $page = 1, int $perPage = 20, string $search = '', ?string $type = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;
        $where = [];
        $types = '';
        $values = [];

        if (trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $where[] = '(taxpayer_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR business_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $types .= 'ssssss';
            array_push($values, $term, $term, $term, $term, $term, $term);
        }

        if ($type !== null && $type !== '') {
            $where[] = 'taxpayer_type = ?';
            $types .= 's';
            $values[] = strtoupper($type);
        }

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $count = $this->query("SELECT COUNT(*) AS total FROM taxpayers{$whereSql}", $types, $values)->fetch_assoc();
        $total = (int) ($count['total'] ?? 0);

        $listTypes = $types . 'ii';
        $listValues = [...$values, $perPage, $offset];
        $rows = $this->query(
            "SELECT * FROM taxpayers{$whereSql} ORDER BY created_at DESC, taxpayer_id DESC LIMIT ? OFFSET ?",
            $listTypes,
            $listValues
        )->fetch_all(MYSQLI_ASSOC);

        return [
            'data' => $rows,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function generateTaxpayerNumber(): string
    {
        do {
            $number = 'TIN-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while ($this->findByNumber($number) !== null);

        return $number;
    }

    /** Aggregate counters powering the taxpayers report screen. */
    public function summary(): array
    {
        $row = $this->query(
            "SELECT COUNT(*) total_taxpayers,
                    COALESCE(SUM(CASE WHEN status='ACTIVE' THEN 1 ELSE 0 END),0) active_taxpayers,
                    COALESCE(SUM(CASE WHEN taxpayer_type IN ('BUSINESS','CORPORATE') THEN 1 ELSE 0 END),0) business_taxpayers
             FROM taxpayers"
        )->fetch_assoc() ?: [];
        return array_map('intval', $row);
    }
}
