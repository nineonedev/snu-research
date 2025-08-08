<?php

namespace app\lib\rules;

use app\contracts\RuleInterface;
use finfo;

class FileTypeRule implements RuleInterface
{
    private string $typeClass;
    private array $allowedExtensions;
    private array $allowedMimeTypes;
    
    public function __construct(
        string $typeClass, 
        array $allowedExtensions, 
        array $allowedMimeTypes
    )
    {
        $this->typeClass = $typeClass;
        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function passes($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $ext = strtolower(pathinfo($value['name'] ?? '', PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedExtensions, true)) {
            return false;
        }

        if (!isset($value['tmp_name']) || !is_file($value['tmp_name'])) {
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($value['tmp_name']);

        return in_array($mime, $this->allowedMimeTypes, true);
    }

    public function message(): string
    {
        return rule_message($this->typeClass);
    }       
}
