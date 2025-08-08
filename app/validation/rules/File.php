<?php 

namespace app\validation\rules;

use app\validation\RuleInterface;
use app\core\UploadedFile;

class File implements RuleInterface {
    public function passes($attribute, $value): bool {
        return $value instanceof UploadedFile && $value->isValid();
    }

    public function message($attribute): string {
        return "{$attribute}는 유효한 파일이 아닙니다.";
    }
}
