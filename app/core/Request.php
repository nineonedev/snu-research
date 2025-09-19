<?php

namespace app\core;

use app\core\Csrf;

class Request
{
    const METHOD_GET     = 'get';
    const METHOD_POST    = 'post';
    const METHOD_PUT     = 'put';
    const METHOD_PATCH   = 'patch';
    const METHOD_DELETE  = 'delete';
    const SPOOF_FIELD    = '_method';

    protected array $query;
    protected array $body;
    protected array $files;
    protected string $method;
    protected string $path;

    public function __construct()
    {
        $this->query = $_GET ?? [];
        $this->files = $_FILES ?? [];
        $this->path = $this->resolvePath();
        $this->method = $this->resolveMethod();
        $this->body = $this->resolveBody();
    }

    protected function resolveMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET);

        if ($method === self::METHOD_POST) {
            $spoofed = strtolower(trim($_POST[self::SPOOF_FIELD] ?? ''));
            if (in_array($spoofed, [self::METHOD_PUT, self::METHOD_PATCH, self::METHOD_DELETE], true)) {
                return $spoofed;
            }
        }

        return $method;
    }

    protected function resolvePath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = explode('?', $uri)[0];
        return '/' . trim($path, '/');
    }

    protected function resolveBody(): array
    {
        if ($this->isJson()) {
            $json = json_decode(file_get_contents('php://input'), true);
            return is_array($json) ? $json : [];
        }

        return $_POST ?? [];
    }

    public function param(string $key)
    {
        return $this->query[$key] ?? null;
    }
    
    public function query(): array
    {
        return $this->query; 
    }

    public function get(string $key, $defualt)
    {
        return $this->query[$key] ?? $defualt;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }


    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
    
    public function set(string $key, $value): void
    {
        $this->body[$key] = $value;
    }
    
    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        return array_filter($this->all(), fn($k) => in_array($k, $keys), ARRAY_FILTER_USE_KEY);
    }

    public function except(array $keys): array
    {
        return array_filter($this->all(), fn($k) => !in_array($k, $keys), ARRAY_FILTER_USE_KEY);
    }

    public function file(string $key): ?UploadedFile
    {
        if (!isset($_FILES[$key])) {
            return null;
        }
    
        return new UploadedFile($_FILES[$key]);
    }

    public function isGet(): bool
    {
        return $this->method === self::METHOD_GET;
    }

    public function isPost(): bool
    {
        return $this->method === self::METHOD_POST;
    }

    public function isPut(): bool
    {
        return $this->method === self::METHOD_PUT;
    }

    public function isPatch(): bool
    {
        return $this->method === self::METHOD_PATCH;
    }

    public function isDelete(): bool
    {
        return $this->method === self::METHOD_DELETE;
    }

    public function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    public function verifyCsrf(): bool
    {
        if ($this->isGet()) return true;

        $token = $this->input(Csrf::FIELD);
        return Csrf::verifyToken($token);
    }
}
