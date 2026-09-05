<?php
declare(strict_types=1);
namespace App\Services;

class PersonalIncomeTaxService {
    public function calculate(array $data): array {
        $gross=max(0,(float)($data['gross_income']??$data['annual_gross_income']??0));
        $ded=$this->deductions($data); $chargeable=max(0,$gross-$ded['total']);
        $bands=$this->bands($data['tax_period_id']??null); $tax=$this->progressiveTax($chargeable,$bands);
        $credits=max(0,(float)($data['tax_credits']??0));
        return ['tax_type'=>'PIT','gross_income'=>round($gross,2),'deductions'=>$ded,'chargeable_income'=>round($chargeable,2),'tax_before_credits'=>round($tax,2),'credits'=>round($credits,2),'tax_payable'=>round(max(0,$tax-$credits),2),'bands'=>$bands];
    }
    private function deductions(array $d): array {
        $x=['pension'=>max(0,(float)($d['pension_deduction']??0)),'nhf'=>max(0,(float)($d['nhf_deduction']??0)),'nhis'=>max(0,(float)($d['nhis_deduction']??0)),'other'=>max(0,(float)($d['other_deductions']??0))];
        $x['total']=array_sum($x); return array_map(fn($v)=>round((float)$v,2),$x);
    }
    private function bands(?int $period): array {
        if($period && function_exists('db')){
            $s=db()->prepare("SELECT lower_limit,upper_limit,rate FROM tax_rules WHERE tax_period_id=? AND rule_type='PIT_BAND' AND status='ACTIVE' ORDER BY lower_limit");
            if($s){$s->bind_param('i',$period);$s->execute();$r=$s->get_result();$b=[];while($row=$r->fetch_assoc())$b[]=['lower'=>(float)$row['lower_limit'],'upper'=>$row['upper_limit']===null?null:(float)$row['upper_limit'],'rate'=>(float)$row['rate']];$s->close();if($b)return $b;}
        }
        return [['lower'=>0,'upper'=>800000,'rate'=>0],['lower'=>800000,'upper'=>3000000,'rate'=>.15],['lower'=>3000000,'upper'=>12000000,'rate'=>.18],['lower'=>12000000,'upper'=>25000000,'rate'=>.21],['lower'=>25000000,'upper'=>50000000,'rate'=>.23],['lower'=>50000000,'upper'=>null,'rate'=>.25]];
    }
    private function progressiveTax(float $income,array $bands): float {
        $tax=0; foreach($bands as $b){$lo=(float)$b['lower'];$hi=$b['upper']===null?null:(float)$b['upper'];if($income<=$lo)continue;$base=$hi===null?$income-$lo:min($income,$hi)-$lo;if($base>0)$tax+=$base*max(0,(float)$b['rate']);if($hi!==null&&$income<=$hi)break;} return $tax;
    }
}