<?php 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit('POST request method required');
}

if (empty($_FILES)) {
    exit('$_FILES is empty - is file_uploads set to "Off" in php.ini?');
}

if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

    switch ($_FILES["image"]["error"]) {
        case UPLOAD_ERR_PARTIAL:
            exit('File only partially uploaded');
            break;
        case UPLOAD_ERR_NO_FILE:
            exit('No file was uploaded');
            break;
        case UPLOAD_ERR_EXTENSION:
            exit('File upload stopped by a PHP extension');
            break;
        case UPLOAD_ERR_FORM_SIZE:
            exit('File exceeds MAX_FILE_SIZE in the HTML form');
            break;
        case UPLOAD_ERR_INI_SIZE:
            exit('File exceeds upload_max_filesize in php.ini');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            exit('Temporary folder not found');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            exit('Failed to write file');
            break;
        default:
            exit('Unknown upload error');
            break;
    }
}

// Reject uploaded file larger than 1MB
if ($_FILES["image"]["size"] > 1048576) {
    exit('File too large (max 1MB)');
}

// Use fileinfo to get the mime type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($_FILES["image"]["tmp_name"]);

$mime_types = ["image/gif", "image/png", "image/jpeg"];
        
if ( ! in_array($mime_type, $mime_types)) {
    exit("Invalid file type");
}

// Replace any characters not \w- in the original filename
$pathinfo = pathinfo($_FILES["image"]["name"]);

$base = $pathinfo["filename"];

$base = preg_replace("/[^\w-]/", "_", $base);

$filename = $base . "." . $pathinfo["extension"];

$destination = __DIR__ . "/uploads/" . $filename;

// Add a numeric suffix if the file already exists
$i = 1;

while (file_exists($destination)) {

    $filename = $base . "($i)." . $pathinfo["extension"];
    $destination = __DIR__ . "/uploads/" . $filename;

    $i++;
}

if ( ! move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {

    exit("Can't move uploaded file");

}

echo "File uploaded successfully.";


abstract class File {
    protected array $allowedExtensions = [];
    protected array $allowedMimeTypes = []; 
    protected int $maxSize = 1024 * 1024 * 5;
    protected int $size;
    protected int $error;
    protected string $name; 
    protected string $tmpName; 
    protected string $mimeType;
    protected string $extension;

    public function __construct(array $file)
    {
        $this->name = $this->generateSafeFilename($file['name']); 
        $this->extension = $this->getExtension($file['name']);
        $this->tmpName = $file['tmp_name'];
        $this->error = $file['error'];
        $this->mimeType = $file['mimetype'];
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'tmp_name' => $this->tmpName,
            'size' => $this->size,
            'extension' => $this->extension,
            'mime_type' => $this->mimeType,
        ];
    }

    public function validate()
    {

    }

    public function has($key)
    {
        return !$this->validateEmpty() && isset($_FILES[$key]);
    }

    public function valid($key)
    {
        return $this->has($key) && $this->validateSize();
    }

    protected function validateError(): bool
    {
        return $this->error !== UPLOAD_ERR_OK;
    }

    protected function validateSize(): bool
    {
        return $this->size['size'] > $this->maxSize;
    }

    protected function validateEmpty(): bool
    {
        return empty($_FILES);
    }   

    protected function validateExtensionAndMime(): bool
    {
        return !in_array($this->extension, $this->allowedExtensions, true) 
            || !in_array($this->mimeType, $this->allowedMimeTypes, true);
    }

    protected function getExtension(string $originName): string
    {
        return strtolower(pathinfo($originName, PATHINFO_EXTENSION));
    }

    protected function generateSafeFilename(string $originName): string
    {
        $pathinfo = pathinfo($originName);
        $base = preg_replace('/[^\w-]/', '_', $pathinfo['filename']); 
        $ext = $pathinfo['extension'];

        return $base . '.' . $ext; 
    }

}

