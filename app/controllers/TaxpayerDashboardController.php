<?php
declare(strict_types=1);
namespace App\Controllers;

class TaxpayerDashboardController extends DashboardController
{
    public function index(): void
    {
        $this->taxpayer();
    }
}
