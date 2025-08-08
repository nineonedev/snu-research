<?php

use app\facades\DB;
use app\core\Config;

?>

<?php section('content') ?>

<section class="no-sub-visual no-pd-64--b">
    <div class="no-container-xl">
        <nav class="no-sub-visual-nav no-mg-32--b">

            <div class="nsw nsw1">
                <a href="<?=web_path('teams')?>">
                    <div class="top-menu">
                        <p class="no-base-label"><?= lang('menu.teams') ?></p>
                       
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

        <div class="page-nav-list no-mg-32--t">
            <ul class="swiper-wrapper">
                <?php foreach ($teams as $team) :
                    $isActive = $team['is_active'] ? 'active' : '';
                ?>
                    <li class="swiper-slide no-pd-10--y  <?= $isActive ?>">
                        <a href="<?= $team['path'] ?>" class="no-sub-nav__link">
                            <?= $team['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="sub-all no-pd-120--y">
    <div class="no-container-xl">
        <ul class="research-list grid-col-3 no-pd-48--t" <?= AOS_FADE_UP ?>>
            <?php foreach ($teams as $team): ?>
                <li>
                    <a href="<?=web_path('teams/'.$team['id'])?>">
                        <figure>
                            <img src="<?= $team['image'] ? UPLOAD_URL . $team['image'] : img('default.jpg') ?>">
                        </figure>
                        <h3 class="no-mg-16--t no-base-stitle">
                            <?= htmlspecialchars($team['name'] ?? '이름 없음') ?>
                        </h3>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php endSection() ?>
