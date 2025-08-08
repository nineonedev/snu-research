<?php

namespace app\lib\rules\types;

use app\lib\rules\FileMultipleRule;
use app\lib\rules\FileNotEmptyRule;
use app\lib\rules\FileSizeRule;
use app\lib\rules\FileTypeRule;

class FileArchiveRule extends FileMultipleRule
{
    public function __construct()
    {
        parent::__construct([
            new FileUploadRule(),
            new FileNotEmptyRule(),
            new FileSizeRule(MAX_ARCHIVE_SIZE),
            new FileTypeRule(
                static::class,
                ALLOWED_ARCHIVE_EXTENSIONS, 
                ALLOWED_ARCHIVE_MIMETYPES
            )
        ]);
    }
}
