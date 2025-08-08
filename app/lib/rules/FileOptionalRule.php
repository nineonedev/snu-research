<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;
use app\core\File;

class FileOptionalRule implements RuleInterface
{
    private RuleInterface $fallbackRule;
    
    public function __construct(RuleInterface $fallbackRule)
    {
        $this->fallbackRule = $fallbackRule;
    }

    public function passes($file): bool
    {
        if (File::isMissingUpload($file)) {
            return true;
        }
        
        return $this->fallbackRule->passes($file);
    }

    public function message(): string
    {
        return $this->fallbackRule->message();
    }
}
