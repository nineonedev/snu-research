<?php

use app\core\Config;
use app\core\Session;
use app\facades\App;
use app\facades\DB;
use app\models\Team;
use app\models\TeamLang;

    $locale = Config::get('default_locale');
    $teams = Team::all();
    $publicBoards = DB::table('no_boards')
                ->where('is_public', '=', 1)
                ->get();
?>

<aside class="no-admin-drawer">
    <div class="no-admin-drawer-inner">
        <h1 class="no-admin-drawer-logo">
            <img src="<?=img('admin/logo-white.svg')?>" alt="">
        </h1>
        <div class="no-hambuger">
            <button type="button" id="menu-close-btn">
                <i class="fa-regular fa-xmark-large"></i>
            </button>
        </div>
    </div>
    <nav class="no-admin-drawer-nav">
        <div class="no-admin-drawer-group">
            <span class="no-admin-drawer-group__label">게시글 관리</span>
            <ul class="no-admin-drawer-menu">
                <?php if(Session::get('role_id') == ROLE_OWNER) : ?>
                <li class="no-admin-drawer-menu__item <?= isRouteLike(['admin.teams', 'admin.boards']) ? 'active' : '' ?>">
                    <a href="/admin/teams" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-regular fa-flask"></i>
                            <span>테이블 관리</span>
                        </div>
                        <button type="button">
                            <i class="fa-regular fa-chevron-down"></i>
                        </button>
                    </a>
                    
                    <div class="no-admin-drawer-subgroup">
                        <ul class="no-admin-drawer-submenu">
                            <li class="no-admin-drawer-submenu__item <?= isRouteLike('admin.teams') ? 'active' : ''?>">
                                <a href="/admin/teams" class="no-admin-drawer-submenu__link">
                                    <span>연구팀 관리</span></span>
                                </a>
                            </li>
                            <li class="no-admin-drawer-submenu__item <?= isRouteLike('admin.boards') ? 'active' : ''?>">
                                <a href="/admin/boards" class="no-admin-drawer-submenu__link">
                                    <span>게시판 관리</span></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <?php endif; ?>
                <li class="no-admin-drawer-menu__item <?= isRouteLike('admin.posts') && $_GET['team_id'] ? 'active' : '' ?>">
                    <a href="#" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-regular fa-flask"></i>
                            <span>연구팀 게시판</span>
                        </div>
                        <?php if($teams) :?>
                        <button type="button">
                            <i class="fa-regular fa-chevron-down"></i>
                        </button>
                        <?php endif; ?>
                    </a>
                    
                    <div class="no-admin-drawer-subgroup">
                        <ul class="no-admin-drawer-submenu">
                            <?php foreach ($teams as $team) :
                                $data = DB::table('no_team_langs')
                                    ->where('team_id', '=', $team->id)
                                    ->where('locale', '=', Config::get('default_locale'))
                                    ->first();
                                if(Session::get('role_id') != ROLE_OWNER && Session::get('team_id') != $team->id)  continue;
                            
                            ?>
                            <li class="no-admin-drawer-submenu__item <?= isRouteLike('admin.posts') && $_GET['team_id'] == $team->id ? 'active' : ''?>">
                                <a href="/admin/posts/?team_id=<?= $team->id ?>" class="no-admin-drawer-submenu__link">
                                    <span><?= $data['name'] ?? '이름 없음' ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>
                <?php if (Session::get('role_id') == ROLE_OWNER) :?> 
                <li class="no-admin-drawer-menu__item <?= isRouteLike('admin.posts') && $_GET['board_id'] ? 'active' : '' ?>">
                    <a href="#" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-regular fa-list-ul"></i>
                            <span>공용 게시판</span>
                        </div>
                        <button type="button">
                            <i class="fa-regular fa-chevron-down"></i>
                        </button>
                    </a>
                    
                    <?php if ($publicBoards): ?>
                    <div class="no-admin-drawer-subgroup">
                        <ul class="no-admin-drawer-submenu">
                            <?php foreach ($publicBoards as $board) :
                                $lang = DB::table('no_board_langs')
                                    ->where('board_id', '=', $board['id'])
                                    ->where('locale', '=', $locale)
                                    ->first();

                            ?>
                                <li class="no-admin-drawer-submenu__item <?= isRouteLike('admin.posts') && $_GET['board_id'] == $board['id'] ? 'active' : ''?>">
                                    <a href="/admin/posts/?board_id=<?=$board['id']?>&is_public=1" class="no-admin-drawer-submenu__link">
                                        <span><?= htmlspecialchars($lang['name'] ?? '제목 없음') ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (Session::get('role_id') == ROLE_OWNER) :?>
        <div class="no-admin-drawer-group">
            <span class="no-admin-drawer-group__label">권한 관리</span>
            <ul class="no-admin-drawer-menu">
                <li class="no-admin-drawer-menu__item <?= isRouteLike('admin.admin') ?>">
                    <a href="/admin/admins" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-regular fa-user"></i>
                            <span>계정</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="no-admin-drawer-group">
            <span class="no-admin-drawer-group__label">컨텐츠 관리</span>
            <ul class="no-admin-drawer-menu">
                <li class="no-admin-drawer-menu__item <?=isRouteLike('admin.settings.index')?>">
                    <a href="/admin/settings" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-light fa-gear"></i>
                            <span>사이트 정보</span>
                        </div>
                    </a>
                </li>
                <li class="no-admin-drawer-menu__item <?=isRouteLike('admin.banners')?>">
                    <a href="/admin/banners" class="no-admin-drawer-menu__link">
                        <div>
                            <i class="fa-regular fa-image"></i>
                            <span>배너 관리</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </nav>
    <div class="no-admin-drawer-foot">

    </div>
</aside>