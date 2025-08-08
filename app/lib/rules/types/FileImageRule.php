<?php

namespace app\lib\rules\types;

use app\lib\rules\FileMultipleRule;
use app\lib\rules\FileNotEmptyRule;
use app\lib\rules\FileSizeRule;
use app\lib\rules\FileTypeRule;


class FileImageRule extends FileMultipleRule
{
    public function __construct()
    {
        parent::__construct([
            new FileUploadRule(),
            new FileNotEmptyRule(),
            new FileSizeRule(MAX_IMAGE_SIZE),
            new FileTypeRule(
                static::class,
                ALLOWED_IMAGE_EXTENSIONS, 
                ALLOWED_IMAGE_MIMETYPES
            )
        ]);
    }
}
