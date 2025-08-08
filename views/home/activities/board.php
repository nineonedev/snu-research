<?php

use app\core\Config;

 section('content') ?>

<?php 
    $skinClass = '';
    if($board['skin'] === 'bbs') {
        $skinClass = 'sub-list';
    } else if( $board['skin'] === 'mbr') {
        $skinClass = 'sub-member';
    } else {
        $skinClass = 'sub-active';
    }
?>

<form >
<section class="no-sub-visual no-pd-64--b">
        <div class="no-container-xl">
            <nav class="no-sub-visual-nav no-mg-32--b">

                <div class="nsw nsw1">
                    <a href="<?=web_path('activities/'.$board['id'])?>">
                        <div class="top-menu">
                            <p class="no-base-label"><?= $title ?></p>
                            <i class="fa-sharp fa-regular fa-angle-up fa-rotate-90"></i>
                        </div>
                    </a>
                </div>

                <div class="nsw nsw2">
                    <a href="<?=web_path('activities/'.$board['id'])?>">
                        <div class="top-menu">
                            <p class="no-base-label"><?=$board['name']?></p>
                        </div>
                    </a>
                </div>

            </nav>

            <hgroup>
                <h2 class="no-heading-xl no-mg-16--b">
                    <?=$title?>
                </h2>
                <p class="no-body-lg"><?=$subTitle?></p>
            </hgroup>
        </div>
    </section>

    <section class="<?=$skinClass?> no-pd-120--y">
    <div class="no-container-xl">
        <?php if ($boards) : ?>
        <div class="team-nav">
            <ul class="swiper-wrapper">
                <?php foreach ($boards as $b) : ?>

                    <li class="swiper-slide">
                        <a href="<?=$b['path']?>" class="no-base-smenu no-pd-16--y <?= $b['is_active'] ? 'active' : '' ?>">
                        <?= $b['name'] ?>
                        </a>
                    </li>
                <?php  endforeach;?>
            </ul>
        </div>
        <div class="no-pd-36--t"></div>
        <?php endif; ?>
        <div class="opt_top">
            <ul class="num_info">
                <li><b class="no-body-lg"><?=$total?></b> results</li>
            </ul>

            <div class="search-box">
                <input type="search" name="search" placeholder="Search" value="<?=$search?>" class="no-body-md">
                <button type="submit">
                    <i class="fa-light fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        <?php if($board['skin'] === 'mbr')  : ?>
        <ul class="member-list no-pd-48--t" <?=AOS_FADE_UP?>>
            <?php foreach ($posts as $post) : ?>
            <li>
                <figure>
                    <img src="<?= $post['image'] ? UPLOAD_URL.$post['image'] : img('default.jpg')?>">
                </figure>

                <div class="txt">
                    <h3 class="no-heading-xs no-mg-24--b"><?=$post['lang']['title']?></h3>
                    <p>
                        <div>
                            <?= htmlspecialchars_decode($post['lang']['content'])?>
                        </div>
                    </p>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php elseif ($board['skin'] == 'pub') : ?>
        <ul class="public-list no-pd-48--t no-pd-24--b grid-col-4" <?= AOS_FADE_UP ?>>
            <?php foreach ($posts as $post) : ?>
            <li>
                <a href="<?=$post['path']?>">
                    <figure class="no-pub-img">
                    <img src="<?= $post['image'] ? UPLOAD_URL.$post['image'] : img('default.jpg')?>">
                    </figure>
                    <h3 class="no-body-xl no-mg-12--b no-mg-16--t"><?=$post['lang']['title']?></h3>
                    <span class="no-body-sm"><?=date('Y-m-d', strtotime($post['created_at']))?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php elseif ($board['skin'] == 'vid') : ?>
        <ul class="video-list no-pd-48--t no-pd-24--b grid-col-4" <?= AOS_FADE_UP ?>>
            <?php foreach ($posts as $post) : ?>
            <li>
                <a href="<?=$post['link_url'] ?: '#'?>" target="<?=$post['link_url'] ? '_blank' : '_self'?>">
                    <figure class="no-vid-img">
                        <img src="<?= $post['image'] ? UPLOAD_URL.$post['image'] : img('default.jpg')?>">
                        <img src="<?=img('youtube.svg')?>" alt="" class="icon">
                    </figure>
                    <h3 class="no-body-xl no-mg-12--b no-mg-16--t"><?=$post['lang']['title']?></h3>
                    <span class="no-body-sm"><?=date('Y-m-d', strtotime($post['created_at']))?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php elseif($board['skin'] === 'bbs'): ?>
        <div class="no-board">
            <div class="no-skin-list">
                <div class="no-skin-list-table-container">
                    <table class="no-skin-list-table">
                        <colgroup>
                            <col style="width: 8%;">
                            <col style="width: 64%">
                            <col style="width: 12%;">
                            <col style="width: 16%;">
                        </colgroup>
                        <thead>
                            <tr class="no-body-lg --fw-semibold">
                                <th><?=lang('board.no')?></th>
                                <th class="--tal"><?=lang('board.title')?></th>
                                <th><?=lang('board.views')?></th>
                                <th><?=lang('board.date')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post) : ?>
                            <tr>
                                <td>
                                    <?php if($post['is_notice']): ?>
                                    <span class="no-notice-megaphone">
                                        <i class="fa-solid fa-megaphone"></i>
                                    </span> 
                                    <?php else: ?>
                                    <span class="no-clr-text-label"><?=$post['_no']?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="--tal --full">
                                    <a href="<?=$post['path']?>" class="no-clr-text-title no-body-lg --fw-semibold">
                                        <?php 
                                            $createdAt = new DateTime($post['created_at']); 
                                            $now = new DateTime(); // 현재 시간
                                            $interval = $now->diff($createdAt); 
                                            $isNew = $interval->days <= 14; 
                                        ?>
                                        <?php if($isNew) : ?>
                                        <!-- <div class="no-skin-new">
                                            <span>N</span>
                                        </div> -->
                                        <?php endif; ?>
                                        <strong>
                                        <?=$post['lang']['title']?>
                                        </strong>
                                    </a>
                                </td>
                                <td class="no-skin-list-table__label">
                                    <span class="no-clr-text-label" data-label="<?=lang('board.views')?>">
                                    <?=$post['views']?>
                                    </span>
                                </td>
                                <td class="no-skin-list-table__label">
                                    <span class="no-clr-text-label" data-label="<?=lang('board.date')?>">
                                    <?=date('Y-m-d', strtotime($post['created_at']))?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else :?>

        <ul class="active-list no-pd-48--t" <?= AOS_FADE_UP ?>>
            <?php foreach ($posts as $post) : ?>
            <li>
                <a href="<?=$post['path']?>">
                    <figure>
                        <img src="<?= $post['image'] ? UPLOAD_URL.$post['image'] : img('default.jpg')?>">
                    </figure>

                    <div class="txt no-pd-24--y">
                        <h3 class="no-heading-xs no-mg-8--b"><?=$post['lang']['title']?></h3>
                        <p class="no-body-lg no-mg-16--b">
                            <?= htmlspecialchars(mb_strimwidth(strip_tags($post['lang']['content'] ?? ''), 0, 200, '...'))?>
                        </p>
                        <span class="no-body-lg"><?= date("Y-m-d", strtotime($post['created_at'])) ?></span>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php endif; ?>

        <div class="no-mg-64--t">
            <?= $pagination ?>
        </div>
    </div>

</section>
</form>

<?php endSection() ?>
