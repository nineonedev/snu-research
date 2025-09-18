<?php

use app\core\Config;

 section('content') ?>

<section class="no-sub-visual no-pd-64--b">
    <div class="no-container-xl">
        <nav class="no-sub-visual-nav no-mg-32--b">

            <div class="nsw nsw1">
                <a href="<?=web_path('teams')?>">
                    <div class="top-menu">
                        <p class="no-base-label"><?= lang('menu.teams') ?></p>
                        <i class="fa-sharp fa-regular fa-angle-up fa-rotate-90"></i>
                    </div>
                </a>
            </div>

            <div class="nsw nsw2">
                <a href="<?=web_path('teams/'.$team['id'])?>">
                    <div class="top-menu">
                        <p class="no-base-label"><?=$team['name']?></p>
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

        <div class="no-sub-nav no-mg-32--t">
            <ul class="no-sub-nav__list">
                <?php foreach ($teams as $team) :
                    $isActive = $team['is_active'] ? 'active' : '';
                ?>
                    <li class="no-sub-nav__item no-pd-10--y  <?= $isActive ?>">
                        <a href="<?= $team['path'] ?>" class="no-sub-nav__link">
                            <?= $team['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>


<section class="sub-intro">
    <div class="no-container-xl">
        <div class="team-nav no-mg-64--t">
            <ul class="swiper-wrapper">
                <?php foreach ($boards as $board) : ?>

                    <li class="swiper-slide">
                        <a href="<?=$board['path']?>" class="no-base-smenu no-pd-16--y <?= $board['is_active'] ? 'active' : '' ?>">
                        <?= $board['name'] ?>
                        </a>
                    </li>
                <?php  endforeach;?>
            </ul>
        </div>


        <div class="intro-wrap f-wrap" <?= AOS_FADE_UP?>>
            <figure>
                <img src="<?= $post['image'] ? UPLOAD_URL. '/' . ltrim($post['image'], '/'): img('default.jpg')?>">
            </figure>
            <div class="txt">
                <h2 class="no-heading-sm no-mg-24--b"></h2>
                <h3 class="no-mg-48--b"><?=$post['lang']['title']?></h3>
                <div class="txt-box no-editor">
					<?php
						$content = preg_replace('/\x{FEFF}/u', '', $post['lang']['content']);
						echo htmlspecialchars_decode($content);
					?>
                </div>

				<div class="no-post-files">
				<?php for ($i = 1; $i <= 10; $i++) : ?>
						<?php if($post['lang']["image$i"]): ?>
							<a href="/storage/uploads/<?=ltrim($post['lang']["image$i"], '/')?>" class="no-post-file" target="_blank">첨부파일 <?=$i?></a>
						<?php endif; ?>
					<?php endfor; ?>
				</div>

            </div>
        </div>
    </div>
</section>
<?php endSection() ?>
