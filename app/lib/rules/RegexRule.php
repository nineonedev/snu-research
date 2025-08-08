<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class RegexRule implements RuleInterface {
    private string $pattern;

    public function __construct(string $pattern) {
        $this->pattern = $pattern;
    }

    public function passes($value): bool {
        return preg_match($this->pattern, $value) === 1;
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
