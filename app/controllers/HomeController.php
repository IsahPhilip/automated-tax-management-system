<?php
declare(strict_types=1);
namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        if (function_exists('is_authenticated') && is_authenticated()) {
            $this->redirectTo('/dashboard');
        }

        $this->redirectTo('/login');
    }
}
