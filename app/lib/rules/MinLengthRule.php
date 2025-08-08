<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class MinLengthRule implements RuleInterface {
    private int $min;

    public function __construct(int $min) {
        $this->min = $min;
    }

    public function passes($value): bool {
        return mb_strlen($value) >= $this->min;
    }

    public function message(): string {
        return str_replace(':min', $this->min, rule_message(static::class));
    }
}
