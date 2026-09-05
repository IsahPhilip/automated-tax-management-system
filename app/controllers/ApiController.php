<?php
declare(strict_types=1);
namespace App\Controllers;

class ApiController extends BaseController
{
    public function health(): never
    {
        $this->json([
            'success' => true,
            'message' => 'ATMS API is operational.',
            'data' => [
                'application' => defined('APP_NAME') ? APP_NAME : 'Automated Tax Management System',
                'timestamp' => date(DATE_ATOM),
            ],
        ]);
    }

    public function currentUser(): never
    {
        $this->requireAuth();

        $this->json([
            'success' => true,
            'data' => function_exists('auth_user') ? auth_user() : null,
        ]);
    }
}
