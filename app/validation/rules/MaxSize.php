<?php 

namespace app\validation\rules;

use app\validation\RuleInterface;
use app\core\UploadedFile;

class MaxSize implements RuleInterface {
    protected int $maxBytes;

    public function __construct(int $maxKb)
    {
        $this->maxBytes = $maxKb * 1024;
    }

    public function passes($attribute, $value): bool
    {
        return $value instanceof UploadedFile && $value->getSize() <= $this->maxBytes;
    }

    public function message($attribute): string
    {
        return "{$attribute}는 {$this->maxBytes} 바이트 이하여야 합니다.";
    }
}