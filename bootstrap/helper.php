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