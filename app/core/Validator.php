<?php

namespace app\core;

class Validator {
    private array $data = []; 
    private array $errors = [];
    private RuleParser $ruleParser;

    public static function make($data, $ruleOptions): Validator
    {
        return new Validator($data, $ruleOptions);
    }

    public function __construct(array $data = [], array $ruleOptions = [])
    {
        $this->data = $data;
        $this->ruleParser = new RuleParser($ruleOptions);
    }

    public function getParser()
    {
        return $this->ruleParser;
    }

    public function setData(array $data = []): void
    {
        $this->data = $data; 
    }

    public function responeErrors()
    {
        ApiResponse::validationError($this->getErrors());
    }

    public function reset(): void
    {
        $this->resetData();
        $this->resetErrors();
    }

    private function resetErrors()
    {
        $this->errors = [];
    }

    private function resetData()
    {
        $this->data = [];
    }


    public function safe(): array
    {
        $data = []; 
        foreach ($this->ruleParser->getRules() as $key => $value) {
            if (!array_key_exists($key, $this->errors)) {
                $data[$key] = $this->data[$key]; 
            }
        }
        return $data; 
    }

    public function execute(): bool
    {
        foreach ($this->ruleParser->getRules() as $attrName => $rules) {
            $value = $this->data[$attrName] ?? null;
            foreach ($rules as $rule) {
                if (!$rule->passes($value)) {
                    $this->errors[$attrName][] = $rule->message(); 
                }
            }
        }
        return !$this->fails();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function fails(): bool 
    {
        return !empty($this->errors);
    }
}