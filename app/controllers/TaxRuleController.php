<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\TaxRule;
use App\Models\TaxType;
use App\Models\TaxPeriod;

class TaxRuleController extends BaseController
{
    private function adminOnly(): void { $this->requireRole(['SYSTEM_ADMINISTRATOR','ADMIN','SYSTEM ADMINISTRATOR']); }

    public function index(): void {
        $this->adminOnly();
        $this->view('tax-rules/index', ['rules' => (new TaxRule())->all()]);
    }

    public function show(int $id): void {
        $this->adminOnly();
        $rule = (new TaxRule())->find($id); if (!$rule) $this->abort(404, 'Tax rule not found.');
        $this->view('tax-rules/show', ['rule' => $rule]);
    }

    public function create(): void {
        $this->adminOnly();
        $this->view('tax-rules/create', ['taxTypes' => (new TaxType())->allActive(), 'taxPeriods' => (new TaxPeriod())->allActive()]);
    }

    public function store(): void {
        $this->adminOnly(); require_once PROJECT_ROOT . '/app/helpers/validation.php';
        $data=$this->input();
        validate_or_throw($data,[
            'tax_type_id'=>['required','integer'],'tax_period_id'=>['required','integer'],
            'rule_name'=>['required','string','max:255'],'rule_type'=>['required']
        ]);
        // Map form input onto the actual tax_rules schema.
        $payload = [
            'tax_type_id' => (int)$data['tax_type_id'],
            'period_id' => (int)$data['tax_period_id'],
            'rule_name' => trim((string)$data['rule_name']),
            'calculation_method' => strtoupper((string)$data['rule_type']),
            'effective_from' => trim((string)($data['effective_from'] ?? date('Y-m-d'))),
            'status' => strtoupper((string)($data['status'] ?? 'ACTIVE')) === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE',
        ];
        foreach ([['rate','base_rate'],['fixed_amount','base_amount']] as [$in,$col]) {
            if (isset($data[$in]) && $data[$in] !== '') { $payload[$col] = (float)$data[$in]; }
        }
        $payload['created_by'] = auth_id();
        $id = (new TaxRule())->create($payload); if (!$id) $this->abort(500, 'Unable to create tax rule.');
        if(function_exists('audit_log'))audit_log('CREATE','TAX_RULE',(int)$id,'Tax rule created.');
        $this->redirectTo('/tax-rules/'.$id);
    }

    public function edit(int $id): void {
        $this->adminOnly();
        $rule = (new TaxRule())->find($id); if (!$rule) $this->abort(404, 'Tax rule not found.');
        $this->view('tax-rules/edit', ['rule' => $rule]);
    }

    public function update(int $id): void {
        $this->adminOnly(); require_once PROJECT_ROOT . '/app/helpers/validation.php';
        $model = new TaxRule(); if (!$model->find($id)) $this->abort(404, 'Tax rule not found.');
        $data=$this->input();
        validate_or_throw($data,[
            'tax_type_id'=>['required','integer'],'tax_period_id'=>['required','integer'],
            'rule_code'=>['nullable','string','max:100'],'rule_name'=>['required','string','max:255'],
            'rule_type'=>['required']
        ]);
        // Map form input onto the actual tax_rules schema.
        $payload = [
            'tax_type_id' => (int)$data['tax_type_id'],
            'period_id' => (int)$data['tax_period_id'],
            'rule_name' => trim((string)$data['rule_name']),
            'calculation_method' => strtoupper((string)$data['rule_type']),
            'effective_from' => trim((string)($data['effective_from'] ?? date('Y-m-d'))),
            'status' => strtoupper((string)($data['status'] ?? 'ACTIVE')) === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE',
        ];
        foreach ([['rate','base_rate'],['fixed_amount','base_amount']] as [$in,$col]) {
            if (isset($data[$in]) && $data[$in] !== '') { $payload[$col] = (float)$data[$in]; }
        }
        if(!$model->update($id,$payload))$this->abort(500,'Unable to update tax rule.');
        if(function_exists('audit_log'))audit_log('UPDATE','TAX_RULE',$id,'Tax rule updated.');
        $this->redirectTo('/tax-rules/'.$id);
    }

    public function deactivate(int $id): void {
        $this->adminOnly();
        if (!(new TaxRule())->deactivate($id)) $this->abort(500, 'Unable to deactivate tax rule.');
        if(function_exists('audit_log'))audit_log('DEACTIVATE','TAX_RULE',$id,'Tax rule deactivated.');
        $this->redirectTo('/tax-rules');
    }
}
