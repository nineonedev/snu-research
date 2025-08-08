<?php 

namespace app\core;

use app\facades\App;
use app\facades\Rule;
use app\lib\rules\FileMultipleRule;
use Exception;
use finfo;

class File {
    private array $validatingErrors;
    private string $key;
    private string $originName; 
    private string $hashedName; 
    private string $tmpName; 
    private int $size; 
    private int $error;
    private string $extension;
    private string $mimeType;
    private $rules;

    public function __construct(string $key, array $file, $rules = null)
    {
        $this->key = $key;
        $this->rules = $rules ?? 'file';
        $this->validatingErrors = [];

        $this->originName = $this->generateSafeName($file['name']);
        $this->hashedName = $this->generateHashedName($this->originName);
        $this->tmpName = $file['tmp_name'];
        $this->mimeType = $this->detectMimeType($this->tmpName);
        $this->extension =  $this->detectExtension($this->originName);
        $this->error = $file['error']; 
        $this->size = $file['size']; 
    }

    public static function isMissingUpload($file): bool
    {
        return is_null($file) 
            || empty($file) 
            || (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE);
    }


    public static function has(string $key): bool
    {
        return !self::isMissingUpload(App::request()->file($key));
    }

    public static function get(string $key): ?array
    {
        return App::request()->file($key);
    }

    public static function make(string $key, $rules = []): ?File
    {
        $file = self::get($key);

        if (self::isMissingUpload($file)) {
            return null;
        }

        return new self($key, $file, $rules);
    }

    public function getErrors(): array
    {
        return $this->validatingErrors;
    }

    public function validate(): bool
    {
        $this->validatingErrors = [];

        if (!$this->rules) return false;

        $validator = Validator::make(
            [$this->key => $this->getData()], 
            [$this->key => $this->rules]
        );

        $rules = $validator->getParser()->getRules()[$this->key]; 


        foreach ($rules as $rule) {
            if (!($rule instanceof FileMultipleRule)) {
                throw new Exception('rule must be file rule');
            }
        }

        $success = $validator->execute();
        
        if (!$success) {
            $this->validatingErrors = $validator->getErrors();
        }
        
        return $success;
    }

    public function getData(): array
    {
        return [
            'org_name'  => $this->originName,
            'name'      => $this->hashedName, 
            'tmp_name'  => $this->tmpName,
            'extension' => $this->extension, 
            'mime_type' => $this->mimeType,
            'size'      => $this->size,
            'error'     => $this->error
        ];
    }

    public function getHashedName(): string
    {
        return $this->hashedName;
    }

    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getSize(): int
    {
        return $this->size; 
    }

    public function getError(): int
    {
        return $this->error; 
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    protected function generateHashedName(string $originalName): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        return bin2hex(random_bytes(8)) . '.' . $ext;
    }

    protected function generateSafeName(string $originName): string
    {
        $pathinfo = pathinfo($originName);
        $base = preg_replace('/[^\w-]/', '_', $pathinfo['filename']); 
        $ext = $pathinfo['extension'];

        return $base . '.' . $ext; 
    }

    protected function detectExtension(string $originName): string 
    {
        return strtolower(pathinfo($originName, PATHINFO_EXTENSION));
    }

    protected function detectMimeType(string $tmpName): string 
    {
        if (!is_file($tmpName)) return ''; 

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($tmpName) ?: '';
    }

}


