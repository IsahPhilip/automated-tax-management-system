<?php
declare(strict_types=1);
namespace App\Controllers;

abstract class BaseController
{
    protected function view(string $view, array $data = []): void
    {
        $viewFile = PROJECT_ROOT . '/app/views/' . ltrim($view, '/') . '.php';

        if (!is_file($viewFile)) {
            $this->abort(500, 'View not found.');
        }

        $styles = is_array($data['styles'] ?? null) ? $data['styles'] : [];
        if (str_contains($view, 'dashboard/')) {
            $styles[] = '/assets/css/dashboard.css';
        }
        if (($data['isReport'] ?? false) || str_contains($view, 'reports/')) {
            $styles[] = '/assets/css/reports.css';
        }
        $styles = array_values(array_unique(array_filter($styles, static fn ($style) => is_string($style) && $style !== '')));
        $data['styles'] = $styles;

        extract($data, EXTR_SKIP);

        if (filesize($viewFile) === 0) {
            $title = ucwords(str_replace(['/', '-'], [' / ', ' '], $view));
            $content = '<p>This screen is wired up, but its detailed interface has not been implemented yet.</p>';
            require PROJECT_ROOT . '/app/views/layouts/main.php';
            return;
        }

        if (str_starts_with($view, 'auth/')) {
            require $viewFile;
            return;
        }

        require PROJECT_ROOT . '/app/views/layouts/main.php';
    }

    protected function json(array $data = [], int $status = 200): never
    {
        if (function_exists('json_response')) {
            json_response($data, $status);
        }

        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirectTo(string $url, int $status = 302): never
    {
        if (function_exists('redirect')) {
            redirect($url, $status);
        }

        if (!preg_match('#^https?://#i', $url) && function_exists('app_url')) {
            $url = app_url($url);
        }

        header('Location: ' . $url, true, $status);
        exit;
    }

    protected function abort(int $status, string $message = ''): never
    {
        if (function_exists('abort')) {
            abort($status, $message);
        }

        http_response_code($status);
        exit($message);
    }

    protected function requireAuth(): void
    {
        if (function_exists('require_auth')) {
            require_auth();
            return;
        }

        $this->abort(401, 'Authentication required.');
    }

    protected function requireRole(string|int|array $roles): void
    {
        if (function_exists('require_role')) {
            require_role($roles);
            return;
        }

        $this->abort(403, 'You are not authorized to access this resource.');
    }

    protected function input(?string $key = null, mixed $default = null): mixed
    {
        return function_exists('request_input')
            ? request_input($key, $default)
            : ($key === null ? $_POST : ($_POST[$key] ?? $default));
    }

    protected function method(): string
    {
        return function_exists('request_method')
            ? request_method()
            : strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}
