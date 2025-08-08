<?php

namespace app\lib\rules\types;

use app\lib\rules\FileMultipleRule;
use app\lib\rules\FileNotEmptyRule;
use app\lib\rules\FileSizeRule;
use app\lib\rules\FileTypeRule;

class FileVideoRule extends FileMultipleRule
{
    public function __construct()
    {
        parent::__construct([
            new FileNotEmptyRule(),
            new FileSizeRule(MAX_VIDEO_SIZE),
            new FileTypeRule(
                static::class,
                ALLOWED_VIDEO_EXTENSIONS, 
                ALLOWED_VIDEO_MIMETYPES
            )
        ]);
    }
}
