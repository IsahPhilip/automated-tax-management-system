<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Notification;
use App\Models\User;

/**
 * Personal notifications inbox plus an administrative sender.
 */
class NotificationController extends BaseController
{
    private const STAFF_ROLES = [
        1, 2,
        'SYSTEM_ADMINISTRATOR', 'SYSTEM ADMINISTRATOR', 'ADMIN',
        'REVENUE_OFFICER', 'REVENUE OFFICER', 'REVENUE/TAX OFFICER',
        'TAX_OFFICER', 'TAX OFFICER',
    ];

    public function index(): void
    {
        $this->requireAuth();
        $model = new Notification();
        $uid = (int) auth_id();

        $this->view('notifications/index', [
            'notifications' => $model->forUser($uid),
            'unreadCount' => $model->unreadCount($uid),
        ]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();
        $model = new Notification();
        $item = $model->find($id);
        if (!$item) {
            $this->abort(404, 'Notification not found.');
        }
        if ((int) ($item['user_id'] ?? 0) !== (int) auth_id()) {
            $this->abort(403, 'You are not authorized to view this notification.');
        }

        $model->markRead((int) $id);

        $this->view('notifications/show', ['notification' => $item]);
    }

    public function read(int $id): void
    {
        $this->requireAuth();
        $model = new Notification();
        $item = $model->find($id);
        if (!$item || (int) ($item['user_id'] ?? 0) !== (int) auth_id()) {
            $this->abort(404, 'Notification not found.');
        }
        $model->markRead((int) $id);
        $this->redirectTo('/notifications');
    }

    public function readAll(): void
    {
        $this->requireAuth();
        (new Notification())->markAllRead((int) auth_id());
        $this->redirectTo('/notifications');
    }

    public function sendForm(): void
    {
        $this->requireRole(self::STAFF_ROLES);
        $this->view('notifications/send', [
            'users' => (new User())->withRoles(),
        ]);
    }

    public function send(): void
    {
        $this->requireRole(self::STAFF_ROLES);
        require_once PROJECT_ROOT . '/app/helpers/validation.php';

        $data = $this->input();
        validate_or_throw($data, [
            'user_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'notification_type' => ['required'],
        ]);

        $id = (new Notification())->create([
            'user_id' => (int) $data['user_id'],
            'title' => trim((string) $data['title']),
            'message' => trim((string) $data['message']),
            'notification_type' => strtoupper((string) $data['notification_type']),
        ]);
        if (!$id) {
            $this->abort(500, 'Unable to send notification.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'NOTIFICATION', (int) $id, 'Notification sent to user #' . (int) $data['user_id'] . '.');
        }
        if (function_exists('flash_success')) {
            flash_success('Notification sent successfully.');
        }

        $this->redirectTo('/notifications');
    }
}
