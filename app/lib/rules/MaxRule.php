<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class MaxRule implements RuleInterface {
    private int $max;

    public function __construct(int $max) {
        $this->max = $max;
    }

    public function passes($value): bool {
        return is_numeric($value) && $value <= $this->max;
    }

    public function message(): string {
        return str_replace(':max', $this->max, rule_message(static::class));
    }
}
