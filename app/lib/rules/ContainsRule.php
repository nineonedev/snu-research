<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class ContainsRule implements RuleInterface {
    private string $needle;

    public function __construct(string $needle) {
        $this->needle = $needle;
    }

    public function passes($value): bool {
        return strpos($value, $this->needle) !== false;
    }

    public function message(): string {
        return str_replace(':value', $this->needle, rule_message(static::class));
    }
}
