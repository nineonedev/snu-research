<?php 

namespace app\validation;

interface RuleInterface {
    public function passes($attribute, $value): bool;
    public function message($attribute): string;
}
