<?php

use app\core\Config;

$meus = Config::get('menus');

?>

<section class="no-sub-visual no-pd-64--b">
    <div class="no-container-xl">
        <nav class="no-sub-visual-nav no-mg-32--b">

                <div class="nsw nsw1">
                    <a href="#">
                        <div class="top-menu">
                            <p class="no-base-label">depth 1</p>
                            <i class="fa-sharp fa-regular fa-angle-up fa-rotate-90"></i>
                        </div>
                    </a>
                </div>

            <div class="nsw nsw2">
                <a href="#">
                    <div class="top-menu">
                        <p class="no-base-label">depth 2</p>
                    </div>
                </a>
            </div>

                <div class="nsw nsw3">
                    <a href="#">
                        <div class="top-menu">
                            <i class="fa-sharp fa-regular fa-angle-up fa-rotate-90"></i>
                            <p>depth 3</p>
                        </div>
                    </a>
                </div>
        </nav>

        <hgroup>
            <h2 class="no-heading-xl no-mg-16--b">
                title
            </h2>
            <p class="no-body-lg">desc</p>
        </hgroup>

        <div class="page-nav-list no-mg-32--t">
            <ul class="swiper-wrapper">
                <?php foreach (Config::get('menus') as $subIdx => $subPage) :
                    $subActive = $subPage['isActive'] ? 'active' : '';
                ?>
                    <li class="swiper-slide no-pd-10--y  <?= $subActive ?>">
                        <a href="<?= $subPage['path'] ?>" class="no-sub-nav__link">
                            <?= $subPage['title'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>