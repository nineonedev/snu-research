<?php 

namespace app\core; 

class Csrf {
    const KEY = '_csrf_token';
    const FIELD = '_token';
    
    public static function generateToken(): string 
    {
        if (!Session::get(self::KEY)) {
            Session::set(self::KEY, bin2hex(random_bytes(32))); 
        }

        return Session::get(self::KEY);
    }

    public static function getToken(): ?string 
    {
        return Session::get(self::KEY);
    }

    public static function verifyToken($token): bool
    {
        return Session::get(self::KEY) && hash_equals(Session::get(self::KEY), $token);
    }

    public static function input(): string
    {
        return '<input type="hidden" name="'.self::FIELD.'" value="'.self::generateToken().'">';
    }
}