/*
<?php

namespace app\services;

class ImageUploadService
{
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png'];
    protected array $allowedMimeTypes = ['image/jpeg', 'image/png'];
    protected int $maxSize = 1048576; // 1MB
    protected string $uploadDir = __DIR__ . '/../../public/uploads';

    public function upload(array $file): string
    {
        $this->checkError($file);
        $this->validateSize($file);
        $this->validateExtensionAndMime($file);

        $filename = $this->generateSafeFilename($file['name']);
        $destination = $this->getDestinationPath($filename);

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception("파일을 저장하는 데 실패했습니다.");
        }

        return basename($destination);
    }

    protected function checkError(array $file): void
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('파일 업로드 오류 발생 (코드: ' . ($file['error'] ?? 'unknown') . ')');
        }
    }

    protected function validateSize(array $file): void
    {
        if ($file['size'] > $this->maxSize) {
            throw new \Exception('파일 크기가 너무 큽니다. 최대 1MB까지 허용됩니다.');
        }
    }

    protected function validateExtensionAndMime(array $file): void
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($ext, $this->allowedExtensions, true) || !in_array($mime, $this->allowedMimeTypes, true)) {
            throw new \Exception('허용되지 않는 파일 형식입니다.');
        }
    }

    protected function generateSafeFilename(string $originalName): string
    {
        $pathinfo = pathinfo($originalName);
        $base = preg_replace('/[^\w-]/', '_', $pathinfo['filename']);
        $ext = $pathinfo['extension'] ?? 'jpg';

        return $base . '.' . $ext;
    }

    protected function getDestinationPath(string $filename): string
    {
        $destination = rtrim($this->uploadDir, '/') . '/' . $filename;
        $i = 1;

        while (file_exists($destination)) {
            $destination = rtrim($this->uploadDir, '/') . "/{$filename}({$i})." . pathinfo($filename, PATHINFO_EXTENSION);
            $i++;
        }

        return $destination;
    }
}

*/
// class File
// {
//     private string $name;
//     private string $tmpName;
//     private string $extension;
//     private int $size;
//     private int $error;
//     private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
//     private array $errors = [];

//     public function __construct(array $file)
//     {
//         $this->name = $this->sanitizeFileName($file['name']);
//         $this->tmpName = $file['tmp_name'];
//         $this->extension = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
//         $this->size = $file['size'];
//         $this->error = $file['error'];
//         $this->errors = [];
//     }

//     public function getErrors(): array
//     {
//         return $this->errors;
//     }

//     public function getData(): array
//     {
//         return [
//             'name' => $this->name,
//             'tmp_name' => $this->tmpName,
//             'size' => $this->size,
//             'extension' => $this->extension
//         ];
//     }

//     public function checkMethod()
//     {
//         if (App::$app->request->getOriginMethod() !== 'post') {
//             ApiResponse::methodNotAllowed('POST request method required');
//         }
        
//         if (empty($_FILES)) {
//             ApiResponse::badRequest('$_FILES is empty - is file_uploads set to "Off" in php.ini?');
//         }
//     }

//     public function checkError()
//     {
//         if ($this->error === UPLOAD_ERR_OK) {
//             return true;
//         }

//         switch ($this->error) {
//             case UPLOAD_ERR_PARTIAL:
//                 ApiResponse::badRequest('File only partially uploaded');
//                 break;
//             case UPLOAD_ERR_FORM_SIZE:
//                 ApiResponse::badRequest('File exceeds MAX_FILE_SIZE in the HTML form');
//                 break;
//             case UPLOAD_ERR_NO_FILE:
//                 ApiResponse::badRequest('No file was uploaded');
//                 break;
//             case UPLOAD_ERR_EXTENSION:
//                 ApiResponse::serverError('File upload stopped by a PHP extension');
//                 break;
//             case UPLOAD_ERR_INI_SIZE:
//                 ApiResponse::serverError('File exceeds upload_max_filesize in php.ini');
//                 break;
//             case UPLOAD_ERR_NO_TMP_DIR:
//                 ApiResponse::serverError('Temporary folder not found');
//                 break;
//             case UPLOAD_ERR_CANT_WRITE:
//                 ApiResponse::serverError('Failed to write file');
//                 break;
//             default:
//                 ApiResponse::serverError('Unknown upload error');
//                 break;
//         }
//     }

//     private function sanitizeFileName(string $filename): string
//     {
//         return preg_replace('/[^\w\.-]+/', '_', $filename);
//     }

//     public function checkSize(int $maxSize = 1024 * 1024 * 10): bool
//     {
//         return $this->size <= $maxSize;
//     }

//     public function checkExtension(): bool
//     {
//         return in_array($this->extension, $this->allowedExtensions, true);
//     }

//     public function upload(string $destination): bool
//     {
//         if ($this->error !== UPLOAD_ERR_OK) {
//             return false;
//         }

//         $uniqueName = uniqid('', true) . '.' . $this->extension;
//         $targetPath = rtrim($destination, '/') . '/' . $uniqueName;

//         return move_uploaded_file($this->tmpName, $targetPath);
//     }
// }
// ?>
