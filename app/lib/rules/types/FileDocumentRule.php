<?php

namespace app\lib\rules\types;

use app\lib\rules\FileMultipleRule;
use app\lib\rules\FileNotEmptyRule;
use app\lib\rules\FileSizeRule;
use app\lib\rules\FileTypeRule;

class FileDocumentRule extends FileMultipleRule
{
    public function __construct()
    {
        parent::__construct([
            new FileUploadRule(),
            new FileNotEmptyRule(),
            new FileSizeRule(MAX_DOCUMENT_SIZE),
            new FileTypeRule(
                static::class,
                ALLOWED_DOCUMENT_EXTENSIONS, 
                ALLOWED_DOCUMENT_MIMETYPES
            )
        ]);
    }
}
