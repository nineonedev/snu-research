<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class RangeRule implements RuleInterface {
    private int $min, $max;

    public function __construct(int $min, int $max) {
        $this->min = $min;
        $this->max = $max;
    }

    public function passes($value): bool {
        return is_numeric($value) && $value >= $this->min && $value <= $this->max;
    }

    public function message(): string {
        return str_replace(
            [':min', ':max'],
            [$this->min, $this->max],
            rule_message(static::class)
        );
    }
}
