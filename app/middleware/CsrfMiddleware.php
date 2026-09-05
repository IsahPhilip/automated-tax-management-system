<?php
declare(strict_types=1);
namespace App\Middleware;

class CsrfMiddleware
{
	public static function handle(): void
	{
		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
			$token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
			if (!isset($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
				http_response_code(419);
				exit('Invalid CSRF token');
			}
		}
	}
}
