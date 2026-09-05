<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testStrongPasswordPolicyAcceptsValidInput(): void
    {
        $this->assertTrue(password_is_strong('Abc12345'));
        $this->assertTrue(is_strong_password('QwErTy12'));
    }

    public function testStrongPasswordPolicyRejectsWeakInput(): void
    {
        $this->assertFalse(password_is_strong('weak'));
        $this->assertFalse(password_is_strong('abc12345'));
        $this->assertFalse(password_is_strong('ABCDEFGH'));
    }

    public function testRateLimitTriggersAfterThreshold(): void
    {
        session_start();
        $_SESSION = [];

        $bucket = 'login:user@example.com:' . md5('127.0.0.1');
        $_SESSION[$bucket] = [time() - 10, time() - 20];

        $this->assertTrue(is_login_rate_limited('user@example.com', 2, 300));;

        clear_login_failures('user@example.com');
        $this->assertFalse(isset($_SESSION[$bucket]));
    }

    public function testAppUrlBuilderReturnsQualifiedPath(): void
    {
        $expectedBase = rtrim(APP_BASE_URL, '/');

        $this->assertSame($expectedBase . '/login', url('/login'));
        $this->assertSame($expectedBase . '/assets/css/style.css', asset('css/style.css'));
    }
}
