<?php

use app\core\Config;
use app\core\Csrf;
use app\core\Response;
use app\core\View;
use app\facades\App;
use app\facades\Route;
use app\facades\Rule;

function web_path($path = '')
{
    $defaultLocale = Config::get('default_locale');
    $locale = Config::get('locale');
    return ($locale === $defaultLocale ? '/' : "/$locale/") . $path;
}

function lang($key): ?string
{
    return Config::get('lang')[$key];
}

function rule_message($className): string
{
    // $lang = App::language();
    $lang = 'ko';
    $file = APP_PATH.DS.'lib'.DS.'rules'.DS.'langs'.DS.$lang.'.php';
    if (!file_exists($file)) {
        throw new Exception('메시지를 로드에 실패하였습니다. 언어: '. $lang);
    }

    $messages = include $file;

    if ($messages[$className]) {
        return $messages[$className]; 
    }

    if (!in_array($className, Rule::$map, true)) {
        throw new Exception('규칙 메시지를 로드에 실패하였습니다. : ' . $className);
    }

    $key = array_search($className, Rule::$map, true);

    if (!array_key_exists($key, $messages)) {
        throw new Exception('해당 규칙에 대한 메시지가 존재하지 않습니다. 규칙:' . $key);
    }

    return $messages[$key];
}

function img($path = '')
{
    return IMG_URL.DS.trim($path, DS); 
}

function view(): View
{
    return App::view(); 
}


function section(string $name)
{
    App::view()->startSection($name);
}

function endSection()
{
    App::view()->endSection();
}


function render(string $template, array $data = [], int $statusCode = 200): Response
{
    $content = App::view()->withLayout($template, $data);
    return Response::html($content, $statusCode); 
}


function extend($layout): void 
{
    App::view()->extend($layout); 
}

function yieldSection(string $name) 
{
    App::view()->yield($name);
}

function includeView(string $template, array $data = []) 
{
    App::view()->include($template, $data);
}

function route(string $name, array $params = []): ?string 
{
    return Route::getRouter()->route($name, $params);
}

function csrf_token(): string
{
    return Csrf::input();
}


function formatDate(?string $date, string $format = 'Y-m-d'): string
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}



function isRoute(string $name): string
{
    return Route::currentRouteName() === $name ? 'active' : '';
}

if (!function_exists('isRouteLike')) {
    /**
     * 현재 라우트 이름이 주어진 접두어 중 하나로 시작하는 경우 'active' 반환
     *
     * @param string|array $prefixes
     * @return string
     */
    function isRouteLike($prefixes): string
    {
        $name = Route::currentRouteName();

        if (!$name) {
            return '';
        }

        $prefixes = (array) $prefixes;

        foreach ($prefixes as $prefix) {
            if (strpos($name, $prefix) === 0) {
                return 'active';
            }
        }

        return '';
    }
}

