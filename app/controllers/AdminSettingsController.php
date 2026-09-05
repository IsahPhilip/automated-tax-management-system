<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\SystemSetting;

class AdminSettingsController extends BaseController
{
    public function index(): void
    {
        $this->requireRole(1);
        $this->view('admin/settings', ['settings' => (new SystemSetting())->all()]);
    }

    public function update(): void
    {
        $this->requireRole(1);

        $model = new SystemSetting();
        $updatedBy = auth_id() ?? 1;
        $settings = $_POST['settings'] ?? [];

        foreach ($settings as $key => $value) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }

            $existing = $model->firstWhere(['setting_key' => $key]);
            $model->set(
                $key,
                (string) $value,
                $existing['description'] ?? null,
                $updatedBy
            );
        }

        if (function_exists('audit_log')) {
            audit_log('UPDATE', 'SYSTEM_SETTINGS', null, 'System settings updated.');
        }

        $this->redirectTo('/admin/settings');
    }
}
