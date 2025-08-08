<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class FileNotEmptyRule implements RuleInterface
{
    public function passes($file): bool
    {
        return $file 
            && !empty($file['tmp_name']) 
            && is_uploaded_file($file['tmp_name']);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}
