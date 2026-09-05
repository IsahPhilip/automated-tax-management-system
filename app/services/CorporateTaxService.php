<?php
declare(strict_types=1);
namespace App\Services;

class CorporateTaxService {
    public function calculate(array $d): array {
        $turn=max(0,(float)($d['turnover']??0)); $profit=max(0,(float)($d['taxable_profit']??$d['profit']??0));
        $rate=isset($d['tax_rate'])?max(0,min(1,(float)$d['tax_rate'])):($turn<=25000000?0:($turn<=100000000?.20:.30));
        $credit=max(0,(float)($d['tax_credits']??0)); $tax=$profit*$rate;
        return ['tax_type'=>'CIT','turnover'=>round($turn,2),'taxable_profit'=>round($profit,2),'rate'=>$rate,'tax_before_credits'=>round($tax,2),'credits'=>round($credit,2),'tax_payable'=>round(max(0,$tax-$credit),2)];
    }
}