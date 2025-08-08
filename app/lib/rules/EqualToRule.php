<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class EqualToRule implements RuleInterface {
    private $target;

    public function __construct($target) {
        $this->target = $target;
    }

    public function passes($value): bool {
        return $value === $this->target;
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
