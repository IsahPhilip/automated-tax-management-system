<?php
declare(strict_types=1);
namespace App\Middleware;

class RoleMiddleware
{
	public static function require_role(int $roleId): void
	{
		if (!has_role($roleId)) {
			http_response_code(403);
			exit('Access denied.');
		}
	}
}
