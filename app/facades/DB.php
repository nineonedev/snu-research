<?php

namespace app\facades;

use app\core\QueryBuilder;
use Exception;

class DB {
    
    public static function table(string $table)
    {
        return new QueryBuilder($table);
    }

    public static function __callStatic($name, $arguments)
    {
        $db = App::database();
        if (method_exists($db, $name)) {
            return call_user_func_array([$db, $name], $arguments);
        }

        throw new Exception('No method found in Databse:' . $name);
    }
}