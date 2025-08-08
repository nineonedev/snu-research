<?php 

namespace app\facades;

use app\core\Application;
use app\core\Database;
use app\core\Language;
use app\core\Request;
use app\core\Response;
use app\core\View;
use Exception;

class App {
    public static Application $instance;

    public static function init()
    {
        Route::init();
        self::$instance = new Application();
    }

    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance; 
        }

        self::$instance = new Application(); 
        return self::$instance;
    }

    public static function request(): Request
    {
        return self::getInstance()->request; 
    }

    public static function response(): Response
    {
        return self::getInstance()->response; 
    }

    public static function database(): Database
    {
        return self::getInstance()->database;
    }

    public static function language(): Language
    {
        return self::getInstance()->language;
    }

    public static function view(): View 
    {
        return self::getInstance()->view; 
    }

    public static function __callStatic($name, $arguments)
    {
        $app = self::getInstance();

        if (!method_exists($app, $name)) {
            throw new Exception('No found app method: '. $name);    
        }

        return call_user_func_array([$app, $name], $arguments);
    }
}