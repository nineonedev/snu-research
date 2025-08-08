<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class TimeRule implements RuleInterface {
    public function passes($value): bool {
        return preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value);
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
