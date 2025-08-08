<?php 

namespace app\lib\rules;

use app\contracts\RuleInterface;

class UploadNoFileRule implements RuleInterface
{
    public function passes($file): bool
    {
        return !(isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}
