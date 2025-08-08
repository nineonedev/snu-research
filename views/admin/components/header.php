<?php

use app\core\Session;

?>

<header class="no-admin-header">
    <div class="no-container-xl">
        <div class="no-admin-header-inner">
            <div class="no-admin-header-left">
                <div class="no-hambuger">
                    <button type="button" id="menu-open-btn">
                        <i class="fa-regular fa-bars"></i>
                    </button>
                </div>
            </div>
            <div class="no-admin-header-right">
                <?php if(Session::get('user_id')) : ?>
                    <form method="post" action="/auth/logout">
                        <button type="submit" class="no-direct-link">
                            <span>로그아웃</span>
                        </button>
                    </form>
                <?php else : ?>
                    <a href="/admin/signin" class="no-direct-link">
                        <span>로그인</span>
                    </a>
                <?php endif; ?>
                <a href="/" class="no-direct-link" target="_blank">
                    <span>홈페이지 바로가기</span>
                </a>
                <a href="/admin/admins/edit/<?=Session::get('user_id')?>" class="no-btn-primary-outline" target="_blank">
                    <span>내정보</span>
                </a>
            </div>
        </div>
    </div>
</header>