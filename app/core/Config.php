<?php 

namespace app\core; 

class Config {
    static array $settings = []; 
    
    static public function set($key, $value): void
    {
        self::$settings[$key] = $value;
    }

    static public function get($key) 
    {
        return isset(self::$settings[$key]) ? self::$settings[$key] :null; 
    }

    static public function isDevelopment(): bool
    {
        return APP_ENV === ENV_DEVELOPMENT;    
    }

    static public function isProduction(): bool
    {
        return APP_ENV === ENV_PRODUCTION;
    }
}