<?php
declare(strict_types=1);

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sanitize_text')) {
    function sanitize_text(?string $value, int $maxLength = 255): string
    {
        $text = preg_replace('/[\x00-\x1F\x7F]/u', '', trim((string) $value)) ?? '';
        return mb_substr($text, 0, $maxLength);
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email(?string $email): string
    {
        return strtolower(trim((string) filter_var($email, FILTER_SANITIZE_EMAIL)));
    }
}

if (!function_exists('valid_email')) {
    function valid_email(?string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('generate_reference')) {
    function generate_reference(string $prefix, int $length = 12): string
    {
        return strtoupper($prefix) . '-' . strtoupper(substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length));
    }
}

if (!function_exists('money')) {
    function money(float|int|string|null $amount, string $currency = 'NGN '): string
    {
        return $currency . number_format((float) ($amount ?? 0), 2);
    }
}

if (!function_exists('decimal_amount')) {
    function decimal_amount(mixed $amount): float
    {
        return is_numeric($amount) ? round((float) $amount, 2) : 0.0;
    }
}

if (!function_exists('password_is_strong')) {
    function password_is_strong(?string $password): bool
    {
        $value = (string) ($password ?? '');
        if (strlen($value) < 8) {
            return false;
        }

        $hasUpper = preg_match('/[A-Z]/', $value) === 1;
        $hasLower = preg_match('/[a-z]/', $value) === 1;
        $hasNumber = preg_match('/\d/', $value) === 1;

        return $hasUpper && $hasLower && $hasNumber;
    }
}

if (!function_exists('is_strong_password')) {
    function is_strong_password(?string $password): bool
    {
        return password_is_strong($password);
    }
}

if (!function_exists('client_ip')) {
    function client_ip(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR', 'HTTP_CLIENT_IP'];
        foreach ($keys as $key) {
            $value = $_SERVER[$key] ?? '';
            if ($value === '') {
                continue;
            }

            foreach (explode(',', (string) $value) as $part) {
                $ip = trim($part);
                if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }
}

if (!function_exists('login_attempt_bucket')) {
    function login_attempt_bucket(string $email): string
    {
        return 'login:' . strtolower(trim($email)) . ':' . md5(client_ip());
    }
}

if (!function_exists('record_login_failure')) {
    function record_login_failure(string $email): int
    {
        $bucket = login_attempt_bucket($email);
        $window = 300;
        $limit = 5;

        $attempts = $_SESSION[$bucket] ?? [];
        $now = time();
        $attempts = array_values(array_filter($attempts, static fn ($ts) => is_numeric($ts) && ((int) $ts + $window) > $now));
        $attempts[] = $now;
        $_SESSION[$bucket] = $attempts;

        return count($attempts);
    }
}

if (!function_exists('is_login_rate_limited')) {
    function is_login_rate_limited(string $email, int $limit = 5, int $windowSeconds = 300): bool
    {
        $bucket = login_attempt_bucket($email);
        $attempts = $_SESSION[$bucket] ?? [];
        $now = time();
        $attempts = array_values(array_filter($attempts, static fn ($ts) => is_numeric($ts) && ((int) $ts + $windowSeconds) > $now));
        $_SESSION[$bucket] = $attempts;

        return count($attempts) >= $limit;
    }
}

if (!function_exists('clear_login_failures')) {
    function clear_login_failures(string $email): void
    {
        unset($_SESSION[login_attempt_bucket($email)]);
    }
}
