<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class FileEmptyRule implements RuleInterface
{
    public function passes($file): bool
    {
        return !$file 
            && !isset($file['tmp_name']) 
            && !is_uploaded_file($file['tmp_name']);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}
