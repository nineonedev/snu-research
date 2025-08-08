<?php 

namespace app\lib\rules;

use app\contracts\RuleInterface;

class UploadUnknownErrorRule implements RuleInterface
{
    public function passes($file): bool
    {
        if (!isset($file['error'])) return true; 

        $knownErrors = [
            UPLOAD_ERR_OK,
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION,
        ];

        return in_array($file['error'], $knownErrors, true);
    }

    public function message(): string
    {
        return rule_message(static::class);
    }
}