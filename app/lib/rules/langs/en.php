<?php

use app\facades\Rule;

return [
    Rule::UPLOAD_INI_SIZE           => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
    Rule::UPLOAD_FORM_SIZE          => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
    Rule::UPLOAD_PARTIAL            => 'The uploaded file was only partially uploaded.',
    Rule::UPLOAD_NO_FILE            => 'No file was uploaded.',
    Rule::UPLOAD_NO_TMP_DIR         => 'Missing a temporary folder on the server.',
    Rule::UPLOAD_CANT_WRITE         => 'Failed to write file to disk.',
    Rule::UPLOAD_EXTENSION_BLOCK    => 'A PHP extension stopped the file upload.',
    Rule::UPLOAD_UNKNOWN            => 'An unknown upload error occurred.',

    Rule::NOT_EMPTY       => 'This field is required.',
    Rule::EMAIL           => 'The email format is invalid.',
    Rule::PHONE           => 'The phone number is invalid.',
    Rule::STRING          => 'This must be a string.',
    Rule::NUMBER          => 'This must be a number.',
    Rule::LOWERCASE       => 'At least one lowercase letter is required.',
    Rule::UPPERCASE       => 'At least one uppercase letter is required.',
    Rule::CONTAINS        => 'This must contain :value.',
    Rule::EQUAL_TO        => 'The value must match exactly.',
    Rule::DATE            => 'The date format is invalid.',
    Rule::TIME            => 'The time format is invalid (HH:MM or HH:MM:SS).',
    Rule::DATETIME        => 'The date and time format is invalid.',
    Rule::MIN_LENGTH      => 'Must be at least :min characters.',
    Rule::MAX_LENGTH      => 'Must be at most :max characters.',
    Rule::MIN             => 'Must be at least :min.',
    Rule::MAX             => 'Must be at most :max.',
    Rule::RANGE           => 'Must be between :min and :max.',
    Rule::RANGE_LENGTH    => 'Length must be between :min and :max characters.',
    Rule::REGEX           => 'The format is invalid.',

    Rule::FILE_EMPTY       => 'The uploaded file is missing or empty.',
    Rule::FILE_NOT_EMPTY   => 'The uploaded file is empty.',
    Rule::FILE_SIZE        => 'The file size must not exceed :max.',

    Rule::FILE_GENERAL     => 'Only image and document files are allowed.',
    Rule::FILE_IMAGE       => 'Only image files are allowed.',
    Rule::FILE_VIDEO       => 'Only video files are allowed.',
    Rule::FILE_AUDIO       => 'Only audio files are allowed.',
    Rule::FILE_ARCHIVE     => 'Only archive files are allowed.',
    Rule::FILE_DOCUMENT    => 'Only document files are allowed.',
];
