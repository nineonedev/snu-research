<?php 

namespace app\core;

class Disk {
    private string $uploadDir;
    private array $uploadedFileData;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = rtrim($uploadDir, DS);    
    
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true); 
        }
    }
    
    public function put(File $file): bool
    {
        $this->uploadedFileData = [];

        if (!$file->validate()) {
            return false; 
        }

        $destination = $this->path($file);
        $success = move_uploaded_file($file->getTmpName(), $destination); 
    
        if ($success) {
            $fileData = array_merge($file->getData(), [
                'path' => $destination
            ]);
            $this->uploadedFileData = $fileData;
        }

        return $success; 
    }

    public function url(File $file): string 
    {
        $basename = $file->getHashedName();
        $relativePath = str_replace(UPLOAD_PATH, '', $this->uploadDir); 
        return UPLOAD_URL.DS.$relativePath.DS.$basename;
    }

    public function lastPutData(): array
    {
        return $this->uploadedFileData;
    }

    public function path(File $file): string 
    {
        return $this->uploadDir.DS.$file->getHashedName();
    }

    public function exists(string $filename): bool
    {
        return file_exists($this->uploadDir.DS.$filename);
    }

    public function delete(string $filename): bool 
    {
        $path = $this->uploadDir.DS.$filename;

        if (file_exists($path)) {
            return unlink($path);
        }

        return false; 
    }

    public function getUploadDir(): string 
    {
        return $this->uploadDir;
    }


    public function read(string $filename): ?string
    {
        $fullPath = $this->uploadDir.DS.$filename;

        if (!file_exists($fullPath)) {
            return null;
        }

        return file_get_contents($fullPath);
    }


    public function download(string $filename, ?string $downloadName = null): void
    {
        $fullPath = $this->uploadDir.DS.$filename;

        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo 'File not found.';
            exit;
        }

        $downloadName = $downloadName ?: basename($filename);
        $mimeType = mime_content_type($fullPath);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Pragma: public');
        flush();
        readfile($fullPath);
        exit;
    }

}