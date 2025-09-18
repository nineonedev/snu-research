<?php

use app\core\Config;
use app\facades\DB;

$locale = Config::get('locale');
$mainBanners = DB::table('no_banners')
    ->where('is_hidden', '=', 0)
    ->orderBy('display_order', 'desc')
    ->orderBy('created_at', 'desc')
    ->get();

section('content')

?>

<section class="no-visual no-pd-40--t">
    <div class="no-container-xl">
        <div class="no-main-slider">
            <ul class="swiper-wrapper">
                <?php foreach ($mainBanners as $banner) : 
                    $lang = DB::table('no_banner_langs')
                        ->where('banner_id', '=', $banner['id'])
                        ->where('locale', '=', $locale)
                        ->first();    
                ?>
                <li class="swiper-slide">
                    <a href="<?= $lang['link'] ? $lang['link'] : '#'?>">
                        <figure>
                            <img src="<?=UPLOAD_URL.$banner['image']?>" alt="">
                        </figure>
                        <h2 class="no-visual__title no-heading-lg"><?= $lang ? $lang['title'] : ''?></h2>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="swiper-controller">
                <div class="group">
                    <div class="swiper-button-prev arrow"><i class="fa-sharp fa-light fa-arrow-right fa-rotate-180"></i></div>
                    <div class="swiper-button-next arrow"><i class="fa-sharp fa-light fa-arrow-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

    // 1. search_key가 CONF, COLL인 게시판 목록
    $boards = DB::table('no_boards')
    ->whereIn('search_key', ['CONF', 'COLL'])
    ->get();

    // 2. 게시판 ID 배열
    $boardIds = array_column($boards, 'id');

    // 3. 게시판별 언어와 카테고리명 세팅
    $boardInfoMap = [];
    foreach ($boards as $board) {
    $lang = DB::table('no_board_langs')
        ->where('board_id', '=', $board['id'])
        ->where('locale', '=', $locale)
        ->first();

    $boardInfoMap[$board['id']] = [
        'lang' => $lang,
        'category' => $lang['name'] ?? '카테고리 없음'
    ];
    }

    // 4. 게시글 가져오기
    $conferences = DB::table('no_posts')
    ->whereIn('board_id', $boardIds)
    ->orderBy('is_notice', 'desc')
    ->orderBy('created_at', 'desc')
    ->limit(4)
    ->get();

    // 5. 게시글에 lang, category 정보 매핑
    foreach ($conferences as &$post) {
    $post['lang'] = DB::table('no_post_langs')
        ->where('post_id', '=', $post['id'])
        ->where('locale', '=', $locale)
        ->first();

    $boardInfo = $boardInfoMap[$post['board_id']] ?? [];
    $post['board_lang'] = $boardInfo['lang'] ?? null;
    $post['category'] = $boardInfo['category'] ?? '카테고리 없음';
    }

    $mainConference = $conferences[0];
    array_shift($conferences);
	
	if ($mainConference){
		$content = $mainConference['lang']['content'] ?? '';
		$limit   = 100; // 원하는 글자수 제한

		// 멀티바이트 문자열 길이 확인
		$plainText = strip_tags($content);

		if (mb_strlen($plainText, 'UTF-8') > $limit) {
			$shortContent = mb_substr($plainText, 0, $limit, 'UTF-8') . '...';
		} else {
			$shortContent = $plainText;
		}
	}

?>
<section class="no-event no-pd-64--y" <?= AOS_FADE_UP ?>>
    <div class="no-container-xl">
        <div class="section-title">
            <h2 class="no-base-stitle">UPCOMING EVENTS</h2>
        </div>

        <ul class="event-list no-mg-40--t">
            <li class="no-mg-20--r important">
                <a href="/activities/<?=$mainConference['board_id']?>/post/<?= $mainConference['id'] ?>">
                    <figure>
                        <img src="<?= UPLOAD_URL . '/' . $mainConference['image'] ?>">
                        <p class="category no-body-sm"><?= $mainConference['category'] ?? '' ?></p>
                    </figure>

                    <div class="txt no-mg-16--t">
                        <h3 class="no-heading-md"><?= nl2br(htmlspecialchars($mainConference['lang']['title'] ?? '제목 없음')) ?></h3>
                        <div class="no-mg-16--t no-pd-16--b no-body-md"><?= $shortContent ?? '' ?></div>
                        <div class="no-body-sm"><?= formatDate($mainConference['created_at']) ?></div>
                    </div>
                </a>
            </li>

            <div class="group">
                <?php foreach ($conferences as $conf): ?>
                    <li>
                        <a href="/activities/<?=$conf['board_id']?>/post/<?= $conf['id'] ?>">
                            <div class="txt">
                                <p class="category no-body-sm no-mg-8--b"><?= $conf['category'] ?? '' ?></p>
                                <h3 class="no-base-ctitle no-mg-16--b"><?= htmlspecialchars($conf['lang']['title'] ?? '제목 없음') ?></h3>
                                <div class="no-body-sm"><?= formatDate($conf['created_at']) ?></div>
                            </div>
                            <figure>
                                <img src="<?= UPLOAD_URL . '/' . $conf['image'] ?>">
                            </figure>
                        </a>
                    </li>
                <?php endforeach; ?>
            </div>
        </ul>
    </div>
