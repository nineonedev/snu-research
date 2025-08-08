<?php

use app\facades\App;
use app\facades\Route;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__));
define('ROOT_URL', '');

require_once 'util.php';

spl_autoload_register(function($class){
    $class = str_replace('\\', '/', $class).'.php'; 
    
    $file = ROOT_PATH.DS.$class;
    
    if(!file_exists($file)){
        throw new Exception('No found class: ' . $class); 
    }
    
    require_once $file; 
});


require_once 'config.php';
require_once 'helper.php';

App::init();

require_once 'route.php';

$response = Route::dispatch();
$response->send(); 
