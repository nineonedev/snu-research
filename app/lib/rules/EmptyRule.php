<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class EmptyRule implements RuleInterface {
    public function passes($value): bool {
        return empty((string) $value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
