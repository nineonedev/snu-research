<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class LowercaseRule implements RuleInterface {
    public function passes($value): bool {
        return preg_match('/[a-z]/', $value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
