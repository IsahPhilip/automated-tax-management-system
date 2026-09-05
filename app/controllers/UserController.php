<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\User;
use App\Models\Role;

class UserController extends BaseController
{
    private function adminOnly(): void
    {
        $this->requireRole(['SYSTEM_ADMINISTRATOR', 'ADMIN', 'SYSTEM ADMINISTRATOR']);
    }

    public function index(): void
    {
        $this->adminOnly();
        $this->view('users/index', ['users' => (new User())->all()]);
    }

    public function show(int $id): void
    {
        $this->adminOnly();
        $user = (new User())->find($id);
        if (!$user) $this->abort(404, 'User not found.');
        $this->view('users/show', ['user' => $user]);
    }

    public function create(): void
    {
        $this->adminOnly();
        $this->view('users/create', ['roles' => (new Role())->all()]);
    }

    public function store(): void
    {
        $this->adminOnly();
        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $data = $this->input();
        validate_or_throw($data, [
            'role_id' => ['required', 'integer'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'confirmed'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
        ]);

        if (!password_is_strong((string)$data['password'])) {
            $this->abort(422, 'Password must contain uppercase, lowercase, number and at least 8 characters.');
        }

        $data['password_hash'] = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        $data['email'] = sanitize_email((string)$data['email']);
        unset($data['password'], $data['password_confirmation']);

        $id = (new User())->create($data);
        if (!$id) $this->abort(500, 'Unable to create user.');

        if (function_exists('audit_log')) audit_log('CREATE', 'USER', (int)$id, 'System user created.');
        if (function_exists('flash_success')) flash_success('User created successfully.');
        $this->redirectTo('/users/' . $id);
    }

    public function edit(int $id): void
    {
        $this->adminOnly();

        $user = (new User())->find($id);
        if (!$user) $this->abort(404, 'User not found.');

        $this->view('users/edit', ['user' => $user, 'roles' => (new Role())->all()]);
    }

    public function update(int $id): void
    {
        $this->adminOnly();
        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $model = new User();
        if (!$model->find($id)) $this->abort(404, 'User not found.');

        $data = $this->input();
        validate_or_throw($data, [
            'role_id' => ['required', 'integer'],
            'email' => ['required', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
        ]);

        $data['email'] = sanitize_email((string)$data['email']);

        if (!empty($data['password'])) {
            if (!password_is_strong((string)$data['password'])) {
                $this->abort(422, 'Password does not meet the security requirements.');
            }
            $data['password_hash'] = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        }

        unset($data['password'], $data['password_confirmation']);

        if (!$model->update($id, $data)) $this->abort(500, 'Unable to update user.');
        if (function_exists('audit_log')) audit_log('UPDATE', 'USER', $id, 'System user updated.');

        $this->redirectTo('/users/' . $id);
    }

    public function activate(int $id): void
    {
        $this->adminOnly();
        if (!(new User())->activate($id)) $this->abort(500, 'Unable to activate user.');
        if (function_exists('audit_log')) audit_log('ACTIVATE', 'USER', $id, 'System user activated.');
        $this->redirectTo('/users');
    }

    public function deactivate(int $id): void
    {
        $this->adminOnly();

        if (function_exists('auth_id') && (int)auth_id() === $id) {
            $this->abort(422, 'You cannot deactivate your own administrator account.');
        }

        if (!(new User())->deactivate($id)) $this->abort(500, 'Unable to deactivate user.');
        if (function_exists('audit_log')) audit_log('DEACTIVATE', 'USER', $id, 'System user deactivated.');
        $this->redirectTo('/users');
    }
}
