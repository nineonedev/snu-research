<?php

use app\core\Config;

define('ENV_DEVELOPMENT', 'development');
define('ENV_PRODUCTION', 'production');

define('APP_ENV', ENV_PRODUCTION);
define('APP_KEY', 'NINE0000');

define('DEFAULT_LANG', 'ko');

define('ROLE_OWNER', 777);
define('ROLE_ADMIN', 555);
define('ROLE_GUEST', 333);

define('AOS_FADE_UP', 'data-aos="fade-up"  data-aos-once="true"  data-aos-duration="1000"');


Config::set('layouts', [
    '' => 'home', 
    'admin' => 'admin',
    'auth' => 'auth'
]);

Config::set('default_layout', 'home');

Config::set('locales', [
    'ko' => '한국어',
    'en' => 'English',
]);

Config::set('default_locale', 'ko');

Config::set('skins', [
    'vid' => '비디오',
    'itr' => '소개',
    'mbr' => '구성원',
    'bbs' => '리스트',
    'wez' => '웹진', 
    'pub' => '출판',
]);

Config::set('team_boards', [
    [
        'search_key' => 'INTRO',
        'skin' => 'itr',
        'langs' => [
            'ko' => ['name' => '소개'],
            'en' => ['name' => 'Introduction']
        ],
    ],
    [
        'search_key' => 'MEMBER',
        'skin' => 'mbr',
        'langs' => [
            'ko' => ['name' => '구성원'],
            'en' => ['name' => 'Members']
        ],
    ],
    [
        'search_key' => 'ACT',
        'skin' => 'mbr',
        'langs' => [
            'ko' => ['name' => '활동'],
            'en' => ['name' => 'Activities']
        ],
    ],
    [
        'search_key' => 'OUTCOME',
        'skin' => 'wez',
        'langs' => [
            'ko' => ['name' => '연구성과'],
            'en' => ['name' => 'Research Outcomes']
        ],
    ],
    [
        'search_key' => 'RESOURCE',
        'skin' => 'wez',
        'langs' => [
            'ko' => ['name' => '자료실'],
            'en' => ['name' => 'Resources']
        ],
    ],
]);


Config::set('banner_types', [
    'main' => '메인'
]);

