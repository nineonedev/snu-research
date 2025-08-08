<?php 

function format_filesize(int $bytes): string {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' bytes';
}

function MB(int $mb): int 
{
    return $mb * 1024 * 1024;
}

function GB(int $gb): int 
{
    return MB($gb) * 1024;
}

function array_get(string $key, array $data)
{
    return array_key_exists($key, $data) ? $data[$key] : null;
}

function dd($var, $exit = true)
{
    echo '<pre>';
    var_dump($var); 
    echo '</pre>';

    if($exit) {
        exit;
    }
}

if (!function_exists('rm_space')) {
    function rm_space(array $arr): array {
        return array_map(function($item){ return trim($item); }, $arr);
    }
}

function get_class_name($className, $toLowercase = true)
{
    $className = str_replace('\\', '/', $className);
    $baseName = basename($className) ; 
    return ($toLowercase ? strtolower($baseName) : $baseName);
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }
}


if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}
