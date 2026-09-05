<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\TaxType;

class TaxTypeController extends BaseController
{
    private function adminOnly(): void { $this->requireRole(['SYSTEM_ADMINISTRATOR','ADMIN','SYSTEM ADMINISTRATOR']); }

    public function index(): void {
        $this->adminOnly();
        $this->view('tax-types/index', ['taxTypes'=>(new TaxType())->all()]);
    }

    public function show(int $id): void {
        $this->adminOnly();
        $item=(new TaxType())->find($id); if(!$item)$this->abort(404,'Tax type not found.');
        $this->view('tax-types/show',['taxType'=>$item]);
    }

    public function create(): void { $this->adminOnly(); $this->view('tax-types/create'); }

    public function store(): void {
        $this->adminOnly(); require_once PROJECT_ROOT.'/app/helpers/validation.php';
        $data=$this->input();
        validate_or_throw($data,['code'=>['required','string','max:50'],'name'=>['required','string','max:255'],'description'=>['nullable','string','max:1000']]);
        $id=(new TaxType())->create($data); if(!$id)$this->abort(500,'Unable to create tax type.');
        if(function_exists('audit_log'))audit_log('CREATE','TAX_TYPE',(int)$id,'Tax type created.');
        $this->redirectTo('/tax-types/'.$id);
    }

    public function edit(int $id): void {
        $this->adminOnly();
        $item=(new TaxType())->find($id); if(!$item)$this->abort(404,'Tax type not found.');
        $this->view('tax-types/edit',['taxType'=>$item]);
    }

    public function update(int $id): void {
        $this->adminOnly(); require_once PROJECT_ROOT.'/app/helpers/validation.php';
        $model=new TaxType(); if(!$model->find($id))$this->abort(404,'Tax type not found.');
        $data=$this->input();
        validate_or_throw($data,['code'=>['required','string','max:50'],'name'=>['required','string','max:255'],'description'=>['nullable','string','max:1000']]);
        if(!$model->update($id,$data))$this->abort(500,'Unable to update tax type.');
        if(function_exists('audit_log'))audit_log('UPDATE','TAX_TYPE',$id,'Tax type updated.');
        $this->redirectTo('/tax-types/'.$id);
    }

    public function deactivate(int $id): void {
        $this->adminOnly();
        if(!(new TaxType())->deactivate($id))$this->abort(500,'Unable to deactivate tax type.');
        if(function_exists('audit_log'))audit_log('DEACTIVATE','TAX_TYPE',$id,'Tax type deactivated.');
        $this->redirectTo('/tax-types');
    }
}
