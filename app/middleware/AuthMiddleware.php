<?php
declare(strict_types=1);
namespace App\Middleware;

class AuthMiddleware
{
	public static function handle(): void
	{
		if (!function_exists('is_authenticated') || !is_authenticated()) {
			header('Location: ' . app_url('/login'));
			exit;
		}
	}
}
