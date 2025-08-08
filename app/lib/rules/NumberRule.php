<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class NumberRule implements RuleInterface {
    public function passes($value): bool {
        return is_numeric($value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
