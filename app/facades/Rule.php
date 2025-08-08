<?php

namespace app\facades;

use app\contracts\RuleInterface;
use app\lib\rules\EmptyRule;
use app\lib\rules\NotEmptyRule;
use app\lib\rules\MinRule;
use app\lib\rules\MaxRule;
use app\lib\rules\RangeRule;
use app\lib\rules\MinLengthRule;
use app\lib\rules\MaxLengthRule;
use app\lib\rules\RangeLengthRule;
use app\lib\rules\EmailRule;
use app\lib\rules\PhoneRule;
use app\lib\rules\StringRule;
use app\lib\rules\NumberRule;
use app\lib\rules\LowercaseRule;
use app\lib\rules\UppercaseRule;
use app\lib\rules\ContainsRule;
use app\lib\rules\EqualToRule;
use app\lib\rules\DateRule;
use app\lib\rules\TimeRule;
use app\lib\rules\DateTimeRule;
use app\lib\rules\FileEmptyRule;
use app\lib\rules\FileNotEmptyRule;
use app\lib\rules\FileOptionalRule;
use app\lib\rules\FileSizeRule;
use app\lib\rules\RegexRule;
use app\lib\rules\types\FileArchiveRule;
use app\lib\rules\types\FileAudioRule;
use app\lib\rules\types\FileDocumentRule;
use app\lib\rules\types\FileGeneralRule;
use app\lib\rules\types\FileImageRule;
use app\lib\rules\types\FileVideoRule;
use app\lib\rules\UploadIniSizeExceededRule;
use app\lib\rules\UploadFormSizeExceededRule;
use app\lib\rules\UploadPartialRule;
use app\lib\rules\UploadNoFileRule;
use app\lib\rules\UploadNoTmpDirRule;
use app\lib\rules\UploadCantWriteRule;
use app\lib\rules\UploadExtensionBlockedRule;
use app\lib\rules\UploadUnknownErrorRule;
use Exception;

class Rule {
    const EMPTY             = 'empty';
    const NOT_EMPTY         = 'notEmpty';
    const MIN               = 'min';
    const MAX               = 'max';
    const RANGE             = 'range';
    const MIN_LENGTH        = 'minLength';
    const MAX_LENGTH        = 'maxLength';
    const RANGE_LENGTH      = 'rangeLength';
    const EMAIL             = 'email';
    const PHONE             = 'phone';
    const STRING            = 'string';
    const NUMBER            = 'number';
    const LOWERCASE         = 'lowercase';
    const UPPERCASE         = 'uppercase';
    const CONTAINS          = 'contains';
    const EQUAL_TO          = 'equalTo';
    const DATE              = 'date';
    const TIME              = 'time';
    const DATETIME          = 'datetime';
    const REGEX             = 'regex';

    // Upload error specific
    const UPLOAD_INI_SIZE        = 'uploadIniSize';
    const UPLOAD_FORM_SIZE       = 'uploadFormSize';
    const UPLOAD_PARTIAL         = 'uploadPartial';
    const UPLOAD_NO_FILE         = 'uploadNoFile';
    const UPLOAD_NO_TMP_DIR      = 'uploadNoTmpDir';
    const UPLOAD_CANT_WRITE      = 'uploadCantWrite';
    const UPLOAD_EXTENSION_BLOCK = 'uploadExtensionBlocked';
    const UPLOAD_UNKNOWN         = 'uploadUnknown';
    const FILE_OPTIONAL          = 'fileOptional';
    const FILE_EMPTY             = 'fileEmpty';
    const FILE_NOT_EMPTY         = 'fileNotEmpty';
    const FILE_SIZE              = 'fileSize';
    const FILE_GENERAL           = 'file';
    const FILE_IMAGE             = 'image';
    const FILE_VIDEO             = 'video';
    const FILE_ARCHIVE           = 'archive';
    const FILE_AUDIO             = 'audio';
    const FILE_DOCUMENT          = 'document';

