<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class EmailRule implements RuleInterface {
    public function passes($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
