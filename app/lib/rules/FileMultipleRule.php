<?php 

namespace app\lib\rules;

use app\contracts\RuleInterface;

abstract class FileMultipleRule implements RuleInterface {
    private array $rules = [];
    private int $ruleIndex = 0;

    public function __construct(array $rules = [])
    {   
        $this->rules = $rules;
    }

    public function passes($value): bool
    {
        $this->ruleIndex = 0; 

        foreach ($this->rules as $i => $rule) {
            if (!$rule->passes($value)) {
                $this->ruleIndex = $i;
                return false; 
            }
        }

        return true; 
    }

    public function message(): string
    {
        return $this->rules[$this->ruleIndex]->message();
    }
} 