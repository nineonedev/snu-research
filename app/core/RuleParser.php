<?php 

namespace app\core;

use app\contracts\RuleInterface;
use app\facades\Rule;
use Exception;

class RuleParser {
    private array $ruleOptions = []; 
    private array $rules = [];

    public function __construct(array $ruleOptions = [])
    {
        $this->ruleOptions = $ruleOptions; 
        $this->parse();
    }

    public function getRules(): array
    {
        return $this->rules; 
    }

    public function deduplicate(): void
    {
        
        foreach ($this->rules as $attr => &$ruleList) {
            $seen = []; 

            $ruleList = array_filter($ruleList, function ($rule) use (&$seen) {
                $class = get_class($rule); 

                if (in_array($class, $seen, true)) {
                    return false; 
                }

                $seen[] = $class;
                return true; 
            }); 
        }
    }

    public function parse(): void
    {
        foreach ($this->ruleOptions as $attrName => $rules) {
            if (is_string($rules)) {
                $ruleParts = rm_space(explode('|', $rules));
                foreach ($ruleParts as $rule) {
                    $this->rules[$attrName][] = $this->createRuleFromString($rule, $attrName);
                }
                continue;
            } 
            
            if (is_array($rules)) {
                foreach ($rules as $rule) {
                    if (is_string($rule) && Rule::has($rule)) {
                        $this->rules[$attrName][] = Rule::get($rule);
                        continue;
                    }
                    
                    if ($rule instanceof RuleInterface) {
                        $this->rules[$attrName][] = $rule; 
                        continue;
                    }
                    
                    throw new Exception("Validator rule type error: {$attrName}"); 
                }
                continue; 
            }

            throw new Exception("Validator rules argument type error: {$attrName}"); 
        }

        $this->deduplicate();
    }

    public function createRuleFromString(string $rule, string $attrName): RuleInterface
    {
        $parts = rm_space(explode(':', $rule));
        $key = $parts[0];
        $value = $parts[1] ?? null;

        
        if (!Rule::has($key)) {
            throw new Exception("Syntax error or No matching rule: {$key}");
        }

        $args = [];

        if ($key === Rule::FILE_OPTIONAL) {
            if (!in_array($value, Rule::$fileTypes)) {
                throw new Exception("No found fallback rule: {$key}");
            }

            $fileTypeRuleClass = Rule::get($value);
            $args = [new $fileTypeRuleClass()];

        } else if (in_array($key, Rule::$rulesExpectingFile)) {
            $value = $_FILES[$attrName] ?? null;
            $args = [$value];

        } else if ($value !== null) {
            $args = str_contains($value, ',') ? explode(',', $value) : [$value];
            $args = array_map('trim', $args);
        }


        return Rule::$key(...$args);
    }
}