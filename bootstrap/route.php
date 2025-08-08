<?php

use app\facades\Route;

use app\middlewares\AuthMiddleware;

use app\controllers\AdminController;
use app\controllers\AuthController;
use app\controllers\BoardController;
use app\controllers\BoardLangController;
use app\controllers\HomeController;
use app\controllers\PostController;
use app\controllers\TeamController;
use app\controllers\TeamLangController;
use app\controllers\BannerController;
use app\controllers\BannerLangController;
use app\controllers\SettingController;
use app\controllers\PostLangController;
use app\controllers\SettingLangController;
use app\controllers\UploadController;
use app\core\Config;

// 언어 및 기본값 설정
$locales = Config::get('locales');         // 예: ['ko' => 'Korean', 'en' => 'English']
$defaultLocale = Config::get('default_locale');  // 예: 'ko'
$currentLocale = $defaultLocale;
$currentPrefix = ''; // locale prefix

// 현재 URI로부터 locale 판별
foreach ($locales as $locale => $label) {
    $prefix = $locale === $defaultLocale ? '' : $locale;

    if (preg_match("#^/{$prefix}(/|$)#", $_SERVER['REQUEST_URI'])) {
        $currentLocale = $locale;
        $currentPrefix = $prefix;
        break;
    }
}

// locale 및 다국어 파일 설정
Config::set('locale', $currentLocale);

$langFile = ROOT_PATH . DS . 'langs' . DS . "{$currentLocale}.php";
if (file_exists($langFile)) {
    Config::set('lang', require $langFile);
}

// 해당 언어 prefix만 Route 그룹 지정
$prefix = $currentLocale === $defaultLocale ? '' : $currentLocale;

Route::group($prefix, function () {
    // [1] 홈
    Route::get('/', [HomeController::class, 'index'])->name('site.home');

    Route::get('/search', [HomeController::class, 'search'])->name('site.search');

    // [2] 현대한국종합연구단 소개 / 인사말
    Route::get('/institute/intro', [HomeController::class, 'instituteIntro'])->name('site.institute.intro');
    Route::get('/institute/greeting', [HomeController::class, 'instituteGreeting'])->name('site.institute.greeting');

    // [3] 연구팀 목록
    Route::get('/teams', [HomeController::class, 'teamList'])->name('site.teams');

    // [3-1] 팀 상세
    Route::get('/teams/{team}', [HomeController::class, 'teamDetail'])->name('site.teams.detail');

    // [3-2] 팀 게시판
    Route::get('/teams/{team}/board/{board}', [HomeController::class, 'teamBoard'])->name('site.teams.board');

    // [3-3] 게시글 상세
    Route::get('/teams/{team}/board/{board}/post/{id}', [HomeController::class, 'teamPost'])->name('site.teams.board.post');

    // [4] 활동
    // Route::get('/activities', [HomeController::class, 'activityList'])->name('site.activities');
    Route::get('/activities/{board}', [HomeController::class, 'activityBoard'])->name('site.activities.board');
    Route::get('/activities/{board}/post/{id}', [HomeController::class, 'activityPost'])->name('site.activities.board.post');
});

Route::group('auth', function(){
    Route::get('signin', [AuthController::class, 'signin'])->name('auth.signin');
    Route::post('signin', [AuthController::class, 'login']);
    
    Route::get('signup', [AuthController::class, 'signup'])->name('auth.signup');
    Route::post('signup', [AuthController::class, 'register']);

    Route::post('logout', [AuthController::class, 'logout']);
});


Route::group([
    'prefix' => 'admin',
    'middleware' => [AuthMiddleware::class]
], function(){

    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Admin
    Route::get('/admins', [AdminController::class, 'index'])->name('admin.admins.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admin.admins.create');
    Route::get('/admins/edit/{id}', [AdminController::class, 'edit'])->name('admin.admins.edit');
    Route::post('/admins', [AdminController::class, 'store']);
    Route::patch('/admins/{id}', [AdminController::class, 'update']);
    Route::delete('/admins/{id}', [AdminController::class, 'destroy']);

    // Team
    Route::get('/teams', [TeamController::class, 'index'])->name('admin.teams.index');
    Route::get('/teams/create', [TeamController::class, 'create'])->name('admin.teams.create');
    Route::get('/teams/edit/{id}', [TeamController::class, 'edit'])->name('admin.teams.edit');
    
    Route::post('/teams', [TeamController::class, 'store']);
    Route::patch('/teams/{id}', [TeamController::class, 'update']);
    Route::delete('/teams/{id}', [TeamController::class, 'destroy']);

    Route::patch('/teamlangs/{id}', [TeamLangController::class, 'update']);

    // Board
    Route::get('/boards', [BoardController::class, 'index'])->name('admin.boards.index');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('admin.boards.create');
    Route::post('/boards', [BoardController::class, 'store']);
    Route::get('/boards/edit/{id}', [BoardController::class, 'edit'])->name('admin.boards.edit');
    Route::patch('/boards/{id}', [BoardController::class, 'update']);
    Route::delete('/boards/{id}', [BoardController::class, 'destroy']);
    Route::get('/boards/{id}/extras', [BoardController::class, 'getExtras']);

    Route::patch('/boardlangs/{id}', [BoardLangController::class, 'update']);



    // Post
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::post('/posts', [PostController::class, 'store']);
    Route::patch('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::patch('/postlangs/{id}', [PostLangController::class, 'update']);


    // Banner
    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('admin.banners.create');
    Route::get('/banners/edit/{id}', [BannerController::class, 'edit'])->name('admin.banners.edit');
    Route::post('/banners', [BannerController::class, 'store']);
    Route::patch('/banners/{id}', [BannerController::class, 'update']);
    Route::delete('/banners/{id}', [BannerController::class, 'destroy']);
    Route::patch('/bannerlangs/{id}', [BannerLangController::class, 'update']);


    // Setting
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::get('/settings/create', [SettingController::class, 'create'])->name('admin.settings.create'); // 생성폼
    Route::post('/settings', [SettingController::class, 'store']); // 생성처리
    Route::patch('/settings/{id}', [SettingController::class, 'update']); // 수정처리
    Route::patch('/settinglangs/{id}', [SettingLangController::class, 'update']); // 다국어 수정


    Route::post('/uploads/summernote', [UploadController::class, 'summernote']);


});

// Route::debugRoutes();