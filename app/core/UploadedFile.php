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
        $this->name = $file['name'] ?? '';
        $this->type = $file['type'] ?? '';
        $this->size = $file['size'] ?? 0;
        $this->tmpPath = $file['tmp_name'] ?? '';
        $this->error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
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

    public function isAllowedMimeType(string $category = 'image'): bool
    {
        $mime = $this->getMimeType();

        if ($category === 'image') {
            return in_array($mime, $this->allowedImageMimeTypes, true);
        }

        if ($category === 'document') {
            return in_array($mime, $this->allowedDocumentMimeTypes, true);
        }

        return false;
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
        $filename = $filename ?: uniqid() . '_' . $originalName;
        $targetPath = rtrim($targetDir, '/') . '/' . $filename;

        if (!move_uploaded_file($this->tmpPath, $targetPath)) {
            throw new \Exception("파일 이동 실패: {$targetPath}");
        }

        return str_replace(UPLOAD_PATH, '', $targetPath);
    }
}
