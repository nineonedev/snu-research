<?php 


// ==============================================
// 이미지 파일
// ==============================================
define('MAX_IMAGE_SIZE', MB(10));

define('ALLOWED_IMAGE_MIMETYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp',
    'image/bmp',
    'image/svg+xml',
]);

define('ALLOWED_IMAGE_EXTENSIONS', [
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'
]);



// ==============================================
// 문서 파일
// ==============================================
define('MAX_DOCUMENT_SIZE', MB(10));

define('ALLOWED_DOCUMENT_MIMETYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
    'text/plain',
]);

define('ALLOWED_DOCUMENT_EXTENSIONS', [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'
]);

// ===========================================
// 일반 파일
// ===========================================
define('MAX_GENERAL_SIZE', MB(10));
define('ALLOWED_GENERAL_MIMETYPES', array_merge(
        ALLOWED_IMAGE_MIMETYPES,
        ALLOWED_DOCUMENT_MIMETYPES
));

define('ALLOWED_GENERAL_EXTENSIONS', array_merge(
    ALLOWED_IMAGE_EXTENSIONS, 
    ALLOWED_DOCUMENT_EXTENSIONS
));

// ==============================================
// 오디오 파일
// ==============================================
define('MAX_AUDIO_SIZE', MB(20));

define('ALLOWED_AUDIO_MIMETYPES', [
    'audio/mpeg',
    'audio/wav',
    'audio/ogg',
    'audio/mp4',
    'audio/webm',
    'audio/x-ms-wma',
]);

define('ALLOWED_AUDIO_EXTENSIONS', [
    'mp3', 'wav', 'ogg', 'm4a', 'webm', 'wma'
]);

// ==============================================
// 비디오 파일
// ==============================================
define('MAX_VIDEO_SIZE', MB(100));

define('ALLOWED_VIDEO_MIMETYPES', [
    'video/mp4',
    'video/x-msvideo', // avi
    'video/x-ms-wmv',
    'video/webm',
    'video/quicktime', // mov
    'video/mpeg',
]);

define('ALLOWED_VIDEO_EXTENSIONS', [
    'mp4', 'avi', 'wmv', 'webm', 'mov', 'mpeg'
]);

// ==============================================
// 압축 파일
// ==============================================
define('MAX_ARCHIVE_SIZE', MB(50));

define('ALLOWED_ARCHIVE_MIMETYPES', [
    'application/zip',
    'application/x-rar-compressed',
    'application/x-7z-compressed',
    'application/x-tar',
    'application/x-gzip',
]);

define('ALLOWED_ARCHIVE_EXTENSIONS', [
    'zip', 'rar', '7z', 'tar', 'gz'
]);
