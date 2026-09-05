<?php
declare(strict_types=1);
namespace App\Controllers;

class StaffDashboardController extends DashboardController
{
    public function index(): void
    {
        $this->staff();
    }
}
