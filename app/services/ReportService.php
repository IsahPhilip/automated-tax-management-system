<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class ReportService {
    public function revenueSummary(?string $from=null,?string $to=null): array {
        if(!function_exists('db'))return [];
        $sql="SELECT COUNT(*) payment_count,COALESCE(SUM(amount),0) total_revenue FROM payments WHERE status='SUCCESS'";$p=[];$t='';
        if($from){$sql.=" AND paid_at>=?";$p[]=$from.' 00:00:00';$t.='s';} if($to){$sql.=" AND paid_at<=?";$p[]=$to.' 23:59:59';$t.='s';}
        $s=db()->prepare($sql);if(!$s)throw new RuntimeException('Unable to prepare report query.');if($p)$s->bind_param($t,...$p);$s->execute();$r=$s->get_result()->fetch_assoc()?:['payment_count'=>0,'total_revenue'=>0];$s->close();
        return ['payment_count'=>(int)$r['payment_count'],'total_revenue'=>(float)$r['total_revenue']];
    }
    public function taxpayerSummary(): array {
        if(!function_exists('db'))return [];
        $r=db()->query("SELECT COUNT(*) total_taxpayers,SUM(CASE WHEN status='ACTIVE' THEN 1 ELSE 0 END) active_taxpayers FROM taxpayers");
        return $r?($r->fetch_assoc()?:[]):[];
    }
}