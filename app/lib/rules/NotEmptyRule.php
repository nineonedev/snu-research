<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class NotEmptyRule implements RuleInterface {
    public function passes($value): bool {
        return !(empty($value) && $value !== '0');
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
