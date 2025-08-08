<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;


class DateTimeRule implements RuleInterface {
    public function passes($value): bool {
        return date_create($value) !== false;
    }

    public function message(): string {
        return rule_message(static::class);
    }
}
