<?php 

namespace app\core;

use app\facades\Route;

class Application {
    public Database $database;
    public Request $request;
    public Response $response; 
    public Language $language;
    public View $view; 

    public function __construct()
    {
        
        Session::start();
        Route::init();

        $this->database = new Database(); 
        $this->database->connect();

        $this->request = new Request();
        $this->response = new Response(); 
        $this->language = new Language('ko');

        $this->view = new View();
    }

    public function run()
    {
        echo 'run';
    }
}