</section>


<section class="no-center-banner imgbg no-pd-64--b" <?= AOS_FADE_UP ?>>
    <div class="no-container-xl">
        <div class="banner-box">
            <figure class="move-img"><img src="<?=img('center-banner.jpg')?>"></figure>
            <hgroup>
                <h2 class="no-heading-md no-mg-24--b"><?=lang('main.intro.title')?></h2>
                <p class="no-body-lg no-mg-32--b">
                    <?=lang('main.intro.desc')?>
                </p>

                <a href="<?=web_path('institute/intro')?>" class="base-btn no-body-sm">
                    LEARN MORE
                </a>
            </hgroup>
        </div>
    </div>
</section>

<?php
    // 1. 팀 목록 가져오기
    $teams = DB::table('no_teams')->get();
    $teamIds = array_column($teams, 'id');

    // 2. 팀 언어명 가져오기
    $teamLangs = DB::table('no_team_langs')
        ->whereIn('team_id', $teamIds)
        ->where('locale', '=', $locale)
        ->get();

    $teamLangMap = [];
    foreach ($teamLangs as $lang) {
        $teamLangMap[$lang['team_id']] = $lang['name'];
    }

    // 3. 팀별 board 중 search_key = ACT인 것만 추출
    $boards = DB::table('no_boards')
        ->where('search_key', '=', 'ACT')
        ->whereIn('team_id', $teamIds)
        ->get();

    $boardsByTeam = [];
    foreach ($boards as $board) {
        $boardsByTeam[$board['team_id']][] = $board['id'];
    }

    // 4. 각 팀별 게시글 4개씩 가져오기 + 다국어
    $teamPosts = [];
    foreach ($boardsByTeam as $teamId => $boardIds) {
        $posts = DB::table('no_posts')
            ->whereIn('board_id', $boardIds)
            ->orderBy('is_notice', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        foreach ($posts as &$post) {
            $post['lang'] = DB::table('no_post_langs')
                ->where('post_id', '=', $post['id'])
                ->where('locale', '=', $locale)
                ->first();
        }
        unset($post); // 참조해제

        $teamPosts[] = [
            'team_id' => $teamId,
            'team_name' => $teamLangMap[$teamId] ?? '이름 없음',
            'posts' => $posts
        ];
    }

?>
<section class="no-research no-pd-64--y" <?= AOS_FADE_UP ?>>
    <div class="no-container-xl">
        <div class="section-title">
            <h2 class="no-base-stitle">FEATURED RESEARCH</h2>
        </div>

        <div class="research-wrap f-wrap no-mg-40--t">
            <div class="category-nav-wrap">
                <div class="category-nav">
                    <ul class="swiper-wrapper">
                        <?php foreach ($teamPosts as $index => $team): ?>
                        <li class="swiper-slide <?= $index === 0 ? 'active' : '' ?>">
                            <a href="#" class="no-body-md"><?= htmlspecialchars($team['team_name']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="<?=web_path('teams')?>" class="base-btn no-mg-40--t no-body-sm">VIEW ALL</a>
            </div>

            <div class="no-tab-contents">
                <?php foreach ($teamPosts as $index => $team): ?>
                    <div>
                        <ul class="research-list <?= $index === 0 ? 'active' : '' ?>">
                            <?php
                                $posts = $team['posts'];
                                $first = $posts[0] ?? null;
                                $rest = array_slice($posts, 1);

                                $link = web_path("teams/{$team['team_id']}/board/{$first['board_id']}/post/{$first['id']}");


                            ?>

                            <?php if ($first): ?>
                            <li class="important">
                                <a href="<?=$link?>">
                                    <figure><img src="<?= $first['image'] ? UPLOAD_URL.'/'.$first['image'] : img('default.jpg') ?>"></figure>
                                    <div class="txt">
                                        <h3 class="no-heading-sm no-mg-16--b"><?= htmlspecialchars($first['lang']['title'] ?? '제목 없음') ?></h3>
                                        <p class="no-body-md no-mg-8--b"><?= mb_strimwidth(strip_tags($first['lang']['content'] ?? ''), 0, 100, '...') ?></p>
                                        <span class="no-body-sm"><?= formatDate($first['created_at']) ?></span>
                                    </div>
                                </a>
                            </li>
                            <?php endif; ?>

                            <div class="group no-mg-40--t">
                                <?php foreach ($rest as $post): 
                                        $link = web_path("teams/{$team['team_id']}/board/{$post['board_id']}/post/{$post['id']}");
                                ?>
                                <li>
                                    <a href="<?=$link?>">
                                        <figure><img src="<?= $post['image'] ? UPLOAD_URL.'/'.$post['image'] : img('default.jpg') ?>"></figure>
                                        <div class="txt">
                                            <h3 class="no-base-mtitle no-mg-16--y"><?= htmlspecialchars($post['lang']['title'] ?? '제목 없음') ?></h3>
                                            <span class="no-body-sm"><?= formatDate($post['created_at']) ?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </div>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php
$boardKeys = ['NEWS', 'VIDEO', 'PUB'];
$boards = DB::table('no_boards')
    ->whereIn('search_key', $boardKeys)
    ->get();

$boardMap = [];
foreach ($boards as $board) {
    $boardLang = DB::table('no_board_langs')
        ->where('board_id', '=', $board['id'])
        ->where('locale', '=', $locale)
        ->first();

    $posts = DB::table('no_posts')
        ->where('board_id', '=', $board['id'])
        ->orderBy('is_notice', 'desc')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    foreach ($posts as &$post) {
        $post['lang'] = DB::table('no_post_langs')
            ->where('post_id', '=', $post['id'])
            ->where('locale', '=', $locale)
            ->first();
    }
    unset($post); // 참조해제

    $boardMap[$board['search_key']] = [
        'id' => $board['id'],
        'title' => $boardLang['name'] ?? '제목 없음',
        'posts' => $posts
    ];

}
?>

<section class="no-boards no-pd-64--y" <?= AOS_FADE_UP ?>>
    <div class="no-container-xl">
        <ul class="board-list">
            <?php foreach ([
                'NEWS' => 'News & Updates',
                'VIDEO' => 'Video Archive',
                'PUB' => 'Publications'
            ] as $key => $defaultTitle): ?>
            <?php $board = $boardMap[$key] ?? null; ?>
            <?php if ($board): ?>
            <li>
                <div class="f-wrap">
                    <div class="section-title no-mg-40--b">
                        <h2 class="no-base-stitle"><?= htmlspecialchars($board['title']) ?></h2>
                    </div>
                    <a href="<?=web_path("activities/{$board['id']}")?>" class="base-btn no-body-sm">VIEW ALL</a>
                </div>

                <ul class="list-wrap <?= $key === 'PUB' ? 'publication' : '' ?>">
                    <?php foreach ($board['posts'] as $post): ?>
                        <?php $lang = $post['lang']; ?>
                        <li>
                            <a href="<?= $key === 'VIDEO' ? $post['link_url'] : web_path("activities/{$post['board_id']}/post/{$post['id']}") ?>" <?= $key === 'VIDEO' ? 'target="_blank"' : '' ?>>
                                <?php if ($key === 'VIDEO'): ?>
                                    <figure class="no-vid-img">
                                        <img src="<?= $post['image'] ? UPLOAD_URL .'/'. $post['image'] : img('default.jpg') ?>">
                                        <img src="<?=img('youtube.svg')?>" alt="" class="icon">
                                    </figure>
                                <?php elseif ($key === 'PUB'): ?>
                                    <figure>
                                        <img src="<?= $post['image'] ? UPLOAD_URL .'/'. $post['image'] : img('default.jpg') ?>">
                                    </figure>
                                <?php endif; ?>

                                <div class="txt">
                                    <h3 class="no-base-mtitle"><?= nl2br(htmlspecialchars($lang['title'] ?? '제목 없음')) ?></h3>
                                    <?php if ($key === 'NEWS'): ?>
                                        <p class="no-body-sm"><?= htmlspecialchars(mb_strimwidth(strip_tags($lang['content'] ?? ''), 0, 80, '...')) ?></p>
                                    <?php endif; ?>
                                    <span class="no-body-sm"><?= formatDate($post['created_at']) ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endSection() ?>

<?php section('script') ?>
<script>
    const catgItems = $('.category-nav li');
    const contents = $('.no-tab-contents > div');

    catgItems.each((idx, item) => {
        $(item).click(function(e){
            e.preventDefault(); 

            $(catgItems).each((i, t) => {
                $(t).removeClass('active');
            })

            $(this).addClass('active'); 
            contents.hide(); 
            contents.eq(idx).show(); 
        })
    })

    catgItems.eq(0).trigger('click');

</script>

<?php endSection() ?>