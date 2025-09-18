<?php

use app\core\Config;
use app\facades\DB;

$defaultLocale = Config::get('default_locale');
$locale = Config::get('locale');
$basePath = $locale === $defaultLocale ? '/' : "/$locale/";

$menus = [];
// 1. 현대한국종합연구단
$menus[] = [
    'title' => lang('menu.institute'), // 예: "현대한국종합연구단"
    'path' => $basePath . 'institute/intro',
    'pages' => [
        [
            'title' => lang('menu.institute.intro'),   // 예: "소개"
            'path' => $basePath . 'institute/intro',
        ],
        [
            'title' => lang('menu.institute.greeting'), // 예: "인사말"
            'path' => $basePath . 'institute/greeting',
        ],
    ]
];

// 2. 연구팀
$teams = DB::table('no_teams')->get();
$teamMenu = [
    'title' => lang('menu.teams'), // 예: "연구팀"
    'path' => $basePath . 'teams',
    'pages' => []
];

foreach ($teams as $team) {
    $teamLang = DB::table('no_team_langs')
        ->where('team_id', '=', $team['id'])
        ->where('locale', '=', $locale)
        ->first();
    $teamName = $teamLang['name'] ?? '이름 없음';

    $boards = DB::table('no_boards')
        ->where('team_id', '=', $team['id'])
        ->get();

    $subPages = [];

    foreach ($boards as $board) {
        $boardLang = DB::table('no_board_langs')
            ->where('board_id', '=', $board['id'])
            ->where('locale', '=', $locale)
            ->first();
        $boardName = $boardLang['name'] ?? '이름 없음';

        $subPages[] = [
            'title' => $boardName,
            'path' => $basePath . "teams/{$team['id']}/board/{$board['id']}"
        ];
    }

    $teamMenu['pages'][] = [
        'title' => $teamName,
        'path' => $basePath . "teams/{$team['id']}",
        'pages' => $subPages
    ];
}
$menus[] = $teamMenu;

// 3. 활동 (CONF, COLL, PUB)
$searchKeys = ['CONF', 'VIDEO', 'COLL', 'PUB'];
$activityMenu = [
    'title' => lang('menu.activities'),
    'path' => '#',
    'pages' => []
];

foreach ($searchKeys as $key) {
    $boards = DB::table('no_boards')
        ->where('is_public', '=', 1)
        ->whereNull('team_id')
        ->where('search_key', '=', $key)
        ->get();

    foreach ($boards as $board) {
        $boardLang = DB::table('no_board_langs')
            ->where('board_id', '=', $board['id'])
            ->where('locale', '=', $locale)
            ->first();

        $activityMenu['pages'][] = [
            'title' => $boardLang['name'] ?? '이름 없음',
            'path' => $basePath . "activities/{$board['id']}"
        ];
    }
}
// usort($activityMenu['pages'], function($a, $b) {
//     return strcmp($a['title'], $b['title']);
// });
$menus[] = $activityMenu;

// 4. 소식 (NEWS)
$newsBoard = DB::table('no_boards')
    ->where('is_public', '=', 1)
    ->whereNull('team_id')
    ->where('search_key', '=', 'NEWS')
    ->first();

if ($newsBoard) {
    $newsLang = DB::table('no_board_langs')
        ->where('board_id', '=', $newsBoard['id'])
        ->where('locale', '=', $locale)
        ->first();

    $menus[] = [
        'title' => $newsLang['name'] ?? '소식',
        'path' => $basePath . "activities/{$newsBoard['id']}"
    ];
}

define('MENU_ITEMS', $menus);
Config::set('menus', $menus);

// ==================================================
// Locale 
// ==================================================

// === Build locale-switched URLs for the current page ===
$locales        = array_keys(Config::get('locales'));          // ['ko','en'] 등
$defaultLocale  = Config::get('default_locale');               // 예: 'ko'
$currentLocale  = Config::get('locale');

$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$parsed = parse_url($uri);
$path   = $parsed['path']  ?? '/';
$query  = isset($parsed['query']) ? ('?' . $parsed['query']) : '';

// 현재 path에서 (있다면) locale 프리픽스를 제거
$segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
if (!empty($segments) && in_array($segments[0], $locales, true)) {
	array_shift($segments);
}
$restPath = implode('/', $segments);                           // locale 제외한 나머지 경로
$restPath = $restPath === '' ? '' : '/' . $restPath;

// 타겟 locale로 링크 생성
$toLocalePath = function (string $target) use ($defaultLocale, $restPath, $query) {
	if ($target === $defaultLocale) {
		// 기본 언어는 프리픽스 없이
		$p = $restPath === '' ? '/' : $restPath;
	} else {
		// 비기본 언어는 /{locale}/ + 나머지 경로
		$p = '/' . $target . ($restPath === '' ? '/' : $restPath);
	}
	return $p . $query;
};

