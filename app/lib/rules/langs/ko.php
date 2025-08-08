<?php

use app\facades\Rule;

return [
    Rule::UPLOAD_INI_SIZE           => '업로드된 파일이 php.ini의 upload_max_filesize 값을 초과했습니다.',
    Rule::UPLOAD_FORM_SIZE          => '업로드된 파일이 HTML 폼에서 지정한 MAX_FILE_SIZE 값을 초과했습니다.',
    Rule::UPLOAD_PARTIAL            => '파일이 부분적으로만 업로드되었습니다.',
    Rule::UPLOAD_NO_FILE            => '업로드된 파일이 없습니다.',
    Rule::UPLOAD_NO_TMP_DIR         => '서버에 임시 폴더가 없습니다.',
    Rule::UPLOAD_CANT_WRITE         => '디스크에 파일을 쓸 수 없습니다.',
    Rule::UPLOAD_EXTENSION_BLOCK    => 'PHP 확장 기능이 파일 업로드를 중단했습니다.',
    Rule::UPLOAD_UNKNOWN            => '알 수 없는 파일 업로드 오류가 발생했습니다.',
    
    Rule::NOT_EMPTY       => '이 필드는 필수입니다.',
    Rule::EMAIL           => '유효한 이메일 형식이 아닙니다.',
    Rule::PHONE           => '유효한 휴대폰 번호가 아닙니다.',
    Rule::STRING          => '문자열이어야 합니다.',
    Rule::NUMBER          => '숫자여야 합니다.',
    Rule::LOWERCASE       => '적어도 하나의 소문자를 포함해야 합니다.',
    Rule::UPPERCASE       => '적어도 하나의 대문자를 포함해야 합니다.',
    Rule::CONTAINS        => ':value 를 포함해야 합니다.',
    Rule::EQUAL_TO        => '값이 동일해야 합니다.',
    Rule::DATE            => '유효한 날짜 형식이어야 합니다.',
    Rule::TIME            => '유효한 시간 형식이어야 합니다.',
    Rule::DATETIME        => '유효한 날짜 및 시간 형식이어야 합니다.',
    Rule::MIN_LENGTH      => '최소 :min 글자 이상이어야 합니다.',
    Rule::MAX_LENGTH      => '최대 :max 글자 이하여야 합니다.',
    Rule::MIN             => '최소 :min 이상이어야 합니다.',
    Rule::MAX             => '최대 :max 이하여야 합니다.',
    Rule::RANGE           => ':min 이상 :max 이하의 값이어야 합니다.',
    Rule::RANGE_LENGTH    => '글자수는 :min 이상 :max 이하여야 합니다.',
    Rule::REGEX           => '형식이 올바르지 않습니다.',

    Rule::FILE_EMPTY       => '업로드된 파일이 없거나 비어 있습니다.',
    Rule::FILE_NOT_EMPTY   => '업로드된 파일이 비어있습니다.',
    Rule::FILE_SIZE        => '파일 크기는 최대 :max 이하여야 합니다.',

    Rule::FILE_GENERAL     => '이미지 또는 문서 파일만 업로드할 수 있습니다.',
    Rule::FILE_IMAGE       => '이미지 파일만 업로드할 수 있습니다.',
    Rule::FILE_VIDEO       => '비디오 파일만 업로드할 수 있습니다.',
    Rule::FILE_AUDIO       => '오디오 파일만 업로드할 수 있습니다.',
    Rule::FILE_ARCHIVE     => '압축 파일만 업로드할 수 있습니다.',
    Rule::FILE_DOCUMENT    => '문서 파일만 업로드할 수 있습니다.',
];
