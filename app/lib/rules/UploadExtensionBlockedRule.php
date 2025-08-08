<?php 

namespace app\lib\rules;

use app\contracts\RuleInterface;

class UploadExtensionBlockedRule implements RuleInterface
{
    public function passes($file): bool
    {
        return !(isset($file['error']) && $file['error'] === UPLOAD_ERR_EXTENSION);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}