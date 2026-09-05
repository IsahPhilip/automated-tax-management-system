<?php
declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use mysqli_sql_exception;

class PenaltyService {
    public function calculate(array $assessment,?string $dueDate=null,?string $asOfDate=null): array {
        $p=max(0,(float)($assessment['tax_payable']??$assessment['amount_due']??0));$due=new DateTimeImmutable($dueDate?:'now');$as=new DateTimeImmutable($asOfDate?:'now');
        if($as<=$due)return ['days_late'=>0,'penalty'=>0.0,'rate'=>0.0];
        $days=(int)$due->diff($as)->days;$rate=$this->rate();return ['days_late'=>$days,'penalty'=>round($p*$rate,2),'rate'=>$rate];
    }
    private function rate(): float {
        if(function_exists('db')){
            try{
                $r=db()->query("SELECT rate FROM penalties WHERE code='LATE_PAYMENT' AND status='ACTIVE' LIMIT 1");
                if($r&&($x=$r->fetch_assoc()))return max(0,(float)$x['rate']);
            }catch(mysqli_sql_exception){
                // Configuration table may not exist yet; fall back to the default rate below.
            }
        }
        return .05;
    }
}