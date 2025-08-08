<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class PhoneRule implements RuleInterface {
    public function passes($value): bool {
        return preg_match('/^01[016789]-?\d{3,4}-?\d{4}$/', $value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
