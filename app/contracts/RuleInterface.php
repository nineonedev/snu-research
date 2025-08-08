<?php 

namespace app\contracts; 

interface RuleInterface {
    public function passes($value): bool; 
    public function message(): string; 
}