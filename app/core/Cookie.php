<?php 

namespace app\core; 

class Cookie {
    public static function set(
        string $name, 
        string $value, 
        int $expire = 3600, 
        string $path = '/'
    ): void
    {
        setcookie($name, $value, time() + $expire, $path);
    }

    public static function get(string $name): ?string 
    {
        return $_COOKIE[$name] ?? null;
    }

    public static function delete(string $name, string $path = '/'): void
    {
        setcookie($name, '', time() - 3600, $path);
    }

    public static function exists(string $name): bool
    {
        return isset($_COOKIE[$name]); 
    }
}