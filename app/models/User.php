<?php
declare(strict_types=1);
namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        return $this->query(
            'SELECT u.*, r.role_name FROM users u INNER JOIN roles r ON r.role_id = u.role_id WHERE LOWER(u.email) = ? LIMIT 1',
            's',
            [strtolower(trim($email))]
        )->fetch_assoc() ?: null;
    }
    public function updateLastLogin(int $userId): bool { return $this->update($userId,['last_login'=>date('Y-m-d H:i:s')]); }
    public function staff(): array { return $this->query("SELECT u.*,r.role_name FROM users u INNER JOIN roles r ON r.role_id=u.role_id WHERE r.role_id IN (1,2) ORDER BY u.last_name,u.first_name")->fetch_all(MYSQLI_ASSOC); }
    public function withRoles(): array { return $this->query("SELECT u.*, r.role_name FROM users u INNER JOIN roles r ON r.role_id=u.role_id ORDER BY u.user_id")->fetch_all(MYSQLI_ASSOC); }
    public function activate(int $id): bool { return $this->update($id,['status'=>'ACTIVE']); }
    public function deactivate(int $id): bool { return $this->update($id,['status'=>'INACTIVE']); }
}
