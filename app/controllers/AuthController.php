<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (function_exists('is_authenticated') && is_authenticated()) {
            $this->redirectTo('/dashboard');
        }

        $this->view('auth/login');
    }

    public function login(): void
    {
        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $data = [
            'email' => function_exists('sanitize_email')
                ? sanitize_email((string) $this->input('email', ''))
                : trim((string) $this->input('email', '')),
            'password' => (string) $this->input('password', ''),
        ];

        validate_or_throw($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (function_exists('is_login_rate_limited') && is_login_rate_limited($data['email'], 5, 300)) {
            if (function_exists('flash_error')) {
                flash_error('Too many failed login attempts. Please wait 5 minutes before trying again.');
            }
            $this->redirectTo('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || empty($user['password_hash']) ||
            !password_verify($data['password'], (string) $user['password_hash'])) {
            if (function_exists('record_login_failure')) {
                record_login_failure($data['email']);
            }

            if (function_exists('audit_log')) {
                audit_log('LOGIN_FAILED', 'AUTH', null, 'Invalid login credentials.');
            }

            if (function_exists('flash_error')) {
                flash_error('Invalid email or password.');
            }

            $this->redirectTo('/login');
        }

        if (function_exists('clear_login_failures')) {
            clear_login_failures($data['email']);
        }

        if (isset($user['status']) && strtolower((string) $user['status']) !== 'active') {
            if (function_exists('flash_error')) {
                flash_error('Your account is not active.');
            }

            $this->redirectTo('/login');
        }

        if (function_exists('login_user')) {
            login_user($user);
        }

        if (function_exists('audit_log')) {
            audit_log('LOGIN', 'AUTH', (int) $user['user_id'], 'User logged in successfully.');
        }

        $this->redirectTo('/dashboard');
    }

    public function logout(): void
    {
        $userId = function_exists('auth_id') ? auth_id() : null;

        if (function_exists('audit_log')) {
            audit_log('LOGOUT', 'AUTH', $userId, 'User logged out.');
        }

        if (function_exists('logout_user')) {
            logout_user();
        }

        $this->redirectTo('/login');
    }
}
