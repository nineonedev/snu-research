<?php 

namespace app\core;

use Exception;

class View {
    protected string $basePath;
    protected array $sections = [];
    protected string $currentSection = '';
    protected string $layout = 'home'; 

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath ?: VIEW_PATH;
    }

    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->basePath . DS . trim(str_replace('.', DS, $template), DS) . '.php';
        
        if(!file_exists($templateFile)) {
            throw new Exception("View file not found: $templateFile");
        }

        ob_start();
        extract($data); 
        include $templateFile;
        return ob_get_clean(); 
    }

    public function extend($layout = 'home'): void
    {
        $this->layout = $layout;
    }

    public function withLayout(string $template, array $data = [], string $layout = 'home'): string
    {
        $this->extend($layout); 

        $content = $this->render($template, $data);
        $data['content'] = $content; 

        return $this->render("layouts.{$this->layout}", $data);
    }

    public function startSection(string $name): void 
    {
        $this->currentSection = $name; 
        ob_start(); 
    }

    public function endSection(): void
    {
        $this->sections[$this->currentSection] = ob_get_clean();
    }

    public function yield(string $name): void 
    {
        echo $this->sections[$name] ?? ''; 
    }

    public function include(string $viewName, array $data = []): void 
    {
        echo $this->render($viewName, $data); 
    }
}