<?php 

namespace app\core; 

class Json {
    private bool $success = false; 
    private string $message = '';
    private int $status = 200; 
    private array $data = [];

    static public function make(): Json 
    {
        return new Json(); 
    }
    
    public function success(bool $success): Json
    {
        $this->success = $success;
        return $this; 
    }

    public function message(string $message): Json 
    {
        $this->message = $message; 
        return $this; 
    }

    public function status(int $status): Json 
    {
        $this->status = $status; 
        return $this; 
    }

    public function data(array $data = []): Json
    {
        $this->data = $data; 
        return $this; 
    }
    
    public function response(): void 
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $jsonData = [
            'success' => $this->success,
            'message' => $this->message, 
            'status' => $this->status,
            'data' => $this->data,
        ];
        
        http_response_code($this->status); 
        echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); 
        exit; 
    }
}