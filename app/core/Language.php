<?php 

namespace app\core;

class Language {
    private string $locale; 
    private array $loaded = [];

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function get()
    {
        
    }

    public function locale(): string 
    {
        return $this->locale;
    }
}