<?php
declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use mysqli_sql_exception;

class InterestService {
    public function calculate(float $principal,?string $dueDate=null,?string $asOfDate=null): array {
        $p=max(0,$principal);$due=new DateTimeImmutable($dueDate?:'now');$as=new DateTimeImmutable($asOfDate?:'now');
        if($as<=$due)return ['days_late'=>0,'annual_rate'=>0.0,'interest'=>0.0];
        $days=(int)$due->diff($as)->days;$rate=$this->annualRate();return ['days_late'=>$days,'annual_rate'=>$rate,'interest'=>round($p*$rate*($days/365),2)];
    }
    private function annualRate(): float {
        if(function_exists('db')){
            try{
                $r=db()->query("SELECT annual_rate FROM interest_config WHERE code='DEFAULT_TAX_INTEREST' AND status='ACTIVE' LIMIT 1");
                if($r&&($x=$r->fetch_assoc()))return max(0,(float)$x['annual_rate']);
            }catch(mysqli_sql_exception){
                // Configuration table may not exist yet; fall back to the default rate below.
            }
        }
        return .15;
    }
}