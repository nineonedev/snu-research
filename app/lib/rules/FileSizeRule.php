<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;

class FileSizeRule implements RuleInterface
{
    private int $maxSize;

    public function __construct(int $maxMb)
    {
        $this->maxSize = MB($maxMb);
    }

    public function passes($file): bool
    {
        return isset($file['size']) && $file['size'] <= $this->maxSize;
    }

    public function message(): string
    {
        return str_replace(':max', format_filesize($this->maxSize), rule_message(static::class));
    }
}
