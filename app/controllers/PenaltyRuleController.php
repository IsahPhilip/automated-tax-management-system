<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\PenaltyRule;

class PenaltyRuleController extends BaseController
{
    public function index(): void
    {
        $this->requireRole(1);
        $this->view('tax-rules/index', ['rules' => (new PenaltyRule())->all()]);
    }
}