    public static array $fileTypes = [
        self::FILE_GENERAL,
        self::FILE_IMAGE,
        self::FILE_VIDEO,
        self::FILE_ARCHIVE,
        self::FILE_AUDIO,
        self::FILE_DOCUMENT,
        self::FILE_OPTIONAL,
    ];

    public static array $rulesExpectingFile = [
        self::UPLOAD_INI_SIZE,        
        self::UPLOAD_FORM_SIZE,       
        self::UPLOAD_PARTIAL,         
        self::UPLOAD_NO_FILE,         
        self::UPLOAD_NO_TMP_DIR,      
        self::UPLOAD_CANT_WRITE,      
        self::UPLOAD_EXTENSION_BLOCK, 
        self::FILE_OPTIONAL,
        self::FILE_EMPTY,
        self::FILE_NOT_EMPTY,
        self::FILE_IMAGE,
        self::FILE_VIDEO,
        self::FILE_ARCHIVE,
        self::FILE_AUDIO,
        self::FILE_DOCUMENT,
    ];

    public static array $map = [
        self::EMPTY             => EmptyRule::class,
        self::NOT_EMPTY         => NotEmptyRule::class,
        self::MIN               => MinRule::class,
        self::MAX               => MaxRule::class,
        self::RANGE             => RangeRule::class,
        self::MIN_LENGTH        => MinLengthRule::class,
        self::MAX_LENGTH        => MaxLengthRule::class,
        self::RANGE_LENGTH      => RangeLengthRule::class,
        self::EMAIL             => EmailRule::class,
        self::PHONE             => PhoneRule::class,
        self::STRING            => StringRule::class,
        self::NUMBER            => NumberRule::class,
        self::LOWERCASE         => LowercaseRule::class,
        self::UPPERCASE         => UppercaseRule::class,
        self::CONTAINS          => ContainsRule::class,
        self::EQUAL_TO          => EqualToRule::class,
        self::DATE              => DateRule::class,
        self::TIME              => TimeRule::class,
        self::DATETIME          => DateTimeRule::class,
        self::REGEX             => RegexRule::class,

        // Upload error mapping
        self::UPLOAD_INI_SIZE        => UploadIniSizeExceededRule::class,
        self::UPLOAD_FORM_SIZE       => UploadFormSizeExceededRule::class,
        self::UPLOAD_PARTIAL         => UploadPartialRule::class,
        self::UPLOAD_NO_FILE         => UploadNoFileRule::class,
        self::UPLOAD_NO_TMP_DIR      => UploadNoTmpDirRule::class,
        self::UPLOAD_CANT_WRITE      => UploadCantWriteRule::class,
        self::UPLOAD_EXTENSION_BLOCK => UploadExtensionBlockedRule::class,
        self::UPLOAD_UNKNOWN         => UploadUnknownErrorRule::class,

        self::FILE_OPTIONAL     => FileOptionalRule::class,
        self::FILE_EMPTY        => FileEmptyRule::class,
        self::FILE_NOT_EMPTY    => FileNotEmptyRule::class,
        self::FILE_SIZE         => FileSizeRule::class,
        self::FILE_GENERAL      => FileGeneralRule::class,
        self::FILE_IMAGE        => FileImageRule::class,
        self::FILE_DOCUMENT     => FileDocumentRule::class,
        self::FILE_ARCHIVE      => FileArchiveRule::class,
        self::FILE_AUDIO        => FileAudioRule::class,
        self::FILE_VIDEO        => FileVideoRule::class,
    ];

    public static function __callStatic(string $name, array $arguments)
    {
        if (!array_key_exists($name, self::$map)) {
            throw new Exception("Rule '{$name}' does not exist.");
        }

        $className = self::$map[$name];
        return new $className(...$arguments);
    }

    public static function has($key)
    {
        return array_key_exists($key, self::$map);
    }

    public static function get($key): string
    {
        return self::has($key) ? self::$map[$key] : null;
    }

    public static function isFileTypeInstance(RuleInterface $ruleInstance): bool
    {
        $map = [];
        
        foreach (self::$fileTypes as $type) {
            $map[$type] = self::$map[$type]; 
        }

        return in_array(get_class($ruleInstance), $map);
    }
}
