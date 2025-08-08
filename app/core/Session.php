<?php

namespace app\core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, $default = null)
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function removeFlashed(): void
    {
        unset($_SESSION['_flash']);
    }

    // === Laravel 스타일 ===

    public static function old(string $key, $default = null)
    {
        return self::get('_old_input')[$key] ?? $default;
    }

    public static function getOldInput(): array
    {
        return self::get('_old_input') ?? [];
    }

    public static function getErrors(): array
    {
        return self::get('_errors') ?? [];
    }

    public static function hasErrors(): bool
    {
        return !empty(self::get('_errors'));
    }
}
