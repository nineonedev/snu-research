<?php

namespace app\rules;

use app\contracts\RuleInterface;

class RequiredRule implements RuleInterface
{
    public function passes(string $attribute, $value): bool
    {
        return !is_null($value) && $value !== '';
    }

    public function message(string $attribute): string
    {
        return "{$attribute} 필드는 필수입니다.";
    }
}
