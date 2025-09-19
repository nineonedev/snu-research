<?php

namespace app\core;

class UploadedFile
{
    protected string $name;
    protected string $type;
    protected int $size;
    protected string $tmpPath;
    protected int $error;

    public function __construct(array $file)
    {
        $this->name   = $file['name']     ?? '';
        $this->type   = $file['type']     ?? '';
        $this->size   = $file['size']     ?? 0;
        $this->tmpPath= $file['tmp_name'] ?? '';
        $this->error  = $file['error']    ?? UPLOAD_ERR_NO_FILE;
    }

    protected array $allowedImageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    protected array $allowedDocumentMimeTypes = [
        'application/pdf',
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/vnd.ms-excel', // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-powerpoint', // .ppt
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
    ];

    protected array $allowedVideoMimeTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
        'video/quicktime',     // .mov
        'video/mpeg',          // .mpg/.mpeg
        'video/x-msvideo',     // .avi
        'video/x-ms-wmv',      // .wmv
        'video/3gpp',          // .3gp
    ];

    public static function delete(string $relativePath): bool
    {
        $fullPath = rtrim(UPLOAD_PATH, '/') . '/' . ltrim($relativePath, '/');

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    public function hasUploaded(): bool
    {
        return $this->error !== UPLOAD_ERR_NO_FILE;
    }

    public function getOriginalName(): string
    {
        return $this->name;
    }

    public function getMimeType(): string
    {
        return $this->type;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * 카테고리가 null이면 image/document/video 모두 허용
     * $category: 'image' | 'document' | 'video' | null
     */
    public function isAllowedMimeType(?string $category = null): bool
    {
        $mime = $this->getMimeType();

        if ($category === null) {
            return in_array($mime, $this->allowedImageMimeTypes, true)
                || in_array($mime, $this->allowedDocumentMimeTypes, true)
                || in_array($mime, $this->allowedVideoMimeTypes, true);
        }

        if ($category === 'image') {
            return in_array($mime, $this->allowedImageMimeTypes, true);
        }

        if ($category === 'document') {
            return in_array($mime, $this->allowedDocumentMimeTypes, true);
        }

        if ($category === 'video') {
            return in_array($mime, $this->allowedVideoMimeTypes, true);
        }

        return false;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getErrorMessage(): string
    {
    switch ($this->error) {
        case UPLOAD_ERR_OK: return 'OK';
        case UPLOAD_ERR_INI_SIZE: return 'upload_max_filesize를 초과했습니다.';
        case UPLOAD_ERR_FORM_SIZE: return 'HTML form의 MAX_FILE_SIZE를 초과했습니다.';
        case UPLOAD_ERR_PARTIAL: return '파일이 일부만 업로드되었습니다.';
        case UPLOAD_ERR_NO_FILE: return '파일이 업로드되지 않았습니다.';
        case UPLOAD_ERR_NO_TMP_DIR: return '임시 폴더가 없습니다 (upload_tmp_dir).';
        case UPLOAD_ERR_CANT_WRITE: return '디스크에 파일을 쓸 수 없습니다.';
        case UPLOAD_ERR_EXTENSION: return 'PHP 확장에 의해 업로드가 중단되었습니다.';
        default: return '알 수 없는 업로드 오류('.$this->error.')';
    }
}


    public function getTempPath(): string
    {
        return $this->tmpPath;
    }

    public function move(string $targetDir, string $filename = ''): string
    {
        if (!$this->isValid()) {
            throw new \Exception("파일 업로드 실패: 오류 코드 {$this->error}");
        }

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new \Exception("디렉토리 생성 실패: {$targetDir}");
            }
        }

        $originalName = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $this->getOriginalName());
        $filename = $filename ?: uniqid('', true) . '_' . $originalName;
        $targetPath = rtrim($targetDir, '/') . '/' . $filename;

        if (!move_uploaded_file($this->tmpPath, $targetPath)) {
            throw new \Exception("파일 이동 실패: {$targetPath}");
        }

        // 저장 경로를 UPLOAD_PATH 기준의 상대경로로 반환
        return str_replace(rtrim(UPLOAD_PATH, '/'), '', $targetPath);
    }
}
