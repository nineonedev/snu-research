<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class UppercaseRule implements RuleInterface {
    public function passes($value): bool {
        return preg_match('/[A-Z]/', $value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
