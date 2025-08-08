<?php 

namespace app\lib\rules;

use app\contracts\RuleInterface;

class UploadNoTmpDirRule implements RuleInterface
{
    public function passes($file): bool
    {
        return !(isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_TMP_DIR);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}
