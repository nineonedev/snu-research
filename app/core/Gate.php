<?php 

namespace app\core;

use app\contracts\PolicyInterface;

class Gate {
    protected static array $policies = [];

    public static function define(string $model, PolicyInterface $policy) 
    {
        static::$policies[$model] = $policy;
    }

    public static function allows(string $action, string $modelClass, $user, $model = null): bool
    {
        if (!isset(static::$policies[$modelClass])) {
            return false; 
        }

        return static::$policies[$modelClass]->can($action, $user, $model);
    }
}