if (!function_exists('route_is')) {
    /**
     * 현재 라우트 이름이 주어진 이름(또는 접두사)과 일치하는지 확인
     *
     * @param string|array $patterns 정확한 이름 또는 접두사 또는 배열
     * @return bool
     */
    function route_is($patterns): bool
    {
        $name = Route::currentRouteName();

        if (!$name) {
            return false;
        }

        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            // 와일드카드 지원 (예: admin.*)
            if (substr($pattern, -1) === '*') {
                $prefix = rtrim($pattern, '*');
                if (strpos($name, $prefix) === 0) {
                    return true;
                }
            }

            // 정확한 매칭
            if ($name === $pattern) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('fixInlineStyles')) {
    /**
     * HTML 콘텐츠의 style 속성 내부 큰따옴표를 작은따옴표로 변경하여 HTML 속성 충돌 방지
     * font-family 값 내부의 큰따옴표를 작은따옴표로 변경
     * 태그 자체의 잘못된 속성도 제거
     *
     * @param string $html HTML 콘텐츠
     * @return string 정리된 HTML 콘텐츠
     */
    function fixInlineStyles(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // 🔥 1단계: 태그 자체의 잘못된 속성 제거 (예: <p " segoe ui", rotboto, "noto san skr">)
        // 태그 이름 뒤에 따옴표로 시작하는 이상한 속성 패턴 제거
        $html = preg_replace(
            '/<([a-z][a-z0-9]*)\s+["\']\s*[^>]*>/i',
            '<$1>',
            $html
        );

        // 태그 내부에 이상한 속성 패턴 제거 (예: " segoe ui", rotboto)
        $html = preg_replace(
            '/\s+["\']\s*[a-z]+\s*["\']?\s*,?\s*[a-z]+[^=]*/i',
            '',
            $html
        );

        // 🔥 2단계: style 속성을 완전히 재구성
        // style 속성 값을 파싱해서 모든 값 내부의 큰따옴표를 작은따옴표로 변경
        $html = preg_replace_callback(
            '/style\s*=\s*["\']([^"\']*)["\']/i',
            function ($matches) {
                $styleValue = $matches[1];
                
                // 깨진 style 속성 정리 (예: " segoe="" ui",="" roboto)
                $styleValue = preg_replace('/["\']\s*[a-z]+\s*=""\s*[^"\']*["\']?\s*,?\s*=/i', '', $styleValue);
                $styleValue = preg_replace('/["\']\s*[a-z]+\s*=""\s*/i', '', $styleValue);
                $styleValue = preg_replace('/=\s*["\']\s*/', '', $styleValue); // ="" 패턴 제거
                $styleValue = preg_replace('/["\']\s*,?\s*["\']/', '', $styleValue); // 따옴표 사이의 쉼표 제거
                
                // CSS 속성:값 쌍을 파싱 (세미콜론으로 분리하되, 따옴표 안의 세미콜론은 무시)
                $declarations = [];
                $parts = [];
                $current = '';
                $inQuotes = false;
                $quoteChar = null;
                
                for ($i = 0; $i < strlen($styleValue); $i++) {
                    $char = $styleValue[$i];
                    if (($char === '"' || $char === "'") && ($i === 0 || $styleValue[$i - 1] !== '\\')) {
                        if (!$inQuotes) {
                            $inQuotes = true;
                            $quoteChar = $char;
                        } elseif ($char === $quoteChar) {
                            $inQuotes = false;
                            $quoteChar = null;
                        }
                        $current .= $char;
                    } elseif ($char === ';' && !$inQuotes) {
                        if (trim($current)) {
                            $parts[] = trim($current);
                        }
                        $current = '';
                    } else {
                        $current .= $char;
                    }
                }
                if (trim($current)) {
                    $parts[] = trim($current);
                }
                
                foreach ($parts as $part) {
                    // 깨진 패턴 제거 (예: font-size:="" 18px)
                    $part = preg_replace('/:\s*=\s*["\']\s*/', ': ', $part);
                    $part = preg_replace('/["\']\s*$/', '', $part); // 끝의 따옴표 제거
                    
                    if (preg_match('/^\s*([^:]+?)\s*:\s*(.+?)\s*$/', $part, $m)) {
                        $prop = trim($m[1]);
                        $value = trim($m[2]);
                        
                        // !important 처리
                        $hasImportant = preg_match('/\s*!important\s*$/i', $value);
                        $value = preg_replace('/\s*!important\s*$/i', '', $value);
                        $value = trim($value);
                        
                        // 값 내부의 큰따옴표를 작은따옴표로 변경
                        $value = str_replace('"', "'", $value);
                        
                        if ($hasImportant) {
                            $value .= ' !important';
                        }
                        
                        $declarations[] = $prop . ': ' . $value;
                    }
                }
                
                $fixedStyle = implode('; ', $declarations);
                return 'style="' . $fixedStyle . '"';
            },
            $html
        );

        // 🔥 3단계: 깨진 따옴표 패턴 제거 (예: "" 또는 '')
        $html = preg_replace('/["\']{2,}/', '', $html);

        return $html;
    }
}