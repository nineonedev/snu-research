<?php

namespace app\core;

class Response
{
    protected string $content = '';
    protected int $statusCode = 200;
    protected array $headers = [];

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($statusCode);
        $this->headers = $headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
        exit;
    }

    public static function html(string $html, int $status = 200): self
    {
        return new self($html, $status, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    public static function json($data, int $status = 200): self
    {
        return new self(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $status, [
            'Content-Type' => 'application/json',
        ]);
    }

    public static function redirect(string $url, int $status = 302): self
    {
        return new self('', $status, [
            'Location' => $url,
        ]);
    }

    public static function download(string $filePath, string $filename = ''): self
    {
        if (!file_exists($filePath)) {
            return static::html("File not found", 404);
        }

        $filename = $filename ?: basename($filePath);
        $content = file_get_contents($filePath);

        return new self($content, 200, [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public static function alert(string $message, string $redirectTo = '/'): void
    {
        $msg = htmlspecialchars($message, ENT_QUOTES);
        $url = htmlspecialchars($redirectTo, ENT_QUOTES);
        echo "<script>alert('$msg'); window.location.href='$url';</script>";
        exit;
    }

    public static function back(): void
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        static::redirect($url)->send();
    }

    public static function backWithInput(array $input): void
    {
        Session::set('_old_input', $input);
        static::back();
    }

    public static function backWithErrors(array $errors): void
    {
        Session::set('_errors', $errors);
        static::back();
    }

    // Accessors
    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