?>
    <header>
        <div class="no-header">

            <div class="search-box">
                <div class="no-container-xl">
                    <form method="get" action="<?=web_path('search')?>" role="search" aria-label="Site-wide">
                        <div class="input-box">
                            <input name="query" id="search-box" class="query no-body-xl" type="search" placeholder="<?=lang('search.placeholder')?>">
                            <button class="submit search-submit" type="submit" id="search-submit">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="no-container-xl">

                <h1 class="no-header__logo">
                    <a href="<?=$basePath?>">
                        <img src="<?=img('color-logo.svg')?>" alt="서울대학교 현대한국종합연구단" class="color" />
                        <img src="<?=img('white-logo.svg')?>" alt="서울대학교 현대한국종합연구단" class="white" />
                    </a>
                </h1>

                <?php

                if (count(MENU_ITEMS) > 0) : ?>
                    <nav class="no-header__menu">
                        <!-- depth1 -->
                        <ul class="no-header__menu--gnb">
                            <?php foreach (MENU_ITEMS as $di => $depth):
                                $depth_active = $depth['isActive'] ? 'active' : '';
                            ?>
                                <li>
                                    <a href="<?= $depth['path'] ?>" class="<?= $depth_active ?> no-base-menu">
                                        <?= $depth['title'] ?>
                                        <i class="fa-sharp fa-light fa-angle-down"></i>
                                    </a>
                                    <?php if (array_key_exists('pages', $depth) && count($depth['pages']) > 0) : 
                                        $col_4_class = $di === 2 ? '--4' : '';   
                                    ?>
                                        <ul class="no-header__menu--lnb <?=$col_4_class?>">
                                            <?php foreach ($depth['pages'] as $pi => $PAGE):
                                                $page_active = $PAGE['isActive'] ? 'active' : '';
                                            ?>
                                                <li class="<?= $page_active ?>">
                                                    <a href="<?= $PAGE['path'] ?>" class="no-base-smenu">
                                                        <?= $PAGE['title'] ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </li>
                                <!-- menu -->
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                <!--HeaderBtn-->

                <div class="no-header__opt">
                    <div class="lang-wrap">
						<?php foreach (Config::get('locales') as $key => $label): 
							$active = ($key === $currentLocale) ? 'active' : '';
						?>
							<a href="<?= htmlspecialchars($toLocalePath($key), ENT_QUOTES) ?>" class="<?= $active ?>">
								<?= strtoupper($key) ?>
							</a>
						<?php endforeach; ?>

                    </div>


                    <div class="search-wrap">
                        <p class="no-base-menu">Search</p>
                        <i class="fa-light fa-magnifying-glass"></i>
                    </div>

                    <a class=" no-header__btn">
                        <span class="no-header__btn-line-top"></span>
                        <span class="no-header__btn-line-mid"></span>
                        <span class="no-header__btn-line-bottom"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="no-header__m" data-lenis-prevent-wheel>
            <?php $repeated_section_shown = false;

            if (count(MENU_ITEMS) > 0) : ?>
                <nav class="no-header__m-nav">
                    <div class="no-header__m-menu">
                        <?php foreach (MENU_ITEMS as $di => $depth) :
                            $depth_active = $depth['isActive'] ? 'active' : '';
                        ?>
                            <!-- depth1 start -->
                            <ul class="no-header__m--gnb">
                                <li>
                                    <div class="no-header__m--gnb-title">
                                        <a href="<?=$depth['path']?>">
                                            <p><?= $depth['title'] ?></p>
                                            <div class="no-header__m--gnb--arrow">
                                                <i class="fa-regular fa-chevron-down"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <!--depth2 start-->
                                    <?php if (array_key_exists('pages', $depth) && count($depth['pages']) > 0) : ?>
                                        <ul class="no-header__m--lnb">
                                            <?php foreach ($depth['pages'] as $pi => $PAGE) :
                                                $page_active = $PAGE['isActive'] ? 'active' : '';
                                            ?>
                                                <li>
                                                    <a href="<?= $PAGE['path'] ?>"><?= $PAGE['title'] ?></a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <!--depth2 end-->
                                </li>
                            </ul>
                        <?php endforeach; ?>
                    </div>
                </nav>
            <?php endif; ?>


			<div class="no-header__m-lang-wrap">
				<?php foreach (Config::get('locales') as $key => $label): 
					$active = ($key === $currentLocale) ? 'active' : '';
				?>
					<a href="<?= htmlspecialchars($toLocalePath($key), ENT_QUOTES) ?>" class="<?= $active ?>">
						<?= strtoupper($key) ?>
					</a>
				<?php endforeach; ?>
			</div>

        </div>
    </header>

    <div class="search-dimmed"></div>

    <div class="quick_menu">
        <div class="top_btn"><i></i></div>
    </div>