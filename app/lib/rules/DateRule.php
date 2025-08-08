<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class DateRule implements RuleInterface {
    public function passes($value): bool {
        return strtotime($value) !== false;
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
