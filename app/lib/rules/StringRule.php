<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class StringRule implements RuleInterface {
    public function passes($value): bool {
        return is_string($value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
