<?php

namespace app\lib\rules\types;

use app\lib\rules\FileMultipleRule;

use app\lib\rules\UploadIniSizeExceededRule;
use app\lib\rules\UploadFormSizeExceededRule;
use app\lib\rules\UploadPartialRule;
use app\lib\rules\UploadNoFileRule;
use app\lib\rules\UploadCantWriteRule;
use app\lib\rules\UploadExtensionBlockedRule;
use app\lib\rules\UploadUnknownErrorRule;

class FileUploadRule extends FileMultipleRule
{
    public function __construct()
    {
        parent::__construct([
            new UploadIniSizeExceededRule(),
            new UploadFormSizeExceededRule(),
            new UploadPartialRule(),
            new UploadNoFileRule(),
            new UploadCantWriteRule(),
            new UploadExtensionBlockedRule(),
            new UploadUnknownErrorRule(),
        ]);
    }